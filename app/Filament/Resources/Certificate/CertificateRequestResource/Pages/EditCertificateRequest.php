<?php

namespace App\Filament\Resources\Certificate\CertificateRequestResource\Pages;

use App\Filament\Resources\Certificate\CertificateRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificateRequest extends EditRecord
{
    protected static string $resource = CertificateRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
