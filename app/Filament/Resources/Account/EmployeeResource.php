<?php

namespace App\Filament\Resources\Account;

use App\Filament\Resources\Account;
use App\Filament\Resources\Account\EmployeeResource\Pages;
use App\Filament\Resources\Account\EmployeeResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use  Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = 'Compte';

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Gestion de compte';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                        Forms\Components\TextInput::make('surname')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telephone')
                            ->tel()
                            ->maxLength(20),

                // Section Authentification
                Forms\Components\Section::make('Authentification')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->confirmed()
                            ->minLength(8)
                            ->dehydrated(fn($state) => filled($state))
                            ->label('Mot de passe')
                            ->helperText(fn($operation) => $operation === 'edit'
                                ? 'Laissez vide pour conserver l\'actuel'
                                : ''),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->requiredWith('password')
                            ->minLength(8)
                            ->label('Confirmation')
                            ->dehydrated(false),
                    ]),

                // Section Rôles et Permissions
                Forms\Components\Section::make('Rôles et Permissions')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->columns(3)
                            ->options(function () {
                                return Role::whereNotIn('name', [
                                    'dev',
                                    'user',
                                    'gestionnaire', // Fix A7: cohérence 'account' → 'gestionnaire'
                                    'partner'
                                ])->pluck('name', 'id');
                            }),
                    ])->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color('primary')
                    ->label('Rôles'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Activé'),
            ])
            ->filters([
         /*       Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->default(['employee']), */
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Profil employé')
                    ->icon('heroicon-o-user-circle')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('full_name')
                            ->label('Nom complet')
                            ->state(function (User $record) {
                                return $record->surname . ' ' . $record->name;
                            })
                            ->columnSpan(2),

                        Infolists\Components\ImageEntry::make('photo')
                            ->label('')
                            ->disk('public')
                            ->width(100)
                            ->height(100)
                            ->circular()
                            ->columnSpan(1),

                        Infolists\Components\TextEntry::make('email')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('phone')
                            ->icon('heroicon-o-phone'),

                        Infolists\Components\TextEntry::make('date_birth')
                            ->icon('heroicon-o-cake')
                            ->date(),

                        Infolists\Components\TextEntry::make('last_login')
                            ->icon('heroicon-o-clock')
                            ->since(),
                    ]),

                Infolists\Components\Section::make('Rôles et permissions')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('roles')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'employee' => 'primary',
                                        'manager' => 'success',
                                        'supervisor' => 'warning',
                                        default => 'gray',
                                    }),
                            ])
                            ->grid(3),
                    ]),

                Infolists\Components\Section::make('Statistiques')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('certificateRequests_count')
                            ->label('Demandes créées')
                            ->numeric()
                            ->state(function (User $record) {
                                return $record->certificateRequests()->count();
                            }),

                        Infolists\Components\TextEntry::make('last_ip_address')
                            ->label('Dernière IP')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime(),
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
            'index' => Account\EmployeeResource\Pages\ListEmployees::route('/'),
           // 'create' => Account\EmployeeResource\Pages\CreateEmployee::route('/create'),
           // 'edit' => Account\EmployeeResource\Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', function($query) {
                $query->where('name', 'gestionnaire'); // Fix A7: 'account' → 'gestionnaire'
            });
    }

    public static function afterCreate(User $user, array $data): void
    {
        // Assigner le rôle gestionnaire (Fix A7: 'account' → 'gestionnaire')
        $user->assignRole('gestionnaire');

        // Synchroniser les autres rôles si nécessaire
        if (isset($data['roles'])) {
            $user->syncRoles(array_merge(['gestionnaire'], $data['roles']));
        }
    }

    public static function afterSave(User $user, array $data): void
    {
        // S'assurer que l'utilisateur conserve toujours le rôle gestionnaire
        if (!$user->hasRole('gestionnaire')) {
            $user->assignRole('gestionnaire');
        }
    }
}
