<?php

namespace App\Filament\Partner\Resources\CertificateResource\Widgets;

use App\Models\Certificate\CertificateRequest;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CertificateTrend extends ChartWidget
{
    protected static ?string $heading = 'Évolution des demandes';

    protected static ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = true;

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Tout',
            'week' => 'Dernière semaine',
            'month' => 'Dernier mois',
            'year' => 'Dernière année',
        ];
    }

    protected function getData(): array
    {
        $query = CertificateRequest::query();

        // Appliquer le filtre temporel
        $query = match ($this->filter) {
            'week' => $query->where('created_at', '>=', Carbon::now()->subWeek()),
            'month' => $query->where('created_at', '>=', Carbon::now()->subMonth()),
            'year' => $query->where('created_at', '>=', Carbon::now()->subYear()),
            'all' => $query,
            default => $query,
        };

        if (auth()->user()->hasRole('partner')) {
            $query->where('partner_id', auth()->user()->partner_id);
        }

        $data = $query
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Nombre de demandes',
                    'data' => $data->pluck('count')->toArray(),
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgba(75, 192, 192, 1)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgba(75, 192, 192, 1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('d/m/Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'precision' => 0,
                            'color' => '#666',
                        ],
                        'grid' => [
                            'color' => 'rgba(0, 0, 0, 0.05)',
                            'drawBorder' => false,
                        ],
                    ],
                    'x' => [
                        'ticks' => [
                            'color' => '#666',
                            'maxRotation' => 45,
                            'minRotation' => 45,
                        ],
                        'grid' => [
                            'display' => false,
                            'drawBorder' => false,
                        ],
                    ],
                ],
                'plugins' => [
                    'legend' => [
                        'labels' => [
                            'color' => '#666',
                            'font' => [
                                'size' => 12,
                            ],
                        ],
                    ],
                    'tooltip' => [
                        'backgroundColor' => 'rgba(0, 0, 0, 0.7)',
                        'bodyFont' => [
                            'size' => 14,
                        ],
                        'titleFont' => [
                            'size' => 16,
                            'weight' => 'bold',
                        ],
                    ],
                ],
                'elements' => [
                    'line' => [
                        'fill' => 'start',
                    ],
                ],
                'responsive' => true,
                'maintainAspectRatio' => false,
            ],
        ];
    }

    protected function getHeight(): int
    {
        return 250;
    }

    public function getDescription(): ?string
    {
        return "Visualisation du nombre de demandes d'attestations sur la période sélectionnée.";
    }
}
