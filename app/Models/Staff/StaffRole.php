<?php

namespace App\Models\Staff;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StaffRole extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'description', 'permissions'];

    protected $casts = ['permissions' => 'array'];

    /** code => label */
    public const PERMISSIONS = [
        'view_orders'       => 'View orders',
        'manage_orders'     => 'Manage orders',
        'view_menu'         => 'View menu',
        'manage_menu'       => 'Manage menu',
        'manage_tables'     => 'Manage tables',
        'view_reports'      => 'View reports',
        'manage_customers'  => 'Manage customers',
        'manage_attendance' => 'Manage attendance',
        'manage_payroll'    => 'Manage payroll',
        'manage_discounts'  => 'Manage discounts',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'custom_role_id');
    }

    public function has(string $code): bool
    {
        return in_array($code, $this->permissions ?? [], true);
    }
}
