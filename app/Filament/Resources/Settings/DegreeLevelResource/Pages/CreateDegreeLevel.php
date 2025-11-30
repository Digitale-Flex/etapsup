<?php

namespace App\Filament\Resources\Settings\DegreeLevelResource\Pages;

use App\Filament\Resources\Settings\DegreeLevelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDegreeLevel extends CreateRecord
{
    protected static string $resource = DegreeLevelResource::class;

    protected static ?string $title = 'Nouveau niveau de diplôme';
}
