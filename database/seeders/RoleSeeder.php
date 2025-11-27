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
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['display_name' => $role['display_name']]
            );
        }
    }
}
