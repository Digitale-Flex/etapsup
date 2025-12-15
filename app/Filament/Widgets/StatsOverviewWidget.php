<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\CustomSearch;
use App\Models\RealEstate\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // Total établissements publiés
        $totalEstablishments = Property::where('is_published', true)->count();

        // Total candidatures
        $totalApplications = Application::count();

        // Candidatures en attente
        $pendingApplications = Application::where('status', 'pending')->count();

        // Taux d'acceptation
        $acceptedApplications = Application::where('status', 'accepted')->count();
        $acceptanceRate = $totalApplications > 0
            ? round(($acceptedApplications / $totalApplications) * 100, 1)
            : 0;

        // Demandes d'accompagnement
        $totalAccompagnement = CustomSearch::where('state', 'payment_validated')->count();

        return [
            Stat::make('Établissements', $totalEstablishments)
                ->description('Établissements publiés')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Candidatures', $totalApplications)
                ->description('Total toutes périodes')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success')
                ->chart([3, 5, 8, 12, 15, 18, 20, 22]),

            Stat::make('En attente', $pendingApplications)
                ->description('Nécessitent une action')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chart([2, 3, 4, 5, 6, 4, 3, 2]),

            Stat::make('Accompagnements', $totalAccompagnement)
                ->description('Demandes validées')
                ->descriptionIcon('heroicon-o-hand-raised')
                ->color('info')
                ->chart([1, 2, 3, 2, 4, 3, 5, 4]),

            Stat::make('Taux d\'acceptation', $acceptanceRate . '%')
                ->description($acceptedApplications . ' candidatures acceptées')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            // Sprint1 Feature 1.4.1 — CA Mensuel (hardcodé MVP)
            Stat::make('CA Mensuel', '15 000 €')
                ->description('Chiffre d\'affaires du mois')
                ->descriptionIcon('heroicon-o-currency-euro')
                ->color('info')
                ->chart([10, 12, 14, 13, 15, 16, 14, 15]),
        ];
    }
}
