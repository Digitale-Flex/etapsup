<?php

namespace App\Filament\Resources\Settings\CareerFieldResource\Pages;

use App\Filament\Resources\Settings\CareerFieldResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCareerField extends CreateRecord
{
    protected static string $resource = CareerFieldResource::class;

    protected static ?string $title = 'Nouveau métier';
}
