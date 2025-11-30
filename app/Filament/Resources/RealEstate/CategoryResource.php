<?php

namespace App\Filament\Resources\RealEstate;

use App\Filament\Resources\RealEstate\CategoryResource\Pages;
use App\Models\RealEstate\Category;
use App\Models\Scopes\PublishedScope;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $modelLabel = 'Domaine d\'études';

    protected static ?string $pluralLabel = 'Domaines d\'études';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('Publier le domaine')
                    ->helperText('Le domaine sera visible dans les filtres')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('label')
                    ->label('Nom du domaine d\'études')
                    ->placeholder('Ex: Sciences, Commerce, Ingénierie...')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label('Description du domaine')
                    ->helperText('Décrivez les formations et débours de ce domaine')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Domaine d\'études')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('properties_count')
                    ->label('Établissements')
                    ->counts('properties')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->alignCenter()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListCategories::route('/'),
            // 'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
                PublishedScope::class,
            ]);
    }
}
