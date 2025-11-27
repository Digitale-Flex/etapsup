<?php

namespace App\Filament\Resources\Settings\CountryResource\Pages;

use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateNewCity extends CreateRelatedRecord
{
    protected static string $relationship = 'cities';
}
