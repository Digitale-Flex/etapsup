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
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->text('description')->nullable();
                $table->integer('min_duration')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('property_types')) {
            Schema::create('property_types', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->text('description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('regulations')) {
            Schema::create('regulations', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->text('description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('equipments')) {
            Schema::create('equipments', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->text('description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('layouts')) {
            Schema::create('layouts', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->text('description')->nullable();
                $table->boolean('is_published')->default(false);
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
        Schema::dropIfExists('categories');
        Schema::dropIfExists('property_types');
        Schema::dropIfExists('regulations');
        Schema::dropIfExists('equipments');
        Schema::dropIfExists('layouts');
    }
};
