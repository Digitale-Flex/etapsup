<?php

namespace Database\Seeders;

use App\Models\Account\PermissionGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    private $modelPermissions = [
        'viewAny' => 'Lister',
        'view' => 'Voir',
        'create' => 'Créer',
        'update' => 'Modifier',
        'delete' => 'Supprimer',
        'restore' => 'Restaurer',
        'forceDelete' => 'Supprimer définitivement'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            'Property' => 'Logements',
            'Reservation' => 'Réservations',
            'CertificateRequest' => "Demandes d'attestation",
            'CustomSearch' => "Recherche personnalisées",

            'Partner' => 'Partenaires',
            'Coupon' => 'Coupons',

            'Category' => 'Catégories',
            'Equipment' => 'Equipment',
            'Layout' => 'Aménagements',
            'Regulation' => 'Règlements Intérieurs',

            'Region' => 'Régions',
            'City' => 'Ville',
            'User' => 'Utilisateurs',
            // 'RentalDeposit' => 'Cautionnement locatif'
            // 'PaymentProof' => 'Preuves de paiement',
            // 'Genre' => 'Type de logement'
            //  'Location' => 'Adresses',
            //  'PropertyType' => 'Type de logement',
        ];

        $customPermissions = [
            'publish' => 'Publier',
            'approve' => 'Approuver',
            'export' => 'Exporter',
        ];

        foreach ($models as $model => $modelLabel) {
            $group = PermissionGroup::updateOrCreate(
                ['name' => $model],
                ['label' => $modelLabel]
            );

            foreach ($this->modelPermissions as $action => $actionLabel) {
                $name = "{$action} {$model}";
                $label = "{$actionLabel}  {$modelLabel}";

                Permission::updateOrCreate(
                    ['name' => $name],
                    [
                        'label' => $label,
                        'permission_group_id' => $group->id,
                        'guard_name' => 'web'
                    ]
                );
            }
        }

       /*    $name = "{$action} {$model}";
            $label = "{$actionLabel} {$modelLabel}";

            Permission::updateOrCreate(
                ['name' => Str::kebab($name)],
                [
                    'label' => $label,
                    'permission_group_id' => $group->id,
                    'guard_name' => 'web'
                ]
            );
        } */
    }
}
