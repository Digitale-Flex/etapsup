<?php

namespace App\Filament\Resources\Account\EmployeeResource\Pages;

use App\Filament\Resources\Account\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function afterCreate(): void
    {
        // Appeler la méthode afterCreate de la ressource
        static::$resource::afterCreate($this->record, $this->data);
    }
}
