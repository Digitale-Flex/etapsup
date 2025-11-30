<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Crée 3 utilisateurs de test pour EtapSup :
     * - 1 étudiant
     * - 1 admin
     * - 1 partenaire/conseiller
     */
    public function run(): void
    {
        // 1. Étudiant
        User::firstOrCreate(
            ['email' => 'etudiant@test.com'],
            [
                'surname' => 'Diallo',
                'name' => 'Amina',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_partner_manager' => false,
            ]
        );

        // 2. Admin
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'surname' => 'Admin',
                'name' => 'EtapSup',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_partner_manager' => false,
            ]
        );

        // 3. Partenaire/Conseiller
        User::firstOrCreate(
            ['email' => 'partenaire@test.com'],
            [
                'surname' => 'Conseiller',
                'name' => 'Orientation',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_partner_manager' => true, // Partenaire d'orientation
            ]
        );

        $this->command->info('✅ 3 utilisateurs de test créés :');
        $this->command->info('   - etudiant@test.com / password (Amina Diallo)');
        $this->command->info('   - admin@test.com / password (Admin EtapSup)');
        $this->command->info('   - partenaire@test.com / password (Conseiller)');
    }
}
