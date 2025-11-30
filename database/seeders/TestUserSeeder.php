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
        $etudiant = User::firstOrCreate(
            ['email' => 'etudiant@test.com'],
            [
                'surname' => 'Diallo',
                'name' => 'Amina',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_partner_manager' => false,
            ]
        );
        $etudiant->assignRole('user');

        // 2. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'surname' => 'Admin',
                'name' => 'EtapSup',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_partner_manager' => false,
            ]
        );
        $admin->assignRole('admin');

        // 3. Partenaire/Conseiller
        $partenaire = User::firstOrCreate(
            ['email' => 'partenaire@test.com'],
            [
                'surname' => 'Conseiller',
                'name' => 'Orientation',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_partner_manager' => true, // Partenaire d'orientation
            ]
        );
        $partenaire->assignRole('partner');

        $this->command->info('✅ 3 utilisateurs de test créés avec leurs rôles :');
        $this->command->info('   - etudiant@test.com / password (Amina Diallo) [user]');
        $this->command->info('   - admin@test.com / password (Admin EtapSup) [admin]');
        $this->command->info('   - partenaire@test.com / password (Conseiller) [partner]');
    }
}
