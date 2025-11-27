<?php

namespace App\Policies;

use App\Models\RealEstate\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny Category');
    }


    public function view(User $user, Category $category): bool
    {
        return $user->can('view Category');
    }


    public function create(User $user): bool
    {
        return $user->can('create Category');
    }


    public function update(User $user, Category $category): bool
    {
        return $user->can('update Category');
    }


    public function delete(User $user, Category $category): bool
    {
        return $user->can('delete Category');
    }


    public function restore(User $user, Category $category): bool
    {
        return $user->can('restore Category');
    }


    public function forceDelete(User $user, Category $category): bool
    {
        return $user->can('forceDelete Category');
    }
}
