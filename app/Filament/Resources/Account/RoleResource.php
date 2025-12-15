<?php

namespace App\Filament\Resources\Account;

use App\Filament\Resources\Account\RoleResource\Pages;
use App\Filament\Resources\Account\RoleResource\RelationManagers;
use App\Models\Account\PermissionGroup;
use App\Models\Account\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class RoleResource extends Resource
{
    protected static ?string $model = \Spatie\Permission\Models\Role::class;
    protected static ?string $modelLabel = 'Roles & permissions';
    protected static ?string $navigationIcon = 'gmdi-shield-tt';
    protected static ?string $navigationGroup = 'Gestion de compte';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $groups = PermissionGroup::with('permissions')->get();

        $groupedFields = [];
        foreach ($groups as $group) {
            $options = $group->permissions->pluck('label', 'id')->toArray();

            $groupedFields[] = Forms\Components\Fieldset::make($group->label)
                ->schema([
                    Forms\Components\CheckboxList::make('permissions')
                        ->label('')
                        ->relationship('permissions', 'id')
                        ->options($options)
                        ->bulkToggleable()
                        ->gridDirection('row')
                        ->columnSpanFull()
                ])
                ->columnSpan(1);
        }

        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du rôle')
                            ->required()
                            ->unique(
                                table: 'roles',
                                column: 'name',
                                ignoreRecord: true,
                                modifyRuleUsing: fn ($rule) => $rule->where('guard_name', 'web')
                            ) // A8: validation unicité avec guard_name
                            ->validationMessages([
                                'unique' => 'Ce nom de rôle existe déjà. Veuillez en choisir un autre.',
                            ])
                            ->live(onBlur: true) // Validation en temps réel
                            ->helperText('Le nom doit être unique (ex: superviseur, comptable)')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Permissions')
                    ->columns(3)
                    ->schema($groupedFields)
                    ->collapsible()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Rôle')
                    ->sortable()
                    ->searchable()
                    ->badge(fn (\Spatie\Permission\Models\Role $record) => $record->name === 'admin')
                    ->color(fn (\Spatie\Permission\Models\Role $record) => $record->name === 'admin'
                        ? 'danger'
                        : 'primary'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions')
                    ->formatStateUsing(function ($state, \Spatie\Permission\Models\Role $record) {
                        return $record->name === 'admin' ? 'Super admin' : ($state > 0 ? "{$state} permissions" : 'Aucune permission');
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn (\Spatie\Permission\Models\Role $record) => $record->name === 'admin'),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (\Spatie\Permission\Models\Role $record) => $record->name === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn (\Spatie\Permission\Models\Role $record) => $record->name === 'admin'),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotIn('name', ['partner', 'user', 'gestionnaire']); // Fix A7: 'account' → 'gestionnaire'
    }

    public static function canEdit(Model $record): bool
    {
        return $record['name'] !== 'admin'; // TODO: Change the autogenerated stub
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'dev']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'dev']);
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'dev']), 403);

        parent::mount();
    }
}
