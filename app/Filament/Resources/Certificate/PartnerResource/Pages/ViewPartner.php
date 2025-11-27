<?php

namespace App\Filament\Resources\Certificate\PartnerResource\Pages;

use App\Filament\Resources\Certificate\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPartner extends ViewRecord
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
