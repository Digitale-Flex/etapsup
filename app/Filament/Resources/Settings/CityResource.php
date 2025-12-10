<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $modelLabel = 'Ville';

    protected static ?string $pluralLabel = 'Villes';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->columns(4)
                    ->schema([
                        Forms\Components\Section::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->label('Pays')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Ville')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('budget')
                                    ->label('Budget')
                                    ->placeholder('Budget minimum pour faire une demande')
                                    ->suffix('€')
                                    ->numeric()
                                    ->required()
                                    ->maxLength(255),
                            ])->columnSpan(2),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Publier')
                                    ->required()
                                    ->columnSpanFull(),
                            ])->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->label('Pays')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ville')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('budget')
                    ->label('Budget')
                    ->money('EUR')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('addresses.street')
                    ->label('Adresses')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->limit(60)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('certificate_requests_count')
                    ->counts('certificateRequests')
                    ->label('Demandes')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publier')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('md'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            Settings\CityResource\RelationManagers\AddressesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Settings\CityResource\Pages\ListCities::route('/'),
            'create' => Settings\CityResource\Pages\CreateCity::route('/create'),
            'edit' => Settings\CityResource\Pages\EditCity::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
