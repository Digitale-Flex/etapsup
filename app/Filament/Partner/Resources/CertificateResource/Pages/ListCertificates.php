<?php

namespace App\Filament\Partner\Resources\CertificateResource\Pages;

use App\Filament\Partner\Resources\CertificateResource;
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

class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;

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
          CertificateResource\Widgets\CertificateStatsOverview::class
        ];
    }

    public function getTabs(): array
    {
        $partnerFilter = fn(Builder $query) =>
        auth()->user()->hasRole('partner')
            ? $query->where('partner_id', auth()->user()->partner_id)
            : $query;

        return [
            'all' => Tab::make('Toutes')
                ->badge(CertificateRequest::query()
                    ->tap($partnerFilter)
                    ->count())
                ->modifyQueryUsing($partnerFilter)
                ->extraAttributes(['class' => '']),

            'pending' => Tab::make('En attente')
                ->badge(CertificateRequest::whereState('state', PaymentPending::class)
                    ->tap($partnerFilter)
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereState('state', PaymentPending::class)
                    ->tap($partnerFilter))
                ->extraAttributes(['class' => '']),

            'verification' => Tab::make('En vérification')
                ->badge(CertificateRequest::whereState('state', PaymentVerification::class)
                    ->tap($partnerFilter)
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereState('state', PaymentVerification::class)
                    ->tap($partnerFilter))
                ->extraAttributes(['class' => '']),

            'validated' => Tab::make('Reçu')
                ->badge(CertificateRequest::whereState('state', PaymentValidated::class)
                    ->tap($partnerFilter)
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereState('state', PaymentValidated::class)
                    ->tap($partnerFilter))
                ->extraAttributes(['class' => '']),

            'invalid' => Tab::make('Invalides')
                ->badge(CertificateRequest::whereState('state', PaymentInvalid::class)
                    ->tap($partnerFilter)
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereState('state', PaymentInvalid::class)
                    ->tap($partnerFilter))
                ->extraAttributes(['class' => '']),

            'generated' => Tab::make('Générées')
                ->badge(CertificateRequest::whereState('state', CertificateGenerated::class)
                    ->tap($partnerFilter)
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereState('state', CertificateGenerated::class)
                    ->tap($partnerFilter))
                ->extraAttributes(['class' => '']),
        ];
    }
}
