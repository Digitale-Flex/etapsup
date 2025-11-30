<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\Settings;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $modelLabel = 'Pays';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $activeNavigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('Publier')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->label('Nom')
                    ->placeholder('Nom du pays')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nom'),
                Tables\Columns\TextColumn::make('regions_count')
                    ->counts('regions')
                    ->label('Regions')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Publier')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])->defaultSort('regions_count', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            Settings\CountryResource\RelationManagers\RegionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Settings\CountryResource\Pages\ListCountries::route('/'),
            // 'create' => Pages\CreateCountry::route('/create'),
            'edit' => Settings\CountryResource\Pages\EditCountry::route('/{record}/edit'),

            //  'cities' => Pages\ManageCity::route('/{record}/cities'),
            // 'cities.create' => Pages\CreateNewCity::route('/{record}/cities/create'),
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
