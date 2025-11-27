<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('realEstate.vat', 10);

        $this->migrator->add('realEstate.tourist_tax', 1.2);
        $this->migrator->add('realEstate.consumable', 1);
        $this->migrator->add('realEstate.rental_monthly_billing', 3);
        $this->migrator->add('realEstate.service_fees', 10);
        $this->migrator->add('realEstate.application_fees', 500);
        $this->migrator->add('realEstate.category_supporting_documents', 3);
    }
};
