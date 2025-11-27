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
        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('iso');
                $table->string('code');
                $table->string('nationality');
                $table->boolean('is_published')->default(false);
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
                $table->string('name');
                $table->string('description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('region_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
                $table->string('name');
                $table->string('budget')->nullable();
                $table->string('description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('locations')) {
            Schema::create('locations', function (Blueprint $table) {
                $table->id();
                $table->morphs('locatable');
                $table->string('price');
                $table->json('addresses')->nullable();
                $table->boolean('is_published')->default(false);
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('countries');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('locations');
        Schema::enableForeignKeyConstraints();
    }
};
