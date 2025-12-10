<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajoute country_id directement dans properties pour cohérence formulaire Filament
     * Le formulaire aura Pays + Ville (ville filtrée par pays)
     */
    public function up(): void
    {
        // 1. Ajouter la colonne country_id
        if (!Schema::hasColumn('properties', 'country_id')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->foreignId('country_id')
                    ->nullable()
                    ->after('city_id')
                    ->constrained('countries')
                    ->nullOnDelete();
            });
        }

        // 2. Copier country_id depuis cities vers properties pour données existantes
        DB::statement('
            UPDATE properties p
            INNER JOIN cities c ON p.city_id = c.id
            SET p.country_id = c.country_id
            WHERE p.country_id IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }
        });
    }
};
