<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings;
use App\Models\Settings\EstablishmentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EstablishmentTypeResource extends Resource
{
    protected static ?string $model = EstablishmentType::class;

    protected static ?string $modelLabel = 'Type d\'établissement';

    protected static ?string $pluralLabel = 'Types d\'établissement';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('Publié')
                    ->helperText('Le type sera visible dans les filtres')
                    ->default(true)
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('label')
                    ->label('Nom du type')
                    ->placeholder('Ex: Universités, Écoles de commerce...')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->helperText('Description optionnelle du type d\'établissement')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
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
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Settings\EstablishmentTypeResource\Pages\ListEstablishmentTypes::route('/'),
            'create' => Settings\EstablishmentTypeResource\Pages\CreateEstablishmentType::route('/create'),
            'edit' => Settings\EstablishmentTypeResource\Pages\EditEstablishmentType::route('/{record}/edit'),
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
