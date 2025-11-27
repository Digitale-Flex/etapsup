<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Kdabrow\SeederOnce\SeederOnce;

class AdminSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'surname' => 'Lutte',
                'name' => 'POK',
                'email' => 'lu2tepok@outlook.com',
                'password' => 'w@Z@pok09!',
            ],
            [
                'surname' => 'Mareza',
                'name' => 'Admin',
                'email' => 'admin@ma-reza.com',
                'password' => 'Pa$$dr0w!',
            ],
        ];
        foreach ($users as $user) {
            $user = User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'surname' => $user['surname'],
                    'name' => $user['name'],
                    'password' => bcrypt($user['password']),
                ]
            );

            $user->assignRole('admin');
        }
    }
}
