<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère existante
            $table->dropForeign(['region_id']);
            $table->dropIndex('cities_region_id_index');

            // Renommer la colonne region_id en country_id
            $table->renameColumn('region_id', 'country_id');

            // Ajouter la nouvelle contrainte de clé étrangère vers countries
            $table->foreign('country_id')
                  ->references('id')
                  ->on('countries')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Supprimer la contrainte vers countries
            $table->dropForeign(['country_id']);

            // Renommer country_id en region_id
            $table->renameColumn('country_id', 'region_id');

            // Restaurer la contrainte vers regions
            $table->foreign('region_id')
                  ->references('id')
                  ->on('regions')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }
};
