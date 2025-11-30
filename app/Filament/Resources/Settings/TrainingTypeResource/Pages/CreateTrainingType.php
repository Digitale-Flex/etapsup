<?php

namespace App\Filament\Resources\Settings\TrainingTypeResource\Pages;

use App\Filament\Resources\Settings\TrainingTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainingType extends CreateRecord
{
    protected static string $resource = TrainingTypeResource::class;

    protected static ?string $title = 'Nouveau type de formation';
}
