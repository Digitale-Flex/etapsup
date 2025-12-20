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
        Schema::table('custom_searches', function (Blueprint $table) {
            // Question 4: Sexe (M/F)
            $table->enum('gender', ['M', 'F'])->nullable()->after('state');

            // Question 6: Date d'expiration du passeport
            $table->date('passport_expiry_date')->nullable()->after('gender');

            // Question 7: Adresse complète
            $table->text('address')->nullable()->after('passport_expiry_date');

            // Question 10: Niveau d'études actuel (référence vers degree_levels)
            $table->foreignId('current_level_id')->nullable()->after('address')
                ->constrained('degree_levels')->nullOnDelete();

            // Question 13: Langue d'enseignement préférée
            $table->enum('preferred_language', ['FR', 'EN'])->nullable()->after('current_level_id');

            // Question 16: Procédure Campus France antérieure
            $table->boolean('has_campus_france_experience')->default(false)->after('preferred_language');

            // Questions 17-21: Documents disponibles (optionnels)
            $table->boolean('has_diploma')->default(false)->after('has_campus_france_experience');
            $table->boolean('has_transcript')->default(false)->after('has_diploma');
            $table->boolean('has_cv')->default(false)->after('has_transcript');
            $table->boolean('has_motivation_letter')->default(false)->after('has_cv');
            $table->boolean('has_conduct_certificate')->default(false)->after('has_motivation_letter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_searches', function (Blueprint $table) {
            $table->dropForeign(['current_level_id']);
            $table->dropColumn([
                'gender',
                'passport_expiry_date',
                'address',
                'current_level_id',
                'preferred_language',
                'has_campus_france_experience',
                'has_diploma',
                'has_transcript',
                'has_cv',
                'has_motivation_letter',
                'has_conduct_certificate',
            ]);
        });
    }
};
