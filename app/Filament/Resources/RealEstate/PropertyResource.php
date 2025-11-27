<?php

namespace App\Filament\Resources\RealEstate;

use App\Filament\Resources\RealEstate\PropertyResource\Pages;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Property;
use App\Models\Scopes\PublishedScope;
use App\Settings\RealEstateSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $modelLabel = 'Logement';

    protected static ?string $pluralLabel = 'Logements';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        $settings = app(RealEstateSettings::class);
        $rentalMonthlyBillingId = $settings->rental_monthly_billing;

        return $form
            ->schema([
                Forms\Components\Grid::make(6)
                    ->schema([
                        Forms\Components\Section::make('Informations principales')
                            ->description('Détails essentiels de la propriété')
                            ->icon('heroicon-o-home')
                            ->columns(12)
                            ->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Publier la propriété')
                                    ->helperText('La propriété sera visible sur le site')
                                    ->default(false)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(100)
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('property_type_id')
                                    ->relationship('propertyType', 'label')
                                    ->label('Type de propriété')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('label')
                                            ->required(),
                                        Forms\Components\TextInput::make('description'),
                                    ])->columnSpan(6),

                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'label')
                                    ->label('Catégorie')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('label')
                                            ->required(),
                                        Forms\Components\TextInput::make('description'),
                                    ])
                                    ->live()
                                    ->columnSpan(6),

                                Forms\Components\Select::make('sub_category_id')
                                    ->relationship('subCategory', 'label')
                                    ->label('Sous catégorie')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(6),

                                Forms\Components\Select::make('city_id')
                                    ->relationship('city', 'name')
                                    ->label('Ville')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(6),

                                Forms\Components\TextInput::make('price')
                                    ->label('Prix')
                                    ->numeric()
                                    ->required()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('cleaning_fees')
                                    ->label('Frais de ménage')
                                    ->numeric()
                                    ->required()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->columnSpan(3),

                                Forms\Components\RichEditor::make('description')
                                    ->required()
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                        'codeBlock',
                                        'link',
                                        'code',
                                    ])
                                    ->columnSpanFull(),
                            ])->columnSpan(4),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Section::make('Caractéristiques')
                                    ->icon('heroicon-o-squares-2x2')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('room')
                                                    ->numeric()
                                                    ->label('Chambres')
                                                    ->suffix('pièce(s)')
                                                    ->minValue(0)
                                                    ->maxValue(20),

                                                Forms\Components\TextInput::make('bathroom')
                                                    ->numeric()
                                                    ->label('Salles de bain')
                                                    ->suffix('pièce(s)')
                                                    ->minValue(0)
                                                    ->maxValue(10),

                                                Forms\Components\TextInput::make('dining_room')
                                                    ->numeric()
                                                    ->label('Salles à manger')
                                                    ->suffix('pièce(s)')
                                                    ->minValue(0)
                                                    ->maxValue(5),

                                                Forms\Components\TextInput::make('kitchen')
                                                    ->numeric()
                                                    ->label('Cuisines')
                                                    ->suffix('pièce(s)')
                                                    ->minValue(0)
                                                    ->maxValue(5),

                                                Forms\Components\TextInput::make('living_room')
                                                    ->numeric()
                                                    ->label('Salons')
                                                    ->suffix('pièce(s)')
                                                    ->minValue(0)
                                                    ->maxValue(5),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Toggle::make('balcony')
                                                    ->label('Balcon')
                                                    ->inline(false),

                                                Forms\Components\Toggle::make('outdoor_space')
                                                    ->label('Espace extérieur')
                                                    ->inline(false),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Localisation')
                                    ->icon('heroicon-o-map-pin')
                                    ->schema([
                                        Forms\Components\Textarea::make('address')
                                            ->label('Adresse complète')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('airbnb')
                                            ->label('Lien airbnb')
                                            ->columnSpanFull(),
                                    ])->collapsible(),
                            ])->columnSpan(2),

                    ]),

                Forms\Components\Section::make('Équipements et Règlements')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('equipments')
                                    ->relationship('equipments', 'label')
                                    ->label('Equipements')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Select::make('regulations')
                                    ->relationship('regulations', 'label')
                                    ->label('Règlements intérieur')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Select::make('layouts')
                                    ->relationship('layouts', 'label')
                                    ->label('Amenagements')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\RichEditor::make('regulation')
                                    ->label('Autres règlements intérieur')
                                    ->required()
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                        'codeBlock',
                                        'link',
                                        'code',
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Médias')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                            ->collection('images')
                            ->multiple()
                            ->maxFiles(30)
                            ->downloadable()
                            ->reorderable()
                            ->imageEditor()
                            ->responsiveImages()
                            ->maxSize(10024)
                            ->panelLayout('grid')
                            ->acceptedFileTypes(['image/*'])
                            ->columnSpanFull(),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('images')
                    ->grow(false)
                    ->collection('images')
                    ->conversion('thumb')
                    ->circular()
                    ->stacked()
                    ->ring(10)
                    ->limit(3),
                Tables\Columns\TextColumn::make('title')
                    ->description(fn(Property $record): string => $record->propertyType->label)
                    ->label('Titre')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ville')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->label('Prix')
                    ->sortable(),
                Tables\Columns\TextColumn::make('room')
                    ->label('Chambres')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('bathroom')
                    ->label('Salles de bain')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dining_room')
                    ->label('Salles à manger')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kitchen')
                    ->label('Cuisine')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('living_room')
                    ->label('Salon')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('balcony')
                    ->label('Balcon')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('outdoor_space')
                    ->label('E. extérieur')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publier')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ajouter le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->hidden()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->hidden()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                /* ...Category::all()->map(function (Category $category) {
                     return Tables\Filters\Filter::make($category->label)
                         ->query(fn(Builder $query) => $query->where('category_id', $category->id))
                         ->label($category->label);
             })->toArray()*/
            ])
            //->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
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
}
