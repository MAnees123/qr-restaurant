<?php

namespace Tests\Concerns;

use App\Models\Restaurant;
use App\Models\User;

trait CreatesTenants
{
    protected function makeRestaurant(array $features = [], array $attrs = []): Restaurant
    {
        return Restaurant::create(array_merge([
            'name'             => 'Test Restaurant ' . uniqid(),
            'granted_features' => $features,
            'is_active'        => true,
        ], $attrs));
    }

    protected function makeUser(Restaurant $restaurant, string $role = 'admin', array $attrs = []): User
    {
        return User::create(array_merge([
            'restaurant_id' => $restaurant->id,
            'name'          => ucfirst($role) . ' ' . uniqid(),
            'email'         => $role . uniqid() . '@test.local',
            'password'      => bcrypt('password'),
            'role'          => $role,
        ], $attrs));
    }

    /** Admin of a restaurant that has exactly $features granted. */
    protected function adminWith(array $features): User
    {
        return $this->makeUser($this->makeRestaurant($features), 'admin');
    }
}
