<?php

namespace App\Console\Commands;

use App\Models\Account\PermissionGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class GeneratePolicies extends Command
{
    protected $signature = 'policies:generate
                            {--force : Overwrite existing policies}
                            {--models= : Comma separated list of specific models to generate}';
    protected $description = 'Generate policies for all models in permission groups';

    // Mapping des actions aux méthodes de policy
    protected $actionMapping = [
        'viewAny' => 'viewAny',
        'view' => 'view',
        'create' => 'create',
        'update' => 'update',
        'delete' => 'delete',
        'restore' => 'restore',
        'forceDelete' => 'forceDelete',
        'publish' => 'publish',
        'approve' => 'approve',
        'export' => 'export',
    ];

    public function handle()
    {
        $models = $this->option('models') ?
            explode(',', $this->option('models')) :
            PermissionGroup::pluck('name')->toArray();

        foreach ($models as $modelName) {
            $this->generatePolicyForModel($modelName);
        }

        $this->info('All policies generated successfully!');
    }

    protected function generatePolicyForModel($modelName)
    {
        // Trouver la classe du modèle en parcourant les namespaces
        $modelClass = $this->findModelClass($modelName);

        if (!$modelClass) {
            $this->error("Model {$modelName} not found. Skipping...");
            return;
        }

        $policyClass = "{$modelName}Policy";
        $policyPath = app_path("Policies/{$policyClass}.php");
        $group = PermissionGroup::where('name', $modelName)->first();

        // Créer le répertoire des policies si nécessaire
        if (!File::exists(app_path('Policies'))) {
            File::makeDirectory(app_path('Policies'));
        }

        // Générer le contenu de la policy
        $policyContent = $this->generatePolicyContent($modelName, $modelClass, $group);

        // Vérifier si la policy existe déjà
        if (File::exists($policyPath)) {
            if ($this->option('force')) {
                File::put($policyPath, $policyContent);
                $this->info("Policy for {$modelName} overwritten.");
            } else {
                $this->updateExistingPolicy($policyPath, $modelName, $group);
                $this->info("Policy for {$modelName} updated with new methods.");
            }
        } else {
            File::put($policyPath, $policyContent);
            $this->info("Policy for {$modelName} created successfully.");
        }
    }

    protected function findModelClass($modelName)
    {
        // Liste des namespaces possibles pour les modèles
        $namespaces = [
            'App\\Models',
            'App\\Models\\RealEstate',
            'App\\Models\\Certificate',
        ];

        // Chercher dans tous les namespaces
        foreach ($namespaces as $namespace) {
            $fullClass = "{$namespace}\\{$modelName}";
            if (class_exists($fullClass)) {
                return $fullClass;
            }
        }

        // Chercher dans les sous-dossiers récursivement
        $modelsPath = app_path('Models');
        $files = File::allFiles($modelsPath);

        foreach ($files as $file) {
            $path = $file->getRelativePathName();
            $class = sprintf('App\\Models\\%s', str_replace(['/', '.php'], ['\\', ''], $path));

            if (class_exists($class) && class_basename($class) === $modelName) {
                return $class;
            }
        }

        return null;
    }

    protected function generatePolicyContent($modelName, $modelClass, $group)
    {
        $permissions = $group ? $group->permissions->pluck('name')->toArray() : [];
        $stub = File::get($this->getStubPath('policy.stub'));

        // Générer les méthodes pour chaque action
        $methods = $this->generatePolicyMethods($modelName, $permissions);

        // Remplacer les placeholders
        return str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ modelNamespace }}',
                '{{ modelVariable }}',
                '{{ methods }}'
            ],
            [
                'App\Policies',
                "{$modelName}Policy",
                $modelClass,
                Str::camel($modelName), // Utiliser camelCase pour la variable
                trim($methods)
            ],
            $stub
        );
    }

    protected function generatePolicyMethods($modelName, $permissions)
    {
        $methods = '';

        foreach ($this->actionMapping as $action => $methodName) {
            $permissionName = "{$action} {$modelName}";

            if (in_array($permissionName, $permissions)) {
                $stubType = $this->methodRequiresInstance($action) ?
                    'policy_method_with_instance' :
                    'policy_method_without_instance';

                $methodStub = File::get($this->getStubPath("{$stubType}.stub"));

                $methods .= str_replace(
                        [
                            '{{ method }}',
                            '{{ permission }}',
                            '{{ model }}',
                            '{{ modelVariable }}'
                        ],
                        [
                            $methodName,
                            $permissionName,
                            $modelName,
                            Str::camel($modelName)
                        ],
                        $methodStub
                    ) . "\n\n";
            }
        }

        return trim($methods);
    }

    protected function methodRequiresInstance($action)
    {
        return !in_array($action, ['viewAny', 'create']);
    }

    protected function updateExistingPolicy($path, $modelName, $group)
    {
        $content = File::get($path);
        $permissions = $group ? $group->permissions->pluck('name')->toArray() : [];

        // Vérifier les méthodes existantes
        $existingMethods = [];
        try {
            $policyClass = "App\\Policies\\{$modelName}Policy";
            if (class_exists($policyClass)) {
                $reflection = new ReflectionClass($policyClass);
                $existingMethods = array_map(
                    fn($method) => $method->name,
                    $reflection->getMethods(\ReflectionMethod::IS_PUBLIC)
                );
            }
        } catch (\Throwable $e) {
            $this->error("Error reflecting policy: " . $e->getMessage());
        }

        // Ajouter les nouvelles méthodes
        $newMethods = '';
        foreach ($this->actionMapping as $action => $methodName) {
            $permissionName = "{$action} {$modelName}";

            if (in_array($permissionName, $permissions) &&
                !in_array($methodName, $existingMethods)) {

                $stubType = $this->methodRequiresInstance($action) ?
                    'policy_method_with_instance' :
                    'policy_method_without_instance';

                $methodStub = File::get($this->getStubPath("{$stubType}.stub"));

                $newMethods .= str_replace(
                        [
                            '{{ method }}',
                            '{{ permission }}',
                            '{{ model }}',
                            '{{ modelVariable }}'
                        ],
                        [
                            $methodName,
                            $permissionName,
                            $modelName,
                            Str::camel($modelName)
                        ],
                        $methodStub
                    ) . "\n\n";
            }
        }

        if ($newMethods) {
            // Insérer avant la dernière accolade fermante
            $content = preg_replace(
                '/\n\s*}\s*$/',
                "\n" . trim($newMethods) . "\n}",
                $content
            );

            File::put($path, $content);
        }
    }

    protected function getStubPath($stubName)
    {
        $customPath = app_path("Console/Commands/stubs/{$stubName}");

        if (File::exists($customPath)) {
            return $customPath;
        }

        // Chemin par défaut si le stub personnalisé n'existe pas
        return base_path("stubs/{$stubName}");
    }
}
