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
        if (! Schema::hasTable('reservations')) {
            Schema::create('reservations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->integer('guests')->nullable();
                $table->string('status')->nullable();
                $table->string('reason')->nullable();
                $table->text('address')->nullable();
                $table->json('fees')->nullable();
                $table->string('price');
                $table->date('start_date');
                $table->date('end_date');
                $table->text('notes')->nullable();
                $table->string('state')->nullable();
                $table->string('stripe_payment_intent_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
