<?php

namespace App\Filament\Resources\Certificate\PartnerResource\Pages;

use App\Filament\Resources\Certificate\PartnerResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ManagePartnerUsers extends ManageRelatedRecords
{
    protected static string $resource = PartnerResource::class;

    protected static string $relationship = 'users';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $title = 'Gérer les utilisateurs partenaires';

    public static function getNavigationLabel(): string
    {
        return 'Utilisateurs';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_active')
                    ->label('Activer')
                    ->required(),
                Forms\Components\Toggle::make('is_partner_manager')
                    ->label('Admin')
                    ->required(),
                Forms\Components\TextInput::make('surname')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Prénom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Adresse email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @throws \Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('surname')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Prénom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Adresse email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activer')
                    ->default(true)
                    ->alignCenter()
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_partner_manager')
                    ->label('Admin')
                    ->default(true)
                    ->alignCenter()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créér le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Ajouter un utilisateur')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Ajouter un utilisateur')
                    ->modalIcon('heroicon-o-plus')
                    ->modalWidth('lg')
                    ->using(function (array $data, $record): User {
                        $data['partner_id'] = $this->getOwnerRecord()->id;
                        $user = User::create($data);
                        $partnerRole = Role::findByName('partner');
                        $user->assignRole($partnerRole);

                        return $user;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalIcon('heroicon-o-pencil')
                    ->modalWidth('lg'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
