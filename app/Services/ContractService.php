<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class ContractService
{
    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function generateContract($reservation): string
    {
        // Vérification des dépendances
       if (! $this->checkLibreOfficeInstalled()) {
            throw new \Exception('LibreOffice is required for PDF conversion');
        }

        $filePrefix = 'contrat_de_logement_' . $reservation->user->slug . '_' . $reservation->hashid;
        $tempDir = storage_path('app/private/contracts/tmp/');

        // Création du répertoire si inexistant
        Storage::makeDirectory('private/contracts/tmp');

        try {
            $templatePath = storage_path('app/private/contracts/short_duration.docx');
            $outputDocPath = $tempDir.$filePrefix.'.docx';
            $pdfOutputPath = $tempDir.$filePrefix.'.pdf';

            // Traitement du document
            $template = new TemplateProcessor($templatePath);
            $this->populateTemplate($template, $reservation);
            $template->saveAs($outputDocPath);

            // Conversion PDF
            $this->convertToPdf($outputDocPath, $pdfOutputPath);

            // Gestion média
            $this->handleMediaAttachment($reservation, $pdfOutputPath);

        } finally {
            // Nettoyage des fichiers temporaires
            @unlink($outputDocPath);
            @unlink($pdfOutputPath);
        }

        return $pdfOutputPath;
    }

    private function populateTemplate(TemplateProcessor $template, $reservation): void
    {
        $template->setValues([
            'year' => date('y'),
            'id' => str_pad($reservation->id, 8, '0', STR_PAD_LEFT),
            'name' => $reservation->user->full_name,
            'email' => $reservation->user->email,
            'phone' => $reservation->user->phone,
            'date_birth' => Carbon::parse($reservation->user->date_birth)->locale('fr')->translatedFormat('jS F Y'),
            'place_birth' => $reservation->user->place_birth,
            'address' => $reservation->property->address,
            'type' => $reservation->property->propertyType->label,
            'room' => $reservation->property->room,
            'price' => $reservation->property->price,
            'start' => $reservation->start_date->format('d/m/Y'),
            'end' => $reservation->end_date->format('d/m/Y'),
            'stay' => $reservation->start_date->diffInDays($reservation->end_date) + 1,
            'date' => now()->format('d/m/Y'),
        ]);
    }

    private function convertToPdf($wordPath, $pdfPath): void
    {
        $command = 'LIBREOFFICE_PROFILE=/tmp/libreoffice_config libreoffice --headless --convert-to pdf --outdir '.dirname($pdfPath).' '.$wordPath;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('PDF conversion failed: '.implode("\n", $output));
        }
    }

    private function checkLibreOfficeInstalled(): bool
    {
        exec('which libreoffice', $output, $returnCode);

        return $returnCode === 0;
    }

    private function handleMediaAttachment($reservation, $pdfPath): void
    {
       // $reservation->clearMediaCollection('contract');
        $reservation->addMedia($pdfPath)
            ->usingName('Contrat_de_logement_'.$reservation->user->slug . '_' . $reservation->hashid)
            ->toMediaCollection('contract');
    }
}
