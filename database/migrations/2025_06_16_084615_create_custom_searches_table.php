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
        Schema::create('custom_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->foreignId('partner_id')->constrained();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('budget');
            $table->date('rental_start');
            $table->string('duration')->nullable();
            $table->text('note')->nullable();
            $table->string('stripe_payment_intent')->nullable();
            $table->string('paid')->nullable();
            $table->string('state')->default('payment_pending');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('custom_search_rental_deposit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_search_id');
            $table->unsignedBigInteger('rental_deposit_id');

            $table->foreign('custom_search_id', 'fk_cs_rental_deposit_cs')->references('id')->on('custom_searches')->onDelete('cascade');
            $table->foreign('rental_deposit_id', 'fk_cs_rental_deposit_rd')->references('id')->on('rental_deposits')->onDelete('cascade');
        });

        Schema::create('custom_search_layout', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_search_id');
            $table->unsignedBigInteger('layout_id');

            $table->foreign('custom_search_id', 'fk_cs_layout_cs')->references('id')->on('custom_searches')->onDelete('cascade');
            $table->foreign('layout_id', 'fk_cs_layout_l')->references('id')->on('layouts')->onDelete('cascade');
        });

        Schema::create('custom_search_property_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_search_id');
            $table->unsignedBigInteger('property_type_id');

            $table->foreign('custom_search_id', 'fk_cs_property_type_cs')->references('id')->on('custom_searches')->onDelete('cascade');
            $table->foreign('property_type_id', 'fk_cs_property_type_pt')->references('id')->on('property_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('custom_searches');
        Schema::dropIfExists('custom_search_rental_deposit');
        Schema::dropIfExists('custom_search_property_type');
        Schema::enableForeignKeyConstraints();
    }
};
