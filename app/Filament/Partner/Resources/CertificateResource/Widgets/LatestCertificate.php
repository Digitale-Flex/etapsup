<?php

namespace App\Filament\Partner\Resources\CertificateResource\Widgets;

use App\Models\Certificate\CertificateRequest;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class LatestCertificate extends Widget
{
    protected static ?string $heading = 'Dernières demandes';

    protected int|string|array $columnSpan = 'full';

    public function getTableQuery(): Relation|Builder|null
    {
        return CertificateRequest::query()
            ->when(
                auth()->user()->hasRole('partner'),
                fn ($query) => $query->where('partner_id', auth()->user()->partner_id)
            )
            ->latest()
            ->take(10);
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
