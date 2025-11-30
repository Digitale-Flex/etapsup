<?php

namespace App\Filament\Resources\RealEstate\PropertyResource\RelationManagers;

use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    protected static ?string $title = 'Candidatures';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
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

                        Forms\Components\Select::make('program_id')
                            ->relationship('program', 'name')
                            ->label('Programme choisi')
                            ->searchable()
                            ->preload()
                            ->columnSpan(6),

                        Forms\Components\Select::make('status')
                            ->label('Statut de la candidature')
                            ->options([
                                'pending' => 'En attente',
                                'under_review' => 'En cours d\'examen',
                                'accepted' => 'Acceptée',
                                'rejected' => 'Refusée',
                                'cancelled' => 'Annulée',
                            ])
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->description(fn (Application $record): string => $record->user->email ?? '')
                    ->label('Étudiant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('program.name')
                    ->label('Programme')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'under_review' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'under_review' => 'En examen',
                        'accepted' => 'Acceptée',
                        'rejected' => 'Refusée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fees')
                    ->label('Frais')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Début souhaité')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date candidature')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'under_review' => 'En examen',
                        'accepted' => 'Acceptée',
                        'rejected' => 'Refusée',
                        'cancelled' => 'Annulée',
                    ]),

                Tables\Filters\SelectFilter::make('program_id')
                    ->label('Programme')
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                // Désactivé: Les candidatures ne peuvent être créées que depuis le frontend
            ])
            ->actions([
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
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'under_review'])),

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
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'under_review'])),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
