<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Kdabrow\SeederOnce\SeederOnce;
use Spatie\Permission\Models\Role;

class RoleSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sprint1 Update: Feature 1.6.1 — Ajout rôle 'manager' (Gestionnaire)
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrateur',
            ],
            [
                'name' => 'user',
                'display_name' => 'Utilisateur',
            ],
            [
                'name' => 'partner',
                'display_name' => 'Partenaire',
            ],
            [
                'name' => 'manager',
                'display_name' => 'Gestionnaire',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['display_name' => $role['display_name']]
            );
        }
    }
}
