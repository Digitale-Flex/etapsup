<?php

namespace App\Services;

use App\Mail\Certificate\CertificateRequestGenerated;
use App\Models\Address;
use App\Models\Certificate\CertificateRequest;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Date\Date;
use PhpOffice\PhpWord\TemplateProcessor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Browsershot\Browsershot;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class CertificateGenerationService
{
    /*
     * Generate both certificate and contract PDFs
     *
     * @return array Paths to generated PDFs
     */
    public function generate(CertificateRequest $request, bool $useNotifications = false): array
    {
        $data = $this->prepareData($request, $useNotifications);

        if (!$data['address']) {
            return ['certificate' => null, 'contract' => null];
        }

        // Vérifier que LibreOffice est installé
        if (!$this->checkLibreOfficeInstalled()) {
            throw new \Exception('LibreOffice is required for PDF conversion');
        }

        // Générer les deux documents
        $certificatePath = $this->generateDocument(
            $request,
            $data,
            public_path('contracts/attestation_logement.docx'),
            'certificate',
            "attestation-de-logement_{$request->user->slug}"
        );

        $contractPath = $this->generateDocument(
            $request,
            $data,
            public_path('contracts/contrat_bail.docx'),
            'contract',
            "contrat-de-bail_{$request->user->slug}"
        );

        // Envoyer l'email avec les deux documents
        $this->sendEmail($request);

        return [
            'certificate' => $certificatePath,
            'contract' => $contractPath
        ];
    }

    /**
     * Generate a single document and attach it to the request
     */
    private function generateDocument(
        CertificateRequest $request,
        array              $data,
        string             $templatePath,
        string             $mediaCollection,
        string             $filePrefix
    ): string
    {
        // Utilisation du répertoire temp par défaut de Laravel
        $tempDir = Storage::path('temp/');
        $filePrefix .= '_' . $request->id;

        $outputDocPath = $tempDir . $filePrefix . '.docx';
        $pdfOutputPath = $tempDir . $filePrefix . '.pdf';

        try {
            // Charger le template DOCX
            $template = new TemplateProcessor($templatePath);

            $isCertificate = ($mediaCollection === 'certificate');
            $this->populateTemplate($template, $request, $data, $isCertificate);

            // Sauvegarder le DOCX modifié
            $template->saveAs($outputDocPath);

            // Convertir en PDF avec paramètres optimisés
            $this->convertToPdf($outputDocPath, $pdfOutputPath);

            // Attacher le PDF à la demande
            $this->handleMediaAttachment($request, $pdfOutputPath, $mediaCollection);

        } finally {
            // Nettoyage des fichiers temporaires
            @unlink($outputDocPath);
        }

        return $pdfOutputPath;
    }

    /**
     * Remplir le template avec les données
     */
    private function populateTemplate(TemplateProcessor $template, CertificateRequest $request, array $data, bool $isCertificate = false): void
    {
        Date::setLocale('fr');

        // Formatage des valeurs avant insertion
        $formattedPrice = number_format($data['price'], 2, ',', ' ');
        $formattedStartDate = $request->rental_start->format('d/m/Y');
        $formattedCreationDate = $request->created_at->format('d/m/Y');

        $isChambre = ($request->genre->name === 'Chambre en colocation');
        $isStudio = ($request->genre->name === 'Studio meublé');

        $template->setValues([
            'reference' => date('y') . '-' . str_pad($request->id, 8, '0', STR_PAD_LEFT),
            'full_name' => $request->user->full_name,
            'email' => $request->user->email,
            'phone' => $request->user->phone,
            'birth_date' => Date::parse($request->user->date_birth)->format('d/m/Y'),
            'birth_place' => $request->user->place_birth,
            'birth_country' => $request->user->country->name,
            'residence_country' => $request->country->name,
            'address' => $data['address']->street,
            'monthly_rent' => $formattedPrice,
            'rental_start_date' => $formattedStartDate,
            'habitat_type' => $request->genre->name,
            'creation_date' => $formattedCreationDate,
            'surface' => match($request->genre->name) {
                'Chambre en colocation' => '12 m2',
                'Studio meublé' => '18 m2',
                'Appartement T2' => '30 m2',
                default => 'Non spécifié',
            },
        ]);

        $this->setCheckbox($template, 'chambre_checkbox', $isChambre);
        $this->setCheckbox($template, 'studio_checkbox', $isStudio);

        // Insérer le QR code uniquement pour le certificat
        if ($isCertificate) {
            $this->insertQrCode($template, $data['qrcodeData']);
        }
    }

    private function setCheckbox(TemplateProcessor $template, string $placeholder, bool $isChecked): void
    {
        // Remplacer le placeholder par un caractère de case cochée ou décochée
        $template->setValue($placeholder, $isChecked ? '☒' : '☐');

        // Alternative : utiliser des images
        // $imagePath = $isChecked
        //     ? public_path('images/checked.png')
        //     : public_path('images/unchecked.png');
        // $template->setImageValue($placeholder, ['path' => $imagePath]);
    }
    /**
     * Insérer le QR code de manière optimisée
     */
    private function insertQrCode(TemplateProcessor $template, string $qrCodeData): void
    {
        $qrCodePath = $this->generateQrCodeImage($qrCodeData);

        // Créer un élément de positionnement absolu
        $template->setImageValue('qrcode', [
            'path' => $qrCodePath,
            'width' => 100,
            'height' => 100,
            'positioning' => 'absolute',
            'posHorizontal' => 'right',
            'posVertical' => 'bottom',
            'posHorizontalRel' => 'margin',
            'posVerticalRel' => 'margin',
            'marginLeft' => 0,
            'marginTop' => 0,
            'wrap' => 'none'
        ]);

        // Supprimer le fichier QR code temporaire après utilisation
        @unlink($qrCodePath);
    }

    /**
     * Générer le QR code comme image temporaire
     */
    private function generateQrCodeImage(string $qrCodeData): string
    {
        $tempPath = Storage::path('temp/') . uniqid('qrcode_', true) . '.png';

        QrCode::format('png')
            ->size(300)
            ->margin(1) // Marge minimale pour éviter les artefacts
            ->generate($qrCodeData, $tempPath);

        return $tempPath;
    }

    /**
     * Convertir DOCX en PDF avec LibreOffice - Version optimisée
     */
    private function convertToPdf(string $docxPath, string $pdfPath): void
    {
        // Créer un profil temporaire pour éviter les conflits
        $profileDir = sys_get_temp_dir() . '/lo_profile_' . uniqid();
        mkdir($profileDir, 0777, true);

        $command = sprintf(
            'libreoffice --headless --norestore --nofirststartwizard --nolockcheck --nodefault --nologo ' .
            '--convert-to pdf:writer_pdf_Export --outdir %s %s',
            escapeshellarg(dirname($pdfPath)),
            escapeshellarg($docxPath)
        );

        // Ajout des variables d'environnement pour le profil
        $env = [
            'HOME' => $profileDir,
            'TMPDIR' => sys_get_temp_dir(),
            'LIBO_EMBEDDED_TMPDIR' => sys_get_temp_dir(),
        ];

        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes, null, $env);

        if (!is_resource($process)) {
            throw new \Exception('Failed to start LibreOffice process');
        }

        // Fermer les pipes et attendre la fin du processus
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        // Supprimer le profil temporaire
        $this->deleteDirectory($profileDir);

        if ($returnCode !== 0) {
            throw new \Exception("PDF conversion failed (code $returnCode): \nSTDOUT: $stdout\nSTDERR: $stderr");
        }
    }

    /**
     * Supprimer récursivement un répertoire
     */
    private function deleteDirectory(string $dir): void
    {
        if (!file_exists($dir)) return;

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Vérifier si LibreOffice est installé
     */
    private function checkLibreOfficeInstalled(): bool
    {
        exec('libreoffice --version', $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Attacher le PDF à la demande
     */
    private function handleMediaAttachment(
        CertificateRequest $request,
        string             $pdfPath,
        string             $collectionName
    ): void
    {
        $fileName = basename($pdfPath);

        $request->addMedia($pdfPath)
            ->usingFileName($fileName)
            ->toMediaCollection($collectionName);
    }

    /**
     * Préparer les données pour le certificat
     */
    public function prepareData(CertificateRequest $request, bool $useNotifications = false): array
    {
        Date::setLocale('fr');
        $address = $this->selectAddress($request, $useNotifications);

        if (!$address) {
            return ['address' => null];
        }

        $qrCodeData = $this->generateQrCodeData(
            $request,
            $address->street,
            number_format($address->budget, 2, ',', ' ') . ' euro',
            $request->rental_start->format('d/m/Y')
        );

        return [
            'request' => $request,
            'address' => $address,
            'price' => $address->budget,
            'qrcodeData' => $qrCodeData,
        ];
    }

    /**
     * Select an appropriate address for the certificate request.
     */
    private function selectAddress(CertificateRequest $request, bool $useNotifications = false): ?Address
    {
        $requestBudgetInCents = $this->convertEuroToCents($request->budget);

        $availableAddresses = Address::where('city_id', $request->city_id)
            ->where('places', '>', 0)
            ->get()
            ->filter(function ($address) use ($requestBudgetInCents) {
                $addressBudgetInCents = $this->convertEuroToCents($address->budget);

                return $addressBudgetInCents <= $requestBudgetInCents;
            })
            ->sortByDesc(function ($address) {
                return $this->convertEuroToCents($address->budget);
            });

        if ($availableAddresses->isEmpty()) {
            if ($useNotifications) {
                $this->sendNoAddressNotification();
            }

            return null;
        }

        $selectedAddress = $availableAddresses->first();
        $selectedAddress->places -= 1;
        $selectedAddress->save();

        $request->update(['address' => $selectedAddress->street]);

        return $selectedAddress;
    }

    /**
     * Generate QR code data for the certificate.
     */
    private function generateQrCodeData(CertificateRequest $request, string $address, string $amount, string $date): string
    {
        return implode("\n", [
            "Nom: {$request->user->full_name}",
            "Adresse: {$address}",
            "Typologie: {$request->genre->name}",
            "Prix: {$amount}",
            "Début de bail: {$date}",
            'Validité: 2 mois',
        ]);
    }

    /**
     * Convert Euro amount to cents.
     */
    private function convertEuroToCents(float $amount): int
    {
        return (int)round($amount * 100);
    }

    /**
     * Send email with generated certificate.
     */
    private function sendEmail(CertificateRequest $request): void
    {
        Mail::to($request->user->email)->queue(new CertificateRequestGenerated($request));
    }

    /**
     * Send a notification when no address is available.
     */
    private function sendNoAddressNotification(): void
    {
        Notification::make()
            ->title('Aucune adresse disponible')
            ->body("Aucune adresse correspondant au budget n'est disponible pour la ville sélectionnée.")
            ->danger()
            ->send();
    }
}
