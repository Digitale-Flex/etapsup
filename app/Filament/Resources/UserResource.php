<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// Sprint1 Feature 1.6.1 — Comptes & Rôles (admin, partenaire, gestionnaire)
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Comptes';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 100;

    protected static ?string $navigationLabel = 'Comptes utilisateurs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations personnelles')
                    ->icon('heroicon-o-user')
                    ->columns(12)
                    ->schema([
                        Forms\Components\TextInput::make('surname')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(6),

                        Forms\Components\TextInput::make('name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(6),

                        Forms\Components\TextInput::make('email')
                            ->label('Adresse email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(6),

                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(6),

                        Forms\Components\TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? Hash::make($state) : null)
                            ->helperText('Laissez vide pour conserver le mot de passe actuel (édition)')
                            ->columnSpan(6),
                    ]),

                Forms\Components\Section::make('Rôle et permissions')
                    ->icon('heroicon-o-shield-check')
                    ->description('Définir le rôle de l\'utilisateur (admin, partenaire, gestionnaire)')
                    ->columns(12)
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Rôle')
                            ->options([
                                'admin' => '🟩 Administrateur',
                                'partner' => '🟦 Partenaire',
                                'manager' => '🟧 Gestionnaire',
                            ])
                            ->required()
                            ->default('user')
                            ->helperText('Admin: accès complet | Partenaire: gestion établissement | Gestionnaire: lecture et statuts')
                            ->columnSpan(6)
                            ->afterStateHydrated(function (Forms\Components\Select $component, $state, ?User $record) {
                                if ($record && $record->roles->isNotEmpty()) {
                                    $component->state($record->roles->first()->name);
                                }
                            })
                            ->dehydrated(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Compte actif')
                            ->helperText('L\'utilisateur peut se connecter')
                            ->default(true)
                            ->columnSpan(6),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('surname')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->copyMessage('Email copié'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrateur',
                        'partner' => 'Partenaire',
                        'manager' => 'Gestionnaire',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'partner' => 'info',
                        'manager' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date d\'inscription')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_login')
                    ->label('Dernière connexion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rôle')
                    ->relationship('roles', 'display_name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Compte actif')
                    ->placeholder('Tous les comptes')
                    ->trueLabel('Comptes actifs uniquement')
                    ->falseLabel('Comptes inactifs uniquement'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('2xl'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('roles')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
