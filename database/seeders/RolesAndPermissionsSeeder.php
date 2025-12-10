<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Initialise les rôles et permissions pour EtapSup
     * Fix bugs A33-A34: Création impossible de rôles/permissions
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🔄 Initialisation des permissions et rôles...');

        // ============================================
        // 1. CRÉER LES PERMISSIONS
        // ============================================

        $permissions = [
            // Candidatures (Applications)
            'view_applications',
            'view_any_applications',
            'create_applications',
            'update_applications',
            'delete_applications',
            'restore_applications',
            'force_delete_applications',
            'validate_applications', // Valider une candidature

            // Établissements (Properties)
            'view_establishments',
            'view_any_establishments',
            'create_establishments',
            'update_establishments',
            'delete_establishments',
            'restore_establishments',
            'force_delete_establishments',
            'publish_establishments', // Publier un établissement

            // Programmes
            'view_programs',
            'view_any_programs',
            'create_programs',
            'update_programs',
            'delete_programs',
            'publish_programs',

            // Utilisateurs
            'view_users',
            'view_any_users',
            'create_users',
            'update_users',
            'delete_users',
            'impersonate_users', // Se connecter en tant qu'un utilisateur

            // Rôles & Permissions
            'view_roles',
            'view_any_roles',
            'create_roles',
            'update_roles',
            'delete_roles',
            'view_permissions',
            'view_any_permissions',
            'create_permissions',
            'update_permissions',
            'delete_permissions',

            // Paramètres
            'view_settings',
            'update_settings',
            'view_general_settings',
            'update_general_settings',

            // Dashboard & Analytics
            'view_dashboard',
            'view_analytics',
            'export_data',

            // Partenaires
            'view_partners',
            'view_any_partners',
            'create_partners',
            'update_partners',
            'delete_partners',

            // Factures & Paiements
            'view_invoices',
            'view_any_invoices',
            'create_invoices',
            'update_invoices',
            'delete_invoices',
            'view_payments',
            'refund_payments',
        ];

        $count = 0;
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
            $count++;
        }

        $this->command->info("✅ {$count} permissions créées");

        // ============================================
        // 2. CRÉER OU METTRE À JOUR LES RÔLES
        // ============================================

        // ROLE: ADMIN (tous les droits)
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
        $adminRole->syncPermissions(Permission::all());
        $this->command->info("✅ Rôle 'admin' configuré avec tous les droits");

        // ROLE: DEV (équivalent admin pour développeurs)
        $devRole = Role::firstOrCreate(
            ['name' => 'dev'],
            ['guard_name' => 'web']
        );
        $devRole->syncPermissions(Permission::all());
        $this->command->info("✅ Rôle 'dev' configuré avec tous les droits");

        // ROLE: PARTNER (école/établissement)
        $partnerRole = Role::firstOrCreate(
            ['name' => 'partner'],
            ['guard_name' => 'web']
        );
        $partnerRole->syncPermissions([
            'view_applications',
            'update_applications',
            'validate_applications',
            'view_establishments',
            'update_establishments',
            'view_programs',
            'create_programs',
            'update_programs',
            'delete_programs',
            'view_dashboard',
            'view_invoices',
        ]);
        $this->command->info("✅ Rôle 'partner' configuré");

        // ROLE: USER (étudiant)
        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['guard_name' => 'web']
        );
        $userRole->syncPermissions([
            'view_applications',
            'create_applications',
            'view_establishments',
            'view_programs',
            'view_dashboard',
            'view_invoices',
        ]);
        $this->command->info("✅ Rôle 'user' configuré");

        // ROLE: GESTIONNAIRE (employé EtapSup)
        $gestionnaireRole = Role::firstOrCreate(
            ['name' => 'gestionnaire'],
            ['guard_name' => 'web']
        );
        $gestionnaireRole->syncPermissions([
            'view_any_applications',
            'view_applications',
            'update_applications',
            'validate_applications',
            'view_any_establishments',
            'view_establishments',
            'update_establishments',
            'view_any_programs',
            'view_programs',
            'view_any_users',
            'view_users',
            'view_dashboard',
            'view_analytics',
        ]);
        $this->command->info("✅ Rôle 'gestionnaire' configuré");

        // ============================================
        // 3. ASSIGNER ADMIN AUX UTILISATEURS EXISTANTS
        // ============================================

        $adminUsers = \App\Models\User::whereIn('email', [
            'contact@yod-invest.fr',
            'admin@etapsup.com',
        ])->get();

        foreach ($adminUsers as $user) {
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
                $this->command->info("✅ Rôle admin assigné à: {$user->email}");
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 Rôles et permissions initialisés avec succès !');
        $this->command->info("📊 Total: " . Permission::count() . " permissions, " . Role::count() . " rôles");
    }
}
