<?php

namespace App\Policies;

use App\Models\Certificate\CertificateRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificateRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny CertificateRequest') || $user->hasRole(['partner']);
    }


    public function view(User $user, CertificateRequest $certificateRequest): bool
    {
        return $user->can('view CertificateRequest') || $user->hasRole(['partner']);
    }


    public function create(User $user): bool
    {
        return $user->can('create CertificateRequest');
    }


    public function update(User $user, CertificateRequest $certificateRequest): bool
    {
        return $user->can('update CertificateRequest');
    }


    public function delete(User $user, CertificateRequest $certificateRequest): bool
    {
        return $user->can('delete CertificateRequest');
    }


    public function restore(User $user, CertificateRequest $certificateRequest): bool
    {
        return $user->can('restore CertificateRequest');
    }


    public function forceDelete(User $user, CertificateRequest $certificateRequest): bool
    {
        return $user->can('forceDelete CertificateRequest');
    }
}
