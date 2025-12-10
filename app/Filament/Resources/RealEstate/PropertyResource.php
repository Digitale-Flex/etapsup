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

    protected static ?string $modelLabel = 'Établissement';

    protected static ?string $pluralLabel = 'Établissements';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Gestion des Établissements';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $settings = app(RealEstateSettings::class);
        $rentalMonthlyBillingId = $settings->rental_monthly_billing;

        // Sprint1 Feature 1.5.1 — Restructuration en Tabs (navigation onglets)
        return $form
            ->schema([
                Forms\Components\Tabs::make('Gestion Établissement')
                    ->tabs([
                        // TAB 1 : Informations principales
                        Forms\Components\Tabs\Tab::make('Informations principales')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Publier l\'établissement')
                                    ->helperText('L\'établissement sera visible sur le site')
                                    ->default(false)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('title')
                                    ->label('Nom de l\'établissement')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(100)
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('property_type_id')
                                    ->relationship('propertyType', 'label')
                                    ->label('Type d\'établissement')
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
                                    ->label('Domaine d\'études principal')
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
                                    ->label('Spécialisation')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(6),

                                Forms\Components\Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->label('Pays')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live() // Met à jour city_id quand le pays change
                                    ->columnSpan(6),

                                Forms\Components\Select::make('city_id')
                                    ->relationship(
                                        'city',
                                        'name',
                                        fn ($query, Forms\Get $get) => $query->when(
                                            $get('country_id'),
                                            fn ($q, $countryId) => $q->where('country_id', $countryId)
                                        )
                                    )
                                    ->label('Ville')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(6),

                                Forms\Components\Textarea::make('address')
                                    ->label('Adresse complète')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('price')
                                    ->label('Frais de scolarité annuels')
                                    ->numeric()
                                    ->required()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->columnSpan(6),

                                Forms\Components\RichEditor::make('description')
                                    ->required()
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                        'codeBlock',
                                        'link',
                                        'code',
                                    ])
                                    ->columnSpanFull(),
                            ])->columns(12),

                        // TAB 2 : Contact & Tarifs
                        Forms\Components\Tabs\Tab::make('Contact & Tarifs')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Section::make('Informations de contact')
                                    ->icon('heroicon-o-phone')
                                    ->columns(12)
                                    ->schema([
                                        Forms\Components\TextInput::make('website')
                                            ->label('Site web')
                                            ->url()
                                            ->columnSpan(12),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('Téléphone')
                                            ->tel()
                                            ->columnSpan(6),

                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->columnSpan(6),

                                        Forms\Components\TextInput::make('student_count')
                                            ->label('Nombre d\'étudiants')
                                            ->numeric()
                                            ->columnSpan(6),

                                        Forms\Components\TextInput::make('ranking')
                                            ->label('Classement')
                                            ->numeric()
                                            ->columnSpan(6),
                                    ]),

                                Forms\Components\Section::make('Tarification')
                                    ->icon('heroicon-o-currency-euro')
                                    ->columns(12)
                                    ->schema([
                                        Forms\Components\TextInput::make('tuition_min')
                                            ->label('Frais de scolarité minimum')
                                            ->helperText('Tarif le plus bas proposé par l\'établissement')
                                            ->numeric()
                                            ->prefix('€')
                                            ->step(0.01)
                                            ->columnSpan(6),

                                        Forms\Components\TextInput::make('tuition_max')
                                            ->label('Frais de scolarité maximum')
                                            ->helperText('Tarif le plus élevé proposé par l\'établissement')
                                            ->numeric()
                                            ->prefix('€')
                                            ->step(0.01)
                                            ->columnSpan(6),

                                        Forms\Components\TextInput::make('commission')
                                            ->label('Commission EtapSup (%)')
                                            ->helperText('Commission sur les candidatures (ex: 15 pour 15%)')
                                            ->numeric()
                                            ->suffix('%')
                                            ->step(0.01)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->columnSpan(4),

                                        Forms\Components\TextInput::make('frais_dossier')
                                            ->label('Frais de dossier')
                                            ->helperText('Frais de traitement du dossier de candidature')
                                            ->numeric()
                                            ->prefix('€')
                                            ->step(0.01)
                                            ->minValue(0)
                                            ->columnSpan(4),

                                        Forms\Components\TextInput::make('acompte_scolarite')
                                            ->label('Acompte de scolarité')
                                            ->helperText('Montant de l\'acompte à verser lors de l\'inscription')
                                            ->numeric()
                                            ->prefix('€')
                                            ->step(0.01)
                                            ->minValue(0)
                                            ->columnSpan(4),
                                    ]),
                            ])->columns(1),

                        // TAB 3 : Médias
                        Forms\Components\Tabs\Tab::make('Médias')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('Images de l\'établissement')
                                    ->description('Ajoutez des photos de l\'établissement, campus, infrastructures')
                                    ->icon('heroicon-o-camera')
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
                                    ]),
                            ]),

                        // TAB 4 : Filtres & Classification
                        Forms\Components\Tabs\Tab::make('Filtres & Classification')
                            ->icon('heroicon-o-funnel')
                            ->schema([
                                Forms\Components\Section::make('Critères de recherche')
                                    ->description('Paramètres utilisés pour filtrer et rechercher l\'établissement')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->columns(12)
                                    ->schema([
                                        Forms\Components\Select::make('establishment_type_id')
                                            ->relationship('establishmentType', 'label')
                                            ->label('Type d\'établissement')
                                            ->helperText('Catégorie de l\'établissement (Université, École de commerce, etc.)')
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(6),

                                        Forms\Components\Select::make('training_type_id')
                                            ->relationship('trainingType', 'label')
                                            ->label('Type de formation')
                                            ->helperText('Mode de formation proposé (Initiale, Alternance, etc.)')
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(6),

                                        Forms\Components\Select::make('career_field_id')
                                            ->relationship('careerField', 'label')
                                            ->label('Secteur professionnel')
                                            ->helperText('Domaine de carrière visé (Commerce, Ingénieurs, etc.)')
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(6),

                                        Forms\Components\Select::make('degree_level_id')
                                            ->relationship('degreeLevel', 'label')
                                            ->label('Niveau de diplôme')
                                            ->helperText('Niveau de qualification délivré (Licence, Master, etc.)')
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(6),
                                    ]),
                            ]),

                        // TAB 5 : Sections détaillées
                        Forms\Components\Tabs\Tab::make('Sections détaillées')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Contenu de la fiche établissement')
                                    ->description('Informations affichées sur la page publique de l\'établissement')
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->schema([
                                        Forms\Components\Textarea::make('section_presentation')
                                            ->label('Présentation complète')
                                            ->helperText('Décrivez l\'établissement en détail (max 1000 caractères)')
                                            ->maxLength(1000)
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('section_prerequis')
                                            ->label('Prérequis d\'admission')
                                            ->helperText('Diplômes requis, niveau d\'études, notes minimales, etc. (max 1000 caractères)')
                                            ->maxLength(1000)
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('section_conditions_financieres')
                                            ->label('Conditions financières')
                                            ->helperText('Modalités de paiement, bourses disponibles, aides financières (max 1000 caractères)')
                                            ->maxLength(1000)
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('section_specialisation')
                                            ->label('Spécialisations proposées')
                                            ->helperText('Filières, programmes, diplômes disponibles (max 1000 caractères)')
                                            ->maxLength(1000)
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('section_campus')
                                            ->label('Informations campus')
                                            ->helperText('Adresse, infrastructures, équipements (max 1000 caractères)')
                                            ->maxLength(1000)
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
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
                    ->label('Frais de scolarité')
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_count')
                    ->label('Étudiants')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ranking')
                    ->label('Classement')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('establishmentType.label')
                    ->label('Type')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('trainingType.label')
                    ->label('Formation')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('careerField.label')
                    ->label('Secteur')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('degreeLevel.label')
                    ->label('Niveau')
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('property_type_id')
                    ->label('Type d\'établissement')
                    ->relationship('propertyType', 'label')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Domaine d\'études')
                    ->relationship('category', 'label')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('city_id')
                    ->label('Ville')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('establishment_type_id')
                    ->relationship('establishmentType', 'label')
                    ->label('Type établissement (détail)')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('training_type_id')
                    ->relationship('trainingType', 'label')
                    ->label('Type de formation')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('career_field_id')
                    ->relationship('careerField', 'label')
                    ->label('Secteur professionnel')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('degree_level_id')
                    ->relationship('degreeLevel', 'label')
                    ->label('Niveau de diplôme')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('is_published')
                    ->label('Publiés uniquement')
                    ->query(fn (Builder $query) => $query->where('is_published', true))
                    ->toggle(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
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
            PropertyResource\RelationManagers\ApplicationsRelationManager::class,
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
