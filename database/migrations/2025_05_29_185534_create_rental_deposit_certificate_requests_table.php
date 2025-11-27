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
        Schema::create('certificate_request_rental_deposit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('certificate_request_id');
            $table->unsignedBigInteger('rental_deposit_id');

            // Contraintes avec noms courts
            $table->foreign('certificate_request_id', 'fk_cert_req')
                ->references('id')
                ->on('certificate_requests')
                ->onDelete('cascade');

            $table->foreign('rental_deposit_id', 'fk_rent_dep')
                ->references('id')
                ->on('rental_deposits')
                ->onDelete('cascade');
        });

        Schema::table('certificate_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('rental_deposit_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_request_rental_deposit');
        Schema::table('certificate_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('rental_deposit_id')->nullable(false)->change();
        });
    }
};
