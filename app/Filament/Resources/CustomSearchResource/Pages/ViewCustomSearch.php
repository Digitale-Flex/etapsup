<?php

namespace App\Filament\Resources\CustomSearchResource\Pages;

use App\Filament\Resources\CustomSearchResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewCustomSearch extends ViewRecord
{
    protected static string $resource = CustomSearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return "Détails de la demande personnalisées";
    }
}
