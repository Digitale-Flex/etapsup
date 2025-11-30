<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestApplicationsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $heading = 'Dernières candidatures';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Application::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Étudiant')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('property.title')
                    ->label('Établissement')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('program.name')
                    ->label('Programme')
                    ->limit(30)
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
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->url(fn (Application $record): string => ApplicationResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
