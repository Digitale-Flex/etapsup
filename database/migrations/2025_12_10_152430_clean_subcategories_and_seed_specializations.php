<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A06: Nettoyer sub_categories legacy + seed spécialisations académiques
     */
    public function up(): void
    {
        // Désactiver les contraintes FK temporairement
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Supprimer les 3 sous-catégories immobilières ma-Reza
        \Illuminate\Support\Facades\DB::table('sub_categories')->truncate();

        // Seed spécialisations académiques réelles pour EtapSup
        $specializations = [
            ['label' => 'Marketing Digital', 'description' => 'Stratégies marketing en ligne', 'is_published' => true],
            ['label' => 'Finance d\'Entreprise', 'description' => 'Gestion financière et comptabilité', 'is_published' => true],
            ['label' => 'Intelligence Artificielle', 'description' => 'IA et Machine Learning', 'is_published' => true],
            ['label' => 'Développement Web', 'description' => 'Programmation et développement web', 'is_published' => true],
            ['label' => 'Commerce International', 'description' => 'Import-export et commerce mondial', 'is_published' => true],
            ['label' => 'Ressources Humaines', 'description' => 'Gestion du personnel', 'is_published' => true],
            ['label' => 'Gestion de Projet', 'description' => 'Management de projets', 'is_published' => true],
            ['label' => 'Data Science', 'description' => 'Science des données', 'is_published' => true],
            ['label' => 'Cybersécurité', 'description' => 'Sécurité informatique', 'is_published' => true],
            ['label' => 'Entrepreneuriat', 'description' => 'Création et gestion d\'entreprise', 'is_published' => true],
        ];

        foreach ($specializations as $spec) {
            \Illuminate\Support\Facades\DB::table('sub_categories')->insert([
                'label' => $spec['label'],
                'description' => $spec['description'],
                'is_published' => $spec['is_published'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Réactiver les contraintes FK
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback - données legacy supprimées définitivement
    }
};
