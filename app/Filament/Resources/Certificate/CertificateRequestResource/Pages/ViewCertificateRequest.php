<?php

namespace App\Filament\Resources\Certificate\CertificateRequestResource\Pages;

use App\Filament\Resources\Certificate\CertificateRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewCertificateRequest extends ViewRecord
{
    protected static string $resource = CertificateRequestResource::class;

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
