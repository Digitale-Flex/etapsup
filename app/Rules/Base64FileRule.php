<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Base64FileRule implements ValidationRule
{
    protected $allowedMimeTypes;

    protected $maxSize;

    public function __construct(array $allowedMimeTypes = [], int $maxSize = 5120)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->maxSize = $maxSize;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Vérifier si la chaîne est un base64 valide
        if (! $this->isValidBase64($value)) {
            $fail('Le fichier :attribute doit être une chaîne base64 valide.');

            return;
        }

        // Décoder les données base64
        $decodedData = base64_decode($value, true);

        if ($decodedData === false) {
            $fail('Le fichier :attribute n\'est pas un base64 valide.');

            return;
        }

        // Vérifier la taille du fichier
        $size = strlen($decodedData);
        if ($size > $this->maxSize * 1024) {
            $fail("Le fichier :attribute ne doit pas dépasser {$this->maxSize} Ko.");

            return;
        }

        // Détecter le type MIME
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMimeType = $finfo->buffer($decodedData);

        // Vérifier si le type MIME est autorisé
        if (! empty($this->allowedMimeTypes) && ! in_array($detectedMimeType, $this->allowedMimeTypes)) {
            $fail("Le type de fichier :attribute ({$detectedMimeType}) n'est pas autorisé.");
        }
    }

    /**
     * Vérifie si la chaîne est un base64 valide.
     *
     * @param string $string
     * @return bool
     */
    protected function isValidBase64(string $string): bool
    {
        // Supprimer les espaces et les retours à la ligne
        $string = preg_replace('/\s+/', '', $string);

        // Vérifier si la longueur est un multiple de 4
        if (strlen($string) % 4 !== 0) {
            return false;
        }

        // Vérifier si la chaîne contient uniquement des caractères base64 valides
        return preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $string) === 1;
    }
}
