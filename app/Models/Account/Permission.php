<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'label',
        'permission_group_id'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class);
    }
}
