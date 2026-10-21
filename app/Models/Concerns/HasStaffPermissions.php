<?php

namespace App\Models\Concerns;

use App\Models\Staff\StaffRole;

/** Permission checks backed by the restaurant's custom staff roles (role_permission feature). */
trait HasStaffPermissions
{
    public function customRole(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StaffRole::class, 'custom_role_id');
    }

    /**
     * Restaurant admins and super admins hold every permission. Other users need a
     * custom role of their own restaurant that includes the code.
     */
    public function hasPermission(string $code): bool
    {
        if ($this->is_super_admin || $this->role === 'admin') {
            return true;
        }

        $role = $this->customRole;

        return $role !== null
            && (int) $role->restaurant_id === (int) $this->restaurant_id
            && $role->has($code);
    }
}
