<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Bug 6 Fix: Ajouter le pays de destination (ou l'etudiant veut etudier)
     */
    public function up(): void
    {
        Schema::table('custom_searches', function (Blueprint $table) {
            $table->foreignId('destination_country_id')
                ->nullable()
                ->after('city_id')
                ->constrained('countries')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_searches', function (Blueprint $table) {
            $table->dropForeign(['destination_country_id']);
            $table->dropColumn('destination_country_id');
        });
    }
};
