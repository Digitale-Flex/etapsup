<?php

namespace App\Filament\Resources\Settings\EstablishmentTypeResource\Pages;

use App\Filament\Resources\Settings\EstablishmentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEstablishmentType extends CreateRecord
{
    protected static string $resource = EstablishmentTypeResource::class;

    protected static ?string $title = 'Nouveau type d\'établissement';
}
