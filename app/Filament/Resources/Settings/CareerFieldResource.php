<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings;
use App\Models\Settings\CareerField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CareerFieldResource extends Resource
{
    protected static ?string $model = CareerField::class;

    protected static ?string $modelLabel = 'Métier';

    protected static ?string $pluralLabel = 'Métiers';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('Publié')
                    ->helperText('Le métier sera visible dans les filtres')
                    ->default(true)
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('label')
                    ->label('Nom du métier')
                    ->placeholder('Ex: Commerce, Ingénieurs, Santé...')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->helperText('Description optionnelle du secteur professionnel')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Métier')
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
            'index' => Settings\CareerFieldResource\Pages\ListCareerFields::route('/'),
            'create' => Settings\CareerFieldResource\Pages\CreateCareerField::route('/create'),
            'edit' => Settings\CareerFieldResource\Pages\EditCareerField::route('/{record}/edit'),
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
