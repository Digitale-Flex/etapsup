<?php

namespace App\Filament\Partner\Resources\CertificateResource\Pages;

use App\Filament\Partner\Resources\CertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewCertificate extends ViewRecord
{
    protected static string $resource = CertificateResource::class;

    protected static string $view = 'filament.resources.certificate.pages.view-request';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return "Demandes d'attestations";
    }
}
