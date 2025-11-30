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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('study_field_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained('sub_categories');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('degree_level', ['Licence', 'Master', 'Doctorat', 'Certificat', 'Diplôme']);
            $table->integer('duration')->comment('Durée en semestres');
            $table->string('language')->default('Français');
            $table->decimal('tuition_fee', 10, 2)->nullable()->comment('Frais de scolarité');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
