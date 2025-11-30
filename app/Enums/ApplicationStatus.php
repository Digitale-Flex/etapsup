<?php

namespace App\Enums;

/**
 * Enum pour les statuts de candidature
 *
 * Centralise tous les statuts possibles et leurs traductions françaises
 * pour assurer la cohérence dans toute l'application.
 */
enum ApplicationStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PENDING_PAYMENT = 'pending_payment';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case IN_REVIEW = 'in_review';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case WITHDRAWN = 'withdrawn';

    /**
     * Retourne le label en français pour le statut
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::PENDING => 'En attente',
            self::PENDING_PAYMENT => 'En attente de paiement',
            self::SUBMITTED => 'Soumise',
            self::UNDER_REVIEW, self::IN_REVIEW => 'En examen',
            self::ACCEPTED => 'Acceptée',
            self::REJECTED => 'Refusée',
            self::CANCELLED => 'Annulée',
            self::WITHDRAWN => 'Retirée',
        };
    }

    /**
     * Retourne la couleur du badge pour Filament
     */
    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'warning',
            self::PENDING_PAYMENT => 'warning',
            self::SUBMITTED => 'info',
            self::UNDER_REVIEW, self::IN_REVIEW => 'info',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED, self::WITHDRAWN => 'gray',
        };
    }

    /**
     * Traduit un statut string en label français
     * Méthode statique pour faciliter l'utilisation
     */
    public static function translateStatus(string $status): string
    {
        return self::tryFrom($status)?->getLabel() ?? ucfirst($status);
    }

    /**
     * Retourne la couleur pour un statut string
     * Méthode statique pour faciliter l'utilisation
     */
    public static function getStatusColor(string $status): string
    {
        return self::tryFrom($status)?->getColor() ?? 'gray';
    }

    /**
     * Retourne tous les statuts sous forme de tableau pour les formulaires
     */
    public static function toSelectOptions(): array
    {
        return [
            self::DRAFT->value => self::DRAFT->getLabel(),
            self::PENDING->value => self::PENDING->getLabel(),
            self::PENDING_PAYMENT->value => self::PENDING_PAYMENT->getLabel(),
            self::SUBMITTED->value => self::SUBMITTED->getLabel(),
            self::UNDER_REVIEW->value => self::UNDER_REVIEW->getLabel(),
            self::ACCEPTED->value => self::ACCEPTED->getLabel(),
            self::REJECTED->value => self::REJECTED->getLabel(),
            self::CANCELLED->value => self::CANCELLED->getLabel(),
            self::WITHDRAWN->value => self::WITHDRAWN->getLabel(),
        ];
    }

    /**
     * Retourne les statuts pour les filtres (subset le plus utilisé)
     */
    public static function toFilterOptions(): array
    {
        return [
            self::PENDING->value => self::PENDING->getLabel(),
            self::UNDER_REVIEW->value => self::UNDER_REVIEW->getLabel(),
            self::ACCEPTED->value => self::ACCEPTED->getLabel(),
            self::REJECTED->value => self::REJECTED->getLabel(),
            self::CANCELLED->value => self::CANCELLED->getLabel(),
        ];
    }
}
