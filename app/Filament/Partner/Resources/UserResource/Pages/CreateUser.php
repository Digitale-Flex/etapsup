<?php

namespace App\Filament\Partner\Resources\UserResource\Pages;

use App\Filament\Partner\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
