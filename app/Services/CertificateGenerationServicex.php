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
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Browsershot\Browsershot;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class CertificateGenerationServicex
{
    /**
     * Generate a certificate PDF for the given request.
     *
     * @return string|null The path to the generated PDF file
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function generate(CertificateRequest $request, bool $useNotifications = false): ?string
    {
        $data = $this->prepareData($request, $useNotifications);

        if (! $data['address']) {
            return null;
        }

        /* $tempPath = public_path('Label-LIVE-Sales-Receipt-24910.pdf');
         $fileName = 'Label-LIVE-Sales-Receipt-24910';
         $request->addMedia($tempPath)
             ->usingFileName($fileName)
             ->preservingOriginal()
             ->toMediaCollection('certificate'); */

        $html = view('pdf.certificate', $data)->render();

        $fileName = "attestation-de-logement_{$request->user->slug}.pdf";
        $tempPath = Storage::path("temp/{$fileName}");

        Browsershot::html($html)
            ->setNpmBinary('/usr/bin/pnpm')
            ->format('a4')
            ->showBackground()
            ->save($tempPath);

        if (! file_exists($tempPath)) {
            throw new \RuntimeException('Failed to generate PDF file.');
        }

        $request->addMedia($tempPath)
            ->usingFileName($fileName)
            ->toMediaCollection('certificate');

        Storage::delete("temp/{$fileName}");

        $this->sendEmail($request);

        return $tempPath;
    }

    /**
     * Prepare data for certificate generation.
     */
    public function prepareData(CertificateRequest $request, bool $useNotifications = false): array
    {
        Date::setLocale('fr');
        $date = Date::parse($request->rental_start)->format('jS F Y');
        $country = $request->country;

        $address = $this->selectAddress($request, $useNotifications);
        Log::debug($address);

        if (! $address) {
            return ['address' => null];
        }

        $amount = number_format($address->budget, 2, ',', ' ').' euro';

        $qrCodeData = $this->generateQrCodeData($request, $address->street, $amount, $date);
        $qrcode = QrCode::size(130)->generate($qrCodeData);
        $price = $address->budget;

        return compact('request', 'address', 'country', 'qrcode', 'date', 'amount', 'price');
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
        return (int) round($amount * 100);
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
