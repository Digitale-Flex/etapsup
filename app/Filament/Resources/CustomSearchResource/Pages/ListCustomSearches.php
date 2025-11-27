<?php

namespace App\Filament\Resources\CustomSearchResource\Pages;

use App\Filament\Resources\CustomSearchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListCustomSearches extends ListRecords
{
    protected static string $resource = CustomSearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return "Recherche personnalisées";
    }
}
