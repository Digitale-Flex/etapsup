<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings;
use App\Models\City;
use App\Models\Location;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $activeNavigationIcon = 'heroicon-o-check-circle';

    protected static ?string $modelLabel = 'Localités';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('Publier')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Prix')
                    ->numeric()
                    ->suffix('€')
                    ->required(),
                Forms\Components\MorphToSelect::make('locatable')
                    ->label('Ville ou Region')
                    ->types([
                        Forms\Components\MorphToSelect\Type::make(Region::class)->titleAttribute('name'),
                        Forms\Components\MorphToSelect\Type::make(City::class)->titleAttribute('name'),
                    ])
                    ->columns(2)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                TableRepeater::make('addresses')
                    ->label('Adresses')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Adresse')
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('place')
                            ->numeric()
                            ->label('Nombre de Place')
                            ->required(),
                    ])
                    ->addActionLabel('Ajouter une adresse')
                    ->required()
                    ->colStyles([
                        'place' => 'font-size: 14px; font-weight: 400; width: 20%; padding: 6px',
                        'address' => 'font-size: 14px; font-weight: 400; padding: 6px',
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('locatable_type'),
                Tables\Columns\TextColumn::make('locatable.name')
                    ->label('Localité'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->suffix(' €'),
                Tables\Columns\TextColumn::make('addresses')
                    ->label('Adresses')
                    ->getStateUsing(function (Location $record): string {
                        return collect($record->addresses)->map(function ($address) {
                            $addressText = $address['address'];
                            if (isset($address['place'])) {
                                $addressText .= " => {$address['place']}";
                            }

                            return $addressText;
                        })->implode("\n");
                    })
                    ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                    ->html()
                    ->sortable()
                    ->searchable()
                    ->lineClamp(3),
                Tables\Columns\TextColumn::make('address_count')
                    ->label(''),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publier')
                    ->getStateUsing(function (Location $record) {
                        return $record->address_count;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalIcon('heroicon-o-pencil')
                    ->modalWidth('xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Settings\LocationResource\Pages\ListLocations::route('/'),
            'create' => Settings\LocationResource\Pages\CreateLocation::route('/create'),
            'edit' => Settings\LocationResource\Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
