<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

// Sprint1 Feature 1.4.1 — Répartition candidatures par pays (FR/BE/PL uniquement)
class CountryDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition par pays';

    protected static ?int $sort = 1;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Récupérer les candidatures par pays (FR/BE/PL uniquement)
        $countryData = Application::query()
            ->join('properties', 'reservations.property_id', '=', 'properties.id')
            ->join('cities', 'properties.city_id', '=', 'cities.id')
            ->join('countries', 'cities.country_id', '=', 'countries.id')
            ->whereIn('countries.name', ['France', 'Belgique', 'Pologne'])
            ->select('countries.name', DB::raw('COUNT(*) as total'))
            ->groupBy('countries.name')
            ->orderBy('total', 'desc')
            ->get();

        // Si aucune donnée, retourner des valeurs par défaut
        if ($countryData->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Candidatures',
                        'data' => [0, 0, 0],
                        'backgroundColor' => [
                            'rgb(54, 162, 235)',  // Bleu (France)
                            'rgb(255, 205, 86)',  // Jaune (Belgique)
                            'rgb(220, 53, 69)',   // Rouge (Pologne)
                        ],
                    ],
                ],
                'labels' => ['France', 'Belgique', 'Pologne'],
            ];
        }

        $labels = $countryData->pluck('name')->toArray();
        $data = $countryData->pluck('total')->toArray();

        // Couleurs selon le pays
        $colors = [];
        foreach ($labels as $country) {
            $colors[] = match ($country) {
                'France' => 'rgb(54, 162, 235)',    // Bleu
                'Belgique' => 'rgb(255, 205, 86)',  // Jaune
                'Pologne' => 'rgb(220, 53, 69)',    // Rouge
                default => 'rgb(156, 163, 175)',    // Gris par défaut
            };
        }

        return [
            'datasets' => [
                [
                    'label' => 'Candidatures',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Graphique en camembert (doughnut)
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
