<?php

namespace App\Filament\Partner\Resources\CertificateResource\Widgets;

use App\Models\Certificate\CertificateRequest;
use App\States\CertificateRequest\CertificateGenerated;
use App\States\CertificateRequest\PaymentPending;
use App\States\CertificateRequest\PaymentValidated;
use App\States\CertificateRequest\PaymentVerification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class CertificateStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyRequests = $this->getMonthlyRequests($currentYear, $currentMonth);
        $yearlyRequests = $this->getYearlyRequests($currentYear);

        $monthlyRevenue = $monthlyRequests * 199;
        $yearlyRevenue = $yearlyRequests * 199;

        $pendingRequests = $this->getPendingRequests();
        $verificationRequests = $this->getVerificationRequests();

        return [
            Stat::make('Demandes mensuelles', $monthlyRequests)
                ->description($yearlyRequests.' demandes cette année')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary')
                ->chart($this->getMonthlyTrend())
                ->icon('heroicon-s-document-text'),

            Stat::make('Revenus mensuel', number_format($monthlyRevenue, 2).' €')
                ->description(number_format($yearlyRevenue, 2).' € cette année')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('success')
                ->chart($this->getMonthlyRevenueTrend())
                ->icon('heroicon-s-banknotes'),

            Stat::make('Demandes en attente de paiement', $pendingRequests)
                ->description($verificationRequests.' demandes en cours de vérification')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart($this->getPendingTrend())
                ->icon('heroicon-s-currency-dollar'),
        ];
    }

    protected function getMonthlyRequests($year, $month): int
    {
        return CertificateRequest::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereState('state', [PaymentValidated::class, CertificateGenerated::class])
            ->when(
                auth()->user()->hasRole('partner'),
                fn($q) => $q->where('partner_id', auth()->user()->partner_id)
            )
            ->count();
    }

    protected function getYearlyRequests($year): int
    {
        return CertificateRequest::query()
            ->whereYear('created_at', $year)
            ->whereState('state', [PaymentValidated::class, CertificateGenerated::class])
            ->when(
                auth()->user()->hasRole('partner'),
                fn($q) => $q->where('partner_id', auth()->user()->partner_id)
            )
            ->count();
    }

    protected function getMonthlyTrend(): array
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        return CertificateRequest::query()
            ->select(DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', '<=', $currentMonth)
            ->whereState('state', [PaymentValidated::class, CertificateGenerated::class])
            ->when(
                auth()->user()->hasRole('partner'),
                fn($q) => $q->where('partner_id', auth()->user()->partner_id)
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->pluck('count')
            ->toArray();
    }

    protected function getMonthlyRevenueTrend(): array
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $trend = CertificateRequest::query()
            ->select(DB::raw('COUNT(*) * 199 as revenue'))
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', '<=', $currentMonth)
            ->whereState('state', [PaymentValidated::class, CertificateGenerated::class])
            ->when(
                auth()->user()->hasRole('partner'),
                fn($q) => $q->where('partner_id', auth()->user()->partner_id)
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->pluck('revenue')
            ->toArray();

        return array_map('intval', $trend);
    }

    protected function getPendingRequests(): int
    {
        return CertificateRequest::query()
            ->whereState('state', PaymentPending::class)
            ->when(
                auth()->user()->hasRole('partner'),
                fn($q) => $q->where('partner_id', auth()->user()->partner_id)
            )
            ->count();
    }

    protected function getVerificationRequests(): int
    {
        return CertificateRequest::query()
            ->whereState('state', PaymentVerification::class)
            ->when(
                auth()->user()->hasRole('partner'),
                fn($q) => $q->where('partner_id', auth()->user()->partner_id)
            )
            ->count();
    }

    protected function getPendingTrend(): array
    {
        $subquery = CertificateRequest::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereState('state', PaymentPending::class)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->when(
                auth()->user()->hasRole('partner'),
                fn($q) => $q->where('partner_id', auth()->user()->partner_id)
            )
            ->groupBy('date');

        return DB::table(DB::raw("({$subquery->toSql()}) as sub"))
            ->mergeBindings($subquery->getQuery())
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }
}
