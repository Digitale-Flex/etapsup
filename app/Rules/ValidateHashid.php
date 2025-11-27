<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ValidateHashid implements ValidationRule
{
    public function __construct(
        protected readonly string $modelClass
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Si c'est un tableau, valider chaque élément
        $values = is_array($value) ? $value : [$value];

        if (count($values) === 0) {
            $fail('Ce champs ne peut être vide.');
            return;
        }

        if (!class_exists($this->modelClass) || !is_subclass_of($this->modelClass, Model::class)) {
            $fail('Configuration de validation invalide.');
            return;
        }

        foreach ($values as $val) {
            if (($this->modelClass)::findByHashid($val) === null) {
                $fail('Un élément dans :attribute est invalide.');
                return;
            }
        }
    }
}
