<?php

namespace Database\Seeders;

use App\Models\RealEstate\Category;
use App\Models\RealEstate\Equipment;
use App\Models\RealEstate\Layout;
use App\Models\RealEstate\PropertyType;
use App\Models\RealEstate\Regulation;
use Kdabrow\SeederOnce\SeederOnce;

class LayoutSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['label' => 'Colocation longue durée', 'is_published' => true],
            ['label' => 'Location professionnelle', 'is_published' => true],
            ['label' => 'Location moyenne durée', 'is_published' => true],
        ];
        foreach ($categories as $category) {
            Category::updateOrCreate(['label' => $category['label']], $category);
        }

        $types = [
            ['label' => 'Appartement', 'is_published' => true],
            ['label' => 'Maison', 'is_published' => true],
            ['label' => 'Villa', 'is_published' => true],
            ['label' => 'Chalet', 'is_published' => true],
            ['label' => 'Péniche', 'is_published' => true],
        ];
        foreach ($types as $type) {
            PropertyType::updateOrCreate(['label' => $type['label']], $type);
        }

        $regulations = [
            ['label' => 'Animaux de compagnie', 'is_published' => true],
            ['label' => 'Fumeurs non autorisés', 'is_published' => true],
        ];
        foreach ($regulations as $regulation) {
            Regulation::updateOrCreate(['label' => $regulation['label']], $regulation);
        }

        $equipments = [
            ['label' => 'Accès Internet ou Wifi', 'is_published' => true],
            ['label' => 'Parking', 'is_published' => true],
            ['label' => 'Climatisation', 'is_published' => true],
            ['label' => 'Cuisine équipée', 'is_published' => true],
            ['label' => 'Four', 'is_published' => true],
            ['label' => 'Micro-onde', 'is_published' => true],
            ['label' => 'Lave-linges', 'is_published' => true],
            ['label' => 'Sèche-linge', 'is_published' => true],
            ['label' => 'TV', 'is_published' => true],
            ['label' => 'Lit', 'is_published' => true],
            ['label' => 'Lit bébé', 'is_published' => true],
            ['label' => 'Fer', 'is_published' => true],
            ['label' => 'Fer et planche à repasser', 'is_published' => true],
            ['label' => 'Chaise haute', 'is_published' => true],
        ];
        foreach ($equipments as $equipment) {
            Equipment::updateOrCreate(['label' => $equipment['label']], $equipment);
        }

        $layouts = [
            ['label' => 'Parking', 'description' => "Place de parking attribuée ou disponible pour les occupants de l'appartement.", 'is_published' => true],
            ['label' => 'Balcon / Terrasse', 'description' => "Espace extérieur privatif, généralement attenant à l'appartement, pour se détendre ou profiter de l'extérieur.", 'is_published' => true],
            ['label' => 'Buanderie', 'description' => 'Espace réservé pour la machine à laver et le séchage du linge.', 'is_published' => true],
            ['label' => 'Ascenseur', 'description' => "Présence d'un ascenseur pour faciliter l'accès aux différents étages de l'immeuble.", 'is_published' => true],
            ['label' => 'Piscine', 'description' => 'Piscine commune ou privée disponible pour les résidents.', 'is_published' => true],
            ['label' => 'Salle de sport', 'description' => "Espace équipé de matériel de sport pour les résidents de l'immeuble.", 'is_published' => true],
            ['label' => 'Chauffage central', 'description' => "Système de chauffage central alimentant tous les appartements de l'immeuble.", 'is_published' => true],
            ['label' => 'Sécurité 24/24', 'description' => "Service de sécurité assurant une surveillance continue de l'immeuble.", 'is_published' => true],
            ['label' => 'Accès handicapé', 'description' => 'Aménagements permettant un accès facile aux personnes à mobilité réduite.', 'is_published' => true],
            ['label' => 'Vue panoramique', 'description' => "Vue étendue depuis l'appartement, souvent sur la ville, la mer ou les montagnes.", 'is_published' => true],
            ['label' => 'Service de conciergerie', 'description' => 'Service proposant diverses prestations telles que la réception des colis, la réservation de taxis, etc.', 'is_published' => true],
            ['label' => "Système d'alarme", 'description' => "Système d'alarme installé dans l'appartement pour assurer la sécurité des occupants.", 'is_published' => true],
        ];

        foreach ($layouts as $layout) {
            Layout::updateOrCreate(['label' => $layout['label']], $layout);
        }
    }
}
