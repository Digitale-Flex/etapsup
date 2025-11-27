<?php

namespace App\Filament\Resources\CustomSearchResource\Pages;

use App\Filament\Resources\CustomSearchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomSearch extends EditRecord
{
    protected static string $resource = CustomSearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
