<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateCertificateRequestsPaidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Méthode 1: Utilisation du Query Builder (plus rapide pour les grosses tables)
        \DB::table('certificate_requests')
            ->whereNull('paid')
            ->update(['paid' => 399]);

        // Méthode alternative 2: Utilisation des Models (plus lente mais plus flexible)
        // CertificateRequest::whereNull('paid')
        //     ->each(function ($certificateRequest) {
        //         $certificateRequest->update(['paid' => 399]);
        //     });

        $this->command->info('Mise à jour des certificate_requests avec paid=NULL terminée.');
    }
}
