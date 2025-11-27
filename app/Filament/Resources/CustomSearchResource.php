<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomSearchResource\Pages;
use App\Filament\Resources\CustomSearchResource\RelationManagers;
use App\Models\CustomSearch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomSearchResource extends Resource
{
    protected static ?string $model = CustomSearch::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'id')
                    ->required(),
                Forms\Components\Select::make('city_id')
                    ->relationship('city', 'name')
                    ->required(),
                Forms\Components\Select::make('partner_id')
                    ->relationship('partner', 'id')
                    ->required(),
                Forms\Components\Select::make('coupon_id')
                    ->relationship('coupon', 'id'),
                Forms\Components\TextInput::make('budget')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('rental_start')
                    ->required(),
                Forms\Components\TextInput::make('duration')
                    ->maxLength(255),
                Forms\Components\Textarea::make('note')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('stripe_payment_intent')
                    ->maxLength(255),
                Forms\Components\TextInput::make('paid')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->description(fn(CustomSearch $record): string => $record->user->email)
                    ->label('Demandeur')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ville')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.label')
                    ->label('Catégorie')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('budget')
                    ->label('Budget')
                    ->money('EUR')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('paid')
                    ->label('Payé')
                    ->money('EUR')
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rental_start')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée (mois)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'label')
                    ->label('Filtrer par catégorie'),

                Tables\Filters\SelectFilter::make('partner')
                    ->relationship('partner', 'label')
                    ->label('Partenaire')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('has_partner')
                    ->label('Avec partenaire')
                    ->nullable()
                    ->options([
                        true => 'Avec partenaire',
                        false => 'Sans partenaire',
                    ])
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('partner_id'),
                        false: fn(Builder $query) => $query->whereNull('partner_id'),
                        blank: fn(Builder $query) => $query,
                    ),

                Tables\Filters\Filter::make('rental_start')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Début location à partir du'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('rental_start', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('rental_start', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('city.name')
                    ->label('Par ville')
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label('Par date')
                    ->date()
                    ->collapsible(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informations du demandeur')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('user.full_name')
                                    ->label('Nom complet')
                                    ->weight('bold'),

                                Components\TextEntry::make('user.email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope'),

                                Components\TextEntry::make('user.phone')
                                    ->label('Téléphone')
                                    ->icon('heroicon-o-phone'),
                            ]),

                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('user.nationality')
                                    ->label('Nationalité')
                                    ->badge(),

                                Components\TextEntry::make('user.place_birth')
                                    ->label('Lieu de naissance'),

                                Components\TextEntry::make('user.date_birth')
                                    ->label('Date de naissance')
                                    ->date('d/m/Y'),
                            ]),

                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('user.country.name')
                                    ->label('Pays de résidence')
                                    ->color('primary'),

                                Components\TextEntry::make('user.passport_number')
                                    ->label('N° passport'),
                            ])
                    ])
                    ->columnSpan(2),

                Components\Section::make('Partenaire & Coupon')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('partner.label')
                                    ->label('Partenaire')
                                    // ->url(fn ($record) => $record->partner ? route('filament.admin.resources.partners.view', $record->partner) : null)
                                    ->badge()
                                    ->color('info'),

                                Components\TextEntry::make('coupon.code')
                                    ->label('Code promo')
                                    ->badge()
                                    ->color('success'),
                            ]),

                        Components\TextEntry::make('coupon.discount_amount')
                            ->label('Montant du coupon')
                            ->money('EUR')
                            ->color('danger'),

                        Components\TextEntry::make('paid')
                            ->label('Montant payé')
                            ->money('EUR')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                    ])
                    ->columnSpan(1),

                Components\Section::make('Informations principales')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('category.label')
                                    ->label('Catégorie')
                                    ->color('primary'),

                                Components\TextEntry::make('city.name')
                                    ->label('Ville'),

                                Components\TextEntry::make('budget')
                                    ->label('Budget')
                                    ->money('EUR')
                                    ->color('success'),
                            ]),

                        Components\Grid::make(3)
                            ->columns(3)
                            ->schema([
                                Components\TextEntry::make('rental_start')
                                    ->label('Début de location')
                                    ->date('d/m/Y')
                                    ->color('warning'),
                                Components\TextEntry::make('duration')
                                    ->label('Durée de location')
                                    ->suffix(' mois'),
                            ]),

                        Components\TextEntry::make('note')
                            ->label('Notes')
                            ->columnSpanFull()
                            ->markdown(),

                    ])
                    ->columnSpan(2),

                Components\Section::make('Paiement')
                    ->schema([
                        Components\TextEntry::make('stripe_payment_intent')
                            ->label('ID de paiement Stripe')
                            ->copyable()
                            ->badge(),

                        Components\TextEntry::make('created_at')
                            ->label('Date de création')
                            ->dateTime('d/m/Y H:i')
                            ->color('gray'),
                    ])
                    ->columnSpan(1),


                Components\Section::make('Préférences')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('layouts.label')
                                    ->label('Commodités')
                                    ->badge()
                                    ->color('gray'),

                                Components\TextEntry::make('propertyTypes.label')
                                    ->label('Types de propriétés')
                                    ->badge()
                                    ->color('gray'),
                            ]),

                        Components\TextEntry::make('rentalDeposits.name')
                            ->label('Dépôts de garantie')
                            ->badge()
                            ->color('gray'),
                    ])
                    ->columnSpanFull(),


            ])
            ->columns(3);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomSearches::route('/'),
            'create' => Pages\CreateCustomSearch::route('/create'),
            'view' => Pages\ViewCustomSearch::route('/{record}'),
            'edit' => Pages\EditCustomSearch::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('state', 'payment_validated');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return "Recherche personnalisées";
    }
}
