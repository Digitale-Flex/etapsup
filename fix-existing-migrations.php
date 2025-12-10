<?php
/**
 * Script pour marquer les migrations déjà exécutées sur dev.etapsup.org
 *
 * Usage: php fix-existing-migrations.php
 *
 * Ce script vérifie quelles tables existent déjà en base et marque
 * les migrations correspondantes comme exécutées.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "════════════════════════════════════════════════════════════\n";
echo "  🔧 FIX: Marquer les migrations existantes\n";
echo "════════════════════════════════════════════════════════════\n\n";

// Migrations à vérifier (nom de migration => nom de table)
$migrationsToCheck = [
    '2025_11_30_184937_create_personal_access_tokens_table' => 'personal_access_tokens',
    '2025_12_10_133827_create_failed_jobs_table' => 'failed_jobs',
];

$fixed = 0;

foreach ($migrationsToCheck as $migration => $table) {
    echo "Vérification: $table... ";

    // Vérifier si la table existe
    if (Schema::hasTable($table)) {
        echo "✅ existe\n";

        // Vérifier si la migration est déjà enregistrée
        $exists = DB::table('migrations')
            ->where('migration', $migration)
            ->exists();

        if ($exists) {
            echo "  → Déjà dans migrations ✓\n";
        } else {
            echo "  → Ajout dans migrations... ";
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => 1
            ]);
            echo "✅ FAIT\n";
            $fixed++;
        }
    } else {
        echo "❌ n'existe pas (migration sera exécutée)\n";
    }
}

echo "\n════════════════════════════════════════════════════════════\n";
echo "  ✅ Terminé ! $fixed migration(s) marquée(s)\n";
echo "════════════════════════════════════════════════════════════\n\n";

echo "Vous pouvez maintenant exécuter:\n";
echo "  php artisan migrate --force\n\n";
