<?php

namespace App\Filament\Resources\Certificate\PartnerResource\Pages;

use App\Filament\Resources\Certificate\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un partenaire')
                ->icon('heroicon-s-plus')
                ->modalHeading('Ajouter un partenaire')
                ->modalIcon('heroicon-s-plus')
                ->modalWidth('xl'),
        ];
    }
}
