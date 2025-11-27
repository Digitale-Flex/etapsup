<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionGroup extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'label'];

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
