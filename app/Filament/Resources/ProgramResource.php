<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Models\Program;
use App\Models\RealEstate\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $modelLabel = 'Programme d\'études';

    protected static ?string $pluralLabel = 'Programmes d\'études';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations principales')
                    ->description('Détails du programme d\'études')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Select::make('establishment_id')
                            ->relationship('establishment', 'title')
                            ->label('Établissement')
                            ->helperText('Sélectionnez l\'établissement proposant ce programme')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(6),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Publier le programme')
                            ->helperText('Le programme sera visible sur le site')
                            ->default(false)
                            ->columnSpan(6),

                        Forms\Components\TextInput::make('name')
                            ->label('Nom du programme')
                            ->helperText('Exemple: Master en Finance Internationale')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('study_field_id')
                            ->relationship('studyField', 'label')
                            ->label('Domaine d\'études')
                            ->helperText('Domaine principal du programme')
                            ->searchable()
                            ->preload()
                            ->columnSpan(6),

                        Forms\Components\Select::make('specialization_id')
                            ->relationship('specialization', 'label')
                            ->label('Spécialisation')
                            ->helperText('Spécialisation ou filière spécifique')
                            ->searchable()
                            ->preload()
                            ->columnSpan(6),

                        Forms\Components\RichEditor::make('description')
                            ->label('Description détaillée')
                            ->helperText('Décrivez le contenu, les objectifs et les débouchés du programme')
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                            ])
                            ->columnSpanFull(),
                    ])->columns(12),

                Forms\Components\Section::make('Détails académiques')
                    ->description('Informations sur la durée, le niveau et la langue')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Forms\Components\Select::make('degree_level_id')
                            ->relationship('degreeLevel', 'label')
                            ->label('Niveau de diplôme')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(4),

                        Forms\Components\TextInput::make('duration')
                            ->label('Durée du programme')
                            ->helperText('Exemple: 2 ans, 18 mois, 3 années')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(4),

                        Forms\Components\Select::make('language')
                            ->label('Langue d\'enseignement')
                            ->options([
                                'Français' => 'Français',
                                'Anglais' => 'Anglais',
                                'Bilingue (FR/EN)' => 'Bilingue (FR/EN)',
                                'Espagnol' => 'Espagnol',
                                'Allemand' => 'Allemand',
                                'Italien' => 'Italien',
                                'Chinois' => 'Chinois',
                                'Arabe' => 'Arabe',
                                'Autre' => 'Autre',
                            ])
                            ->searchable()
                            ->columnSpan(4),
                    ])->columns(12)->collapsible(),

                Forms\Components\Section::make('Frais de scolarité')
                    ->description('Coût du programme')
                    ->icon('heroicon-o-currency-euro')
                    ->schema([
                        Forms\Components\TextInput::make('tuition_fee')
                            ->label('Frais de scolarité annuels')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->helperText('Montant total des frais de scolarité par an')
                            ->columnSpanFull(),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom du programme')
                    ->description(fn (Program $record): string => $record->establishment->title ?? '')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('establishment.title')
                    ->label('Établissement')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('studyField.label')
                    ->label('Domaine')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('degreeLevel.label')
                    ->label('Niveau')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Doctorat' => 'danger',
                        'Mastères' => 'success',
                        'Licence' => 'info',
                        'BTS' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('language')
                    ->label('Langue')
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('tuition_fee')
                    ->label('Frais')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('establishment_id')
                    ->label('Établissement')
                    ->relationship('establishment', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('study_field_id')
                    ->label('Domaine d\'études')
                    ->relationship('studyField', 'label')
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
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
