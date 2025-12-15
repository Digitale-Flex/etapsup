<?php

namespace Database\Seeders;

use App\Models\Account\PermissionGroup;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeder des permissions pour EtapSup
 * Crée les groupes de permissions et les permissions associées
 */
class PermissionSeeder extends Seeder
{
    /**
     * Actions CRUD standard pour chaque modèle
     */
    private array $crudActions = [
        'view' => 'Voir',
        'view_any' => 'Lister',
        'create' => 'Créer',
        'update' => 'Modifier',
        'delete' => 'Supprimer',
        'restore' => 'Restaurer',
        'force_delete' => 'Supprimer définitivement',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🔄 Création des groupes de permissions EtapSup...');

        // ============================================
        // GROUPES ET PERMISSIONS ETAPSUP
        // ============================================

        $permissionGroups = [
            // Candidatures (coeur de métier EtapSup)
            'Application' => [
                'label' => 'Candidatures',
                'permissions' => [
                    'view_applications' => 'Voir une candidature',
                    'view_any_applications' => 'Lister les candidatures',
                    'create_applications' => 'Créer une candidature',
                    'update_applications' => 'Modifier une candidature',
                    'delete_applications' => 'Supprimer une candidature',
                    'restore_applications' => 'Restaurer une candidature',
                    'force_delete_applications' => 'Supprimer définitivement',
                    'validate_applications' => 'Valider une candidature',
                    'export_applications' => 'Exporter les candidatures',
                ],
            ],

            // Établissements (écoles partenaires)
            'Establishment' => [
                'label' => 'Établissements',
                'permissions' => [
                    'view_establishments' => 'Voir un établissement',
                    'view_any_establishments' => 'Lister les établissements',
                    'create_establishments' => 'Créer un établissement',
                    'update_establishments' => 'Modifier un établissement',
                    'delete_establishments' => 'Supprimer un établissement',
                    'restore_establishments' => 'Restaurer un établissement',
                    'force_delete_establishments' => 'Supprimer définitivement',
                    'publish_establishments' => 'Publier un établissement',
                ],
            ],

            // Programmes d'études
            'Program' => [
                'label' => 'Programmes d\'études',
                'permissions' => [
                    'view_programs' => 'Voir un programme',
                    'view_any_programs' => 'Lister les programmes',
                    'create_programs' => 'Créer un programme',
                    'update_programs' => 'Modifier un programme',
                    'delete_programs' => 'Supprimer un programme',
                    'restore_programs' => 'Restaurer un programme',
                    'force_delete_programs' => 'Supprimer définitivement',
                    'publish_programs' => 'Publier un programme',
                ],
            ],

            // Utilisateurs (étudiants, gestionnaires)
            'User' => [
                'label' => 'Utilisateurs',
                'permissions' => [
                    'view_users' => 'Voir un utilisateur',
                    'view_any_users' => 'Lister les utilisateurs',
                    'create_users' => 'Créer un utilisateur',
                    'update_users' => 'Modifier un utilisateur',
                    'delete_users' => 'Supprimer un utilisateur',
                    'restore_users' => 'Restaurer un utilisateur',
                    'force_delete_users' => 'Supprimer définitivement',
                    'impersonate_users' => 'Se connecter en tant que',
                ],
            ],

            // Partenaires (écoles)
            'Partner' => [
                'label' => 'Partenaires',
                'permissions' => [
                    'view_partners' => 'Voir un partenaire',
                    'view_any_partners' => 'Lister les partenaires',
                    'create_partners' => 'Créer un partenaire',
                    'update_partners' => 'Modifier un partenaire',
                    'delete_partners' => 'Supprimer un partenaire',
                    'restore_partners' => 'Restaurer un partenaire',
                    'force_delete_partners' => 'Supprimer définitivement',
                ],
            ],

            // Rôles & Permissions
            'Role' => [
                'label' => 'Rôles & Permissions',
                'permissions' => [
                    'view_roles' => 'Voir un rôle',
                    'view_any_roles' => 'Lister les rôles',
                    'create_roles' => 'Créer un rôle',
                    'update_roles' => 'Modifier un rôle',
                    'delete_roles' => 'Supprimer un rôle',
                    'view_permissions' => 'Voir les permissions',
                    'view_any_permissions' => 'Lister les permissions',
                    'update_permissions' => 'Modifier les permissions',
                ],
            ],

            // Paramètres système
            'Settings' => [
                'label' => 'Paramètres',
                'permissions' => [
                    'view_settings' => 'Voir les paramètres',
                    'update_settings' => 'Modifier les paramètres',
                    'view_general_settings' => 'Voir paramètres généraux',
                    'update_general_settings' => 'Modifier paramètres généraux',
                ],
            ],

            // Dashboard & Analytics
            'Dashboard' => [
                'label' => 'Tableau de bord',
                'permissions' => [
                    'view_dashboard' => 'Voir le tableau de bord',
                    'view_analytics' => 'Voir les statistiques',
                    'export_data' => 'Exporter les données',
                ],
            ],

            // Factures & Paiements
            'Invoice' => [
                'label' => 'Factures & Paiements',
                'permissions' => [
                    'view_invoices' => 'Voir une facture',
                    'view_any_invoices' => 'Lister les factures',
                    'create_invoices' => 'Créer une facture',
                    'update_invoices' => 'Modifier une facture',
                    'delete_invoices' => 'Supprimer une facture',
                    'view_payments' => 'Voir les paiements',
                    'refund_payments' => 'Rembourser un paiement',
                ],
            ],

            // Documents & Attestations
            'Document' => [
                'label' => 'Documents',
                'permissions' => [
                    'view_documents' => 'Voir les documents',
                    'view_any_documents' => 'Lister les documents',
                    'create_documents' => 'Créer un document',
                    'update_documents' => 'Modifier un document',
                    'delete_documents' => 'Supprimer un document',
                    'download_documents' => 'Télécharger les documents',
                ],
            ],
        ];

        $groupCount = 0;
        $permissionCount = 0;

        foreach ($permissionGroups as $groupName => $groupData) {
            // Créer ou mettre à jour le groupe
            $group = PermissionGroup::updateOrCreate(
                ['name' => $groupName],
                ['label' => $groupData['label']]
            );
            $groupCount++;

            // Créer les permissions du groupe
            foreach ($groupData['permissions'] as $permissionName => $permissionLabel) {
                Permission::updateOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    [
                        'label' => $permissionLabel,
                        'permission_group_id' => $group->id,
                    ]
                );
                $permissionCount++;
            }

            $this->command->info("  ✅ Groupe '{$groupData['label']}' - " . count($groupData['permissions']) . " permissions");
        }

        $this->command->newLine();
        $this->command->info("🎉 Terminé: {$groupCount} groupes, {$permissionCount} permissions créées");
    }
}
