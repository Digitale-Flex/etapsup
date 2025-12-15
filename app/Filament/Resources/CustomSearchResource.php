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

/**
 * Ressource pour les demandes d'accompagnement personnalisées
 * Adapté de Mareza vers EtapSup
 */
class CustomSearchResource extends Resource
{
    protected static ?string $model = CustomSearch::class;

    protected static ?string $modelLabel = 'Demande d\'accompagnement';

    protected static ?string $pluralLabel = 'Demandes d\'accompagnement';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationGroup = 'Gestion des Candidatures';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Étudiant')
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'label')
                    ->label('Domaine d\'études')
                    ->required(),
                Forms\Components\Select::make('city_id')
                    ->relationship('city', 'name')
                    ->label('Ville souhaitée')
                    ->required(),
                Forms\Components\Select::make('partner_id')
                    ->relationship('partner', 'label')
                    ->label('Établissement partenaire'),
                Forms\Components\Select::make('coupon_id')
                    ->relationship('coupon', 'code')
                    ->label('Code promo'),
                Forms\Components\TextInput::make('budget')
                    ->label('Budget scolarité')
                    ->numeric()
                    ->prefix('€')
                    ->required(),
                Forms\Components\DatePicker::make('rental_start')
                    ->label('Rentrée souhaitée')
                    ->required(),
                Forms\Components\TextInput::make('duration')
                    ->label('Durée du programme (mois)')
                    ->numeric(),
                Forms\Components\Textarea::make('note')
                    ->label('Notes et besoins spécifiques')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('stripe_payment_intent')
                    ->label('ID Paiement Stripe')
                    ->disabled(),
                Forms\Components\TextInput::make('paid')
                    ->label('Montant payé')
                    ->numeric()
                    ->prefix('€')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->description(fn(CustomSearch $record): string => $record->user?->email ?? '')
                    ->label('Étudiant')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ville')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.label')
                    ->label('Domaine d\'études')
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
                    ->label('Rentrée')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée (mois)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date demande')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'label')
                    ->label('Domaine d\'études'),

                Tables\Filters\SelectFilter::make('partner')
                    ->relationship('partner', 'label')
                    ->label('Établissement partenaire')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('has_partner')
                    ->label('Avec établissement')
                    ->nullable()
                    ->options([
                        true => 'Avec établissement',
                        false => 'Sans établissement',
                    ])
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('partner_id'),
                        false: fn(Builder $query) => $query->whereNull('partner_id'),
                        blank: fn(Builder $query) => $query,
                    ),

                Tables\Filters\Filter::make('rental_start')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Rentrée à partir du'),
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
                Components\Section::make('Informations de l\'étudiant')
                    ->icon('heroicon-o-user')
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
                                    ->label('N° passeport'),
                            ])
                    ])
                    ->columnSpan(2),

                Components\Section::make('Partenaire & Coupon')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('partner.label')
                                    ->label('Établissement partenaire')
                                    ->badge()
                                    ->color('info'),

                                Components\TextEntry::make('coupon.code')
                                    ->label('Code promo')
                                    ->badge()
                                    ->color('success'),
                            ]),

                        Components\TextEntry::make('coupon.discount_amount')
                            ->label('Réduction')
                            ->money('EUR')
                            ->color('danger'),

                        Components\TextEntry::make('paid')
                            ->label('Montant payé')
                            ->money('EUR')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                    ])
                    ->columnSpan(1),

                Components\Section::make('Critères de recherche')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('category.label')
                                    ->label('Domaine d\'études')
                                    ->badge()
                                    ->color('primary'),

                                Components\TextEntry::make('city.name')
                                    ->label('Ville souhaitée'),

                                Components\TextEntry::make('budget')
                                    ->label('Budget scolarité')
                                    ->money('EUR')
                                    ->color('success'),
                            ]),

                        Components\Grid::make(3)
                            ->columns(3)
                            ->schema([
                                Components\TextEntry::make('rental_start')
                                    ->label('Rentrée souhaitée')
                                    ->date('d/m/Y')
                                    ->badge()
                                    ->color('warning'),
                                Components\TextEntry::make('duration')
                                    ->label('Durée du programme')
                                    ->suffix(' mois'),
                            ]),

                        Components\TextEntry::make('note')
                            ->label('Notes et besoins spécifiques')
                            ->columnSpanFull()
                            ->markdown(),

                    ])
                    ->columnSpan(2),

                Components\Section::make('Paiement')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Components\TextEntry::make('stripe_payment_intent')
                            ->label('ID Paiement Stripe')
                            ->copyable()
                            ->badge(),

                        Components\TextEntry::make('created_at')
                            ->label('Date de la demande')
                            ->dateTime('d/m/Y H:i')
                            ->color('gray'),
                    ])
                    ->columnSpan(1),


                Components\Section::make('Préférences')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('rentalDeposits.name')
                                    ->label('Frais de dossier')
                                    ->badge()
                                    ->color('gray'),

                                Components\TextEntry::make('propertyTypes.label')
                                    ->label('Types d\'établissement')
                                    ->badge()
                                    ->color('gray'),
                            ]),
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
        return false; // Demandes créées par les étudiants côté frontend
    }

    public static function canEdit(Model $record): bool
    {
        return true; // Permettre l'édition pour le suivi
    }

    public static function getNavigationLabel(): string
    {
        return "Demandes d'accompagnement";
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true; // Activé pour EtapSup
    }
}
