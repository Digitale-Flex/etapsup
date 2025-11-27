<?php

namespace App\Http\Resources;

use App\States\CertificateRequest\CertificateGenerated;
use App\States\CertificateRequest\PaymentInvalid;
use App\States\CertificateRequest\PaymentPending;
use App\States\CertificateRequest\PaymentValidated;
use App\States\CertificateRequest\PaymentVerification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'reference' => $this->formatReference(),
            'nationality' => $this->nationality,
            'budget' => $this->budget,
            'rentalStart' => $this->rental_start,
            'duration' => $this->duration,
            'address' => $this->address,
            'state' => [
                'label' => $this->state->label(),
                'value' => $this->state,
                'color' => $this->getStateColor(),
            ],
            'furtherInformation' => $this->further_information,
            'genre' => new GenreResource($this->whenLoaded('genre')),
            'city' => new CityResource($this->whenLoaded('city')),
            'country' => new CountryResource($this->whenLoaded('country')),
            'rentalDeposit' => new RentalDepositesource($this->whenLoaded('rentalDeposit')),
            'rentalDeposits' => RentalDepositesource::collection($this->whenLoaded('rentalDeposits')),
            'partner' => new PartnerResource($this->whenLoaded('partner')),
            'paid' => $this->paid,
            'file' => $this->getFirstMediaUrl('certificate'),
        ];
    }

    private function formatReference(): string
    {
        $year = date('y');
        $paddedId = str_pad((string) $this->id, 8, '0', STR_PAD_LEFT);

        return "{$year}-{$paddedId}";
    }

    private function getStateColor(): string
    {
        return match ($this->state::class) {
            PaymentPending::class => 'warning',
            PaymentVerification::class => 'info',
            PaymentValidated::class => 'primary',
            PaymentInvalid::class => 'danger',
            CertificateGenerated::class => 'success',
            default => 'secondary',
        };
    }
}
