<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $modelLabel = 'Candidature';

    protected static ?string $pluralLabel = 'Candidatures';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gestion des Candidatures';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Candidature')
                    ->description('Détails de la candidature étudiante')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Étudiant')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(6),

                        Forms\Components\Select::make('property_id')
                            ->relationship('property', 'title')
                            ->label('Établissement visé')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(6),

                        Forms\Components\Select::make('program_id')
                            ->relationship('program', 'name')
                            ->label('Programme choisi')
                            ->searchable()
                            ->preload()
                            ->columnSpan(6),

                        Forms\Components\Select::make('status')
                            ->label('Statut de la candidature')
                            ->options(ApplicationStatus::toSelectOptions())
                            ->default('pending')
                            ->required()
                            ->columnSpan(6),

                        Forms\Components\TextInput::make('fees')
                            ->label('Frais de dossier')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->columnSpan(6),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Date de début souhaitée')
                            ->columnSpan(6),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes administratives')
                            ->helperText('Notes internes pour le suivi du dossier')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('reason')
                            ->label('Motif (si refus ou annulation)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->description(fn (Application $record): string => $record->user->email ?? '')
                    ->label('Étudiant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('property.title')
                    ->label('Établissement')
                    ->searchable()
                    ->limit(40)
                    ->sortable(),

                Tables\Columns\TextColumn::make('program.name')
                    ->label('Programme')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // Sprint1 Feature 1.4.1 — Colonne Pays
                Tables\Columns\TextColumn::make('property.city.country.name') // A20
                    ->label('Pays')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => ApplicationStatus::getStatusColor($state))
                    ->formatStateUsing(fn (string $state): string => ApplicationStatus::translateStatus($state))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fees')
                    ->label('Frais')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),

                // Feature 9 - Sprint 1: Colonne Accompagnement Premium
                Tables\Columns\IconColumn::make('accompagnement_premium')
                    ->label('Accompagnement')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->tooltip(fn (Application $record): string =>
                        $record->accompagnement_premium
                            ? ($record->accompagnement_paid ? 'Payé' : 'En attente de paiement')
                            : 'Non demandé'
                    ),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Début souhaité')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date candidature')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mise à jour')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Sprint1 Feature 1.4.1 — Eager loading pour éviter N+1 queries
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'user',
                'property.city.country', // A20
                'program',
            ]))
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(ApplicationStatus::toFilterOptions()),

                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Établissement')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->preload(),

                // Sprint1 Feature 1.4.1 — Filtre par pays (FR/BE/PL uniquement)
                Tables\Filters\Filter::make('country')
                    ->form([
                        Forms\Components\Select::make('country')
                            ->label('Pays')
                            ->options([
                                'France' => 'France',
                                'Belgique' => 'Belgique',
                                'Pologne' => 'Pologne',
                            ])
                            ->placeholder('Tous les pays'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['country'],
                            fn (Builder $query, $country): Builder => $query->whereHas(
                                'property.city.country', // A20
                                fn ($query) => $query->where('name', $country)
                            )
                        );
                    }),

                // Feature 9 - Sprint 1: Filtre Accompagnement Premium
                Tables\Filters\TernaryFilter::make('accompagnement_premium')
                    ->label('Accompagnement Premium')
                    ->placeholder('Tous')
                    ->trueLabel('Avec accompagnement')
                    ->falseLabel('Sans accompagnement')
                    ->queries(
                        true: fn (Builder $query) => $query->where('accompagnement_premium', true),
                        false: fn (Builder $query) => $query->where('accompagnement_premium', false),
                        blank: fn (Builder $query) => $query,
                    ),

                Tables\Filters\TernaryFilter::make('accompagnement_paid')
                    ->label('Accompagnement Payé')
                    ->placeholder('Tous')
                    ->trueLabel('Payé')
                    ->falseLabel('Non payé')
                    ->queries(
                        true: fn (Builder $query) => $query->where('accompagnement_paid', true),
                        false: fn (Builder $query) => $query->where('accompagnement_paid', false),
                        blank: fn (Builder $query) => $query,
                    ),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                // Sprint1 Feature 1.6.1 — Actions limitées pour gestionnaires
                Tables\Actions\Action::make('accept')
                    ->label('Accepter')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Accepter la candidature')
                    ->modalDescription('Êtes-vous sûr de vouloir accepter cette candidature ?')
                    ->modalSubmitActionLabel('Accepter')
                    ->action(function ($record) {
                        $record->update(['status' => 'accepted']);
                        \Filament\Notifications\Notification::make()
                            ->title('Candidature acceptée')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'under_review']))
                    ->hidden(fn () => auth()->user()->hasRole('manager')), // Gestionnaires ne peuvent pas accepter

                Tables\Actions\Action::make('review')
                    ->label('Mettre en examen')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'under_review']);
                        \Filament\Notifications\Notification::make()
                            ->title('Candidature mise en examen')
                            ->info()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),
                    // Gestionnaires peuvent mettre en examen (OK)

                Tables\Actions\Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Refuser la candidature')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Motif du refus')
                            ->required()
                            ->rows(3)
                            ->helperText('Expliquez la raison du refus pour l\'étudiant'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'reason' => $data['reason']
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Candidature refusée')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'under_review']))
                    ->hidden(fn () => auth()->user()->hasRole('manager')), // Gestionnaires ne peuvent pas refuser

                Tables\Actions\EditAction::make()
                    ->hidden(fn () => auth()->user()->hasRole('manager')), // Gestionnaires ne peuvent pas éditer
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }

    // Sprint1 Feature 1.6.1 — Filtrage des candidatures par rôle
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $user = auth()->user();

        // Partenaires : uniquement les candidatures de leurs établissements
        if ($user && $user->hasRole('partner') && $user->partner_id) {
            $query->whereHas('property', function ($q) use ($user) {
                $q->where('partner_id', $user->partner_id);
            });
        }

        // Gestionnaires : toutes les candidatures (lecture + mise à jour statut)
        // Pas de filtre supplémentaire pour manager
        // Admins : accès complet, pas de filtre

        return $query;
    }

    public static function canCreate(): bool
    {
        return false; // Candidatures créées uniquement par les utilisateurs frontend
    }

    // Sprint1 Feature 1.6.1 — Restriction des actions par rôle
    public static function canEdit($record): bool
    {
        $user = auth()->user();

        // Gestionnaires : lecture uniquement, pas d'édition
        if ($user && $user->hasRole('manager')) {
            return false;
        }

        return parent::canEdit($record);
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();

        // Gestionnaires : pas de suppression
        if ($user && $user->hasRole('manager')) {
            return false;
        }

        return parent::canDelete($record);
    }
}
