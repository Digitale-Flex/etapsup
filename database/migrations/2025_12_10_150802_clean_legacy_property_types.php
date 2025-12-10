<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A05: Nettoyer types immobilier legacy ma-Reza + doublons "Université"
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('property_types')
            ->whereIn('id', [7, 8, 9, 10, 11, 12, 13, 14, 15])
            ->delete();

        // IDs supprimés:
        // 7-10: Doublons "Université"
        // 11: Appartement (legacy ma-Reza)
        // 12: Maison (legacy ma-Reza)
        // 13: Villa (legacy ma-Reza)
        // 14: Chalet (legacy ma-Reza)
        // 15: Péniche (legacy ma-Reza)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback - suppression définitive
        // Les types EtapSup valides restent: 1-6
    }
};
