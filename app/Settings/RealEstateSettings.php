<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class RealEstateSettings extends Settings
{
    public float $vat;
    public float $tourist_tax;
    public float $consumable;
    public int $rental_monthly_billing;
    public float $service_fees;
    public float $application_fees;
    public int $category_supporting_documents;

    public static function group(): string
    {
        return 'realEstate';
    }
}
