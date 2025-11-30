<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Feature 9 - Sprint 1: Accompagnement premium personnalisé
     * Ajoute les champs pour gérer l'accompagnement payant (299€)
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('accompagnement_premium')->default(false)->after('stripe_payment_intent_id');
            $table->boolean('accompagnement_paid')->default(false)->after('accompagnement_premium');
            $table->string('accompagnement_stripe_payment_intent_id')->nullable()->after('accompagnement_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['accompagnement_premium', 'accompagnement_paid', 'accompagnement_stripe_payment_intent_id']);
        });
    }
};
