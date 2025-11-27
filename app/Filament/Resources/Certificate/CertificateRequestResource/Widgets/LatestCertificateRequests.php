<?php

namespace App\Filament\Resources\Certificate\CertificateRequestResource\Widgets;

use App\Models\Certificate\CertificateRequest;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class LatestCertificateRequests extends TableWidget
{
    protected static ?string $heading = 'Dernières demandes';

    protected int|string|array $columnSpan = 'full';

    public function getTableQuery(): Relation|Builder|null
    {
        return CertificateRequest::query()->latest()->take(10);
    }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('id')
                ->label('ID')
                ->formatStateUsing(fn ($state) => date('y').'-'.str_pad($state, 3, '0', STR_PAD_LEFT)),
            \Filament\Tables\Columns\TextColumn::make('user.name')->label('Demandeur'),
            \Filament\Tables\Columns\TextColumn::make('state')->label('Statut')->formatStateUsing(fn (CertificateRequest $record): string => $record->state->label()),
            \Filament\Tables\Columns\TextColumn::make('created_at')->label('Date')->date(),
        ];
    }
}
