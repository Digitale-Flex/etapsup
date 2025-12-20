<?php

namespace Database\Seeders;

use App\Models\Certificate\RentalDeposit;
use App\Models\RealEstate\Layout;
use Illuminate\Database\Seeder;

/**
 * EtapSup Data Seeder
 *
 * Bug Fix 5, 6, 7 - Correction des donnees Niveau d'etudes et Services
 *
 * Usage: php artisan db:seed --class=EtapSupDataSeeder
 */
class EtapSupDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedStudyLevels();
        $this->seedServices();
    }

    /**
     * Bug 5 Fix: Niveaux d'etudes corrects pour EtapSup
     */
    private function seedStudyLevels(): void
    {
        // Desactiver les anciens layouts (ma-reza)
        Layout::query()->update(['is_published' => false]);

        $studyLevels = [
            ['label' => 'Baccalaureat', 'description' => 'Niveau Baccalaureat ou equivalent', 'is_published' => true],
            ['label' => 'Bac+1', 'description' => 'Premiere annee post-bac', 'is_published' => true],
            ['label' => 'Bac+2 (BTS/DUT)', 'description' => 'Deuxieme annee post-bac, BTS ou DUT', 'is_published' => true],
            ['label' => 'Bac+3 (Licence)', 'description' => 'Licence ou Bachelor', 'is_published' => true],
            ['label' => 'Bac+4 (Master 1)', 'description' => 'Premiere annee de Master', 'is_published' => true],
            ['label' => 'Bac+5 (Master 2)', 'description' => 'Deuxieme annee de Master ou diplome Ingenieur', 'is_published' => true],
            ['label' => 'Bac+6 et plus (Doctorat)', 'description' => 'Doctorat, PhD ou equivalent', 'is_published' => true],
            ['label' => 'Formation professionnelle', 'description' => 'Formation continue ou professionnelle', 'is_published' => true],
        ];

        foreach ($studyLevels as $level) {
            Layout::updateOrCreate(
                ['label' => $level['label']],
                $level
            );
        }

        $this->command->info('Bug 5 Fix: ' . count($studyLevels) . ' niveaux d\'etudes ajoutes/mis a jour');
    }

    /**
     * Bug 7 Fix: Services EtapSup corrects
     * Liste officielle: Orientation | procedure d'admission | procedure Campus France |
     * procedure VISA | AVI | Logement | Accueil-installation-Integration | Accompagnement premium
     */
    private function seedServices(): void
    {
        // Desactiver les anciens services (ma-reza)
        RentalDeposit::query()->update(['is_published' => false]);

        $services = [
            [
                'name' => 'Orientation',
                'description' => 'Accompagnement personnalise pour choisir votre formation et etablissement',
                'is_published' => true,
            ],
            [
                'name' => 'Procedure d\'admission',
                'description' => 'Aide a la constitution de votre dossier de candidature',
                'is_published' => true,
            ],
            [
                'name' => 'Procedure Campus France',
                'description' => 'Accompagnement pour les demarches Campus France',
                'is_published' => true,
            ],
            [
                'name' => 'Procedure VISA',
                'description' => 'Assistance pour l\'obtention de votre visa etudiant',
                'is_published' => true,
            ],
            [
                'name' => 'AVI (Assurance Voyage)',
                'description' => 'Souscription a une assurance voyage internationale',
                'is_published' => true,
            ],
            [
                'name' => 'Logement',
                'description' => 'Recherche et reservation de logement etudiant',
                'is_published' => true,
            ],
            [
                'name' => 'Accueil-Installation-Integration',
                'description' => 'Service d\'accueil a l\'aeroport et aide a l\'installation',
                'is_published' => true,
            ],
            [
                'name' => 'Accompagnement Premium',
                'description' => 'Pack complet incluant tous les services avec suivi personnalise',
                'is_published' => true,
            ],
        ];

        foreach ($services as $service) {
            RentalDeposit::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }

        $this->command->info('Bug 7 Fix: ' . count($services) . ' services EtapSup ajoutes/mis a jour');
    }
}
