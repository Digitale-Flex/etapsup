<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix pour : SQLSTATE[42S22]: Column not found: 1054 Unknown column 'frais_dossier' in 'field list'
     * Ajoute les colonnes nécessaires pour les établissements scolaires (EtapSup)
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Informations de contact
            if (!Schema::hasColumn('properties', 'website')) {
                $table->string('website')->nullable()->after('description');
            }
            if (!Schema::hasColumn('properties', 'phone')) {
                $table->string('phone')->nullable()->after('website');
            }
            if (!Schema::hasColumn('properties', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }

            // Statistiques établissement
            if (!Schema::hasColumn('properties', 'student_count')) {
                $table->unsignedInteger('student_count')->nullable()->after('email');
            }
            if (!Schema::hasColumn('properties', 'ranking')) {
                $table->unsignedInteger('ranking')->nullable()->after('student_count');
            }

            // Frais de scolarité
            if (!Schema::hasColumn('properties', 'tuition_min')) {
                $table->decimal('tuition_min', 10, 2)->nullable()->after('ranking');
            }
            if (!Schema::hasColumn('properties', 'tuition_max')) {
                $table->decimal('tuition_max', 10, 2)->nullable()->after('tuition_min');
            }
            if (!Schema::hasColumn('properties', 'commission')) {
                $table->decimal('commission', 10, 2)->nullable()->after('tuition_max');
            }
            if (!Schema::hasColumn('properties', 'frais_dossier')) {
                $table->decimal('frais_dossier', 10, 2)->nullable()->after('commission');
            }
            if (!Schema::hasColumn('properties', 'acompte_scolarite')) {
                $table->decimal('acompte_scolarite', 10, 2)->nullable()->after('frais_dossier');
            }

            // Foreign Keys vers Settings (Sprint 1 PRD)
            if (!Schema::hasColumn('properties', 'establishment_type_id')) {
                $table->foreignId('establishment_type_id')->nullable()->after('category_id')
                    ->constrained('establishment_types')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('properties', 'training_type_id')) {
                $table->foreignId('training_type_id')->nullable()->after('establishment_type_id')
                    ->constrained('training_types')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('properties', 'career_field_id')) {
                $table->foreignId('career_field_id')->nullable()->after('training_type_id')
                    ->constrained('career_fields')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('properties', 'degree_level_id')) {
                $table->foreignId('degree_level_id')->nullable()->after('career_field_id')
                    ->constrained('degree_levels')
                    ->nullOnDelete();
            }

            // Sections de contenu (Sprint 1)
            if (!Schema::hasColumn('properties', 'section_presentation')) {
                $table->longText('section_presentation')->nullable()->after('description');
            }
            if (!Schema::hasColumn('properties', 'section_prerequis')) {
                $table->longText('section_prerequis')->nullable()->after('section_presentation');
            }
            if (!Schema::hasColumn('properties', 'section_conditions_financieres')) {
                $table->longText('section_conditions_financieres')->nullable()->after('section_prerequis');
            }
            if (!Schema::hasColumn('properties', 'section_specialisation')) {
                $table->longText('section_specialisation')->nullable()->after('section_conditions_financieres');
            }
            if (!Schema::hasColumn('properties', 'section_campus')) {
                $table->longText('section_campus')->nullable()->after('section_specialisation');
            }

            // Sub-category (spécialisation)
            if (!Schema::hasColumn('properties', 'sub_category_id')) {
                $table->foreignId('sub_category_id')->nullable()->after('category_id')
                    ->constrained('sub_categories')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Supprimer les foreign keys d'abord
            if (Schema::hasColumn('properties', 'establishment_type_id')) {
                $table->dropForeign(['establishment_type_id']);
                $table->dropColumn('establishment_type_id');
            }

            if (Schema::hasColumn('properties', 'training_type_id')) {
                $table->dropForeign(['training_type_id']);
                $table->dropColumn('training_type_id');
            }

            if (Schema::hasColumn('properties', 'career_field_id')) {
                $table->dropForeign(['career_field_id']);
                $table->dropColumn('career_field_id');
            }

            if (Schema::hasColumn('properties', 'degree_level_id')) {
                $table->dropForeign(['degree_level_id']);
                $table->dropColumn('degree_level_id');
            }

            if (Schema::hasColumn('properties', 'sub_category_id')) {
                $table->dropForeign(['sub_category_id']);
                $table->dropColumn('sub_category_id');
            }

            // Supprimer les autres colonnes
            $table->dropColumn([
                'website',
                'phone',
                'email',
                'student_count',
                'ranking',
                'tuition_min',
                'tuition_max',
                'commission',
                'frais_dossier',
                'acompte_scolarite',
                'section_presentation',
                'section_prerequis',
                'section_conditions_financieres',
                'section_specialisation',
                'section_campus',
            ]);
        });
    }
};
