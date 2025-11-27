<?php

namespace App\Filament\Resources\Certificate;

use App\Filament\Resources\Certificate;
use App\Models\Certificate\Partner;
use App\Models\Scopes\PublishedScope;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $modelLabel = 'Partenaire';

    protected static ?string $pluralModelLabel = 'Partenaires';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $activeNavigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Attestations';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('Publier')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('label')
                    ->label('Raison social')
                    ->placeholder('Nom du partenaire')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('Code promo')
                    ->maxLength(255),
                Forms\Components\Select::make('country_id')
                    ->label('Pays')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('dans quel Pays')
                    ->required(),
                Forms\Components\TextInput::make('city')
                    ->label('Ville')
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('Adresse')
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('managers')
                    ->label('Responsables')
                    ->hint('Personnes à contacter')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom & prénom')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->required(),
                    ])->columns(2)
                    ->collapsible()
                    ->columnSpanFull()
                    ->addActionLabel('Ajouter un responsable'),
                Forms\Components\Textarea::make('note')
                    ->label('Note complémentaires')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Raison social')
                    ->sortable()
                    ->searchable()
                    ->limit(20)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                Tables\Columns\TextColumn::make('managers')
                    ->label('Responsables')
                    ->getStateUsing(function (Partner $record): string {
                        return collect($record->managers)->map(function ($manager) {
                            return "{$manager['name']} : {$manager['phone']}";
                        })->implode("\n");
                    })
                    ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                    ->html()
                    ->sortable()
                    ->searchable()
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('country.name')
                    ->label('Pays')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->sortable()
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                Tables\Columns\TextColumn::make('address')
                    ->label('Adresse')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publier')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ajoute le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Certificate\PartnerResource\Pages\EditPartner::class,
            Certificate\PartnerResource\Pages\ManagePartnerUsers::class,
            Certificate\PartnerResource\Pages\ManagePartnerCoupons::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Certificate\PartnerResource\Pages\ListPartners::route('/'),
            // 'create' => Pages\CreatePartner::route('/create'),
            'view' => Certificate\PartnerResource\Pages\ViewPartner::route('/{record}'),
            'edit' => Certificate\PartnerResource\Pages\EditPartner::route('/{record}/edit'),
            'users' => Certificate\PartnerResource\Pages\ManagePartnerUsers::route('/{record}/users'),
            'coupons' => Certificate\PartnerResource\Pages\ManagePartnerCoupons::route('/{record}/coupons'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
                PublishedScope::class
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return 'Partenaires';
    }
}
