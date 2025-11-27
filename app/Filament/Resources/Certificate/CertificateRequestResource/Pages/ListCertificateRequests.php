<?php

namespace App\Filament\Resources\Certificate\CertificateRequestResource\Pages;

use App\Filament\Resources\Certificate\CertificateRequestResource;
use App\Models\Certificate\CertificateRequest;
use App\States\CertificateRequest\CertificateGenerated;
use App\States\CertificateRequest\PaymentInvalid;
use App\States\CertificateRequest\PaymentPending;
use App\States\CertificateRequest\PaymentValidated;
use App\States\CertificateRequest\PaymentVerification;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListCertificateRequests extends ListRecords
{
    protected static string $resource = CertificateRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return "Demandes d'attestations";
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CertificateRequestResource\Widgets\MonthlyYearlyCertificateRequestStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Toutes')->extraAttributes(['class' => 'text-xl font-medium']),
            'pending' => Tab::make('En attente')
                ->badge(CertificateRequest::whereState('state', PaymentPending::class)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereState('state', PaymentPending::class))
                ->extraAttributes(['class' => 'text-xl font-medium']),
            'verification' => Tab::make('En vérification')
                ->badge(CertificateRequest::whereState('state', PaymentVerification::class)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereState('state', PaymentVerification::class))
                ->extraAttributes(['class' => 'text-xl font-medium']),
            'validated' => Tab::make('Reçu')
                ->badge(CertificateRequest::whereState('state', PaymentValidated::class)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereState('state', PaymentValidated::class))
                ->extraAttributes(['class' => 'text-xl font-medium']),
            'invalid' => Tab::make('Invalides')
                ->badge(CertificateRequest::whereState('state', PaymentInvalid::class)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereState('state', PaymentInvalid::class))
                ->extraAttributes(['class' => 'text-xl font-medium']),
            'generated' => Tab::make('Générées')
                ->badge(CertificateRequest::whereState('state', CertificateGenerated::class)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereState('state', CertificateGenerated::class))
                ->extraAttributes(['class' => 'text-xl font-medium']),
        ];
    }
}
