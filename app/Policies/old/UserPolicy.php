<?php

namespace App\Policies\old;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'dev']) || $user->isManager();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin/dev peut tout voir
        if ($user->hasRole(['admin', 'dev'])) {
            return true;
        }

        // Manager peut voir les utilisateurs de son partenaire
        return $user->isManager() && $user->partner_id === $model->partner_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'dev']) || $user->isManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasRole(['admin', 'dev']) ||
            ($user->isManager() && $user->partner_id === $model->partner_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true; // Autoriser la suppression de soi-même
        }

        return $user->hasRole(['admin', 'dev']) ||
            ($user->isManager() && $user->partner_id === $model->partner_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasRole(['admin', 'dev']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole(['admin', 'dev']);
    }
}
