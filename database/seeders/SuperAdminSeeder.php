<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Create or update the Super Admin user with the requested credentials
        User::updateOrCreate(
            ['email' => 'maneesimee@gmail.com'],
            [
                'name'            => 'Super Admin',
                'password'        => Hash::make('123456789'),
                'is_super_admin'  => true,
            ]
        );
    }
}
?>
