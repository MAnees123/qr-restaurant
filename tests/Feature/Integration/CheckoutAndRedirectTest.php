<?php

namespace Tests\Feature\Integration;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class CheckoutAndRedirectTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    private function guestSession(array $features = [])
    {
        $restaurant = $this->makeRestaurant($features);
        $table = Table::create(['restaurant_id' => $restaurant->id, 'table_number' => 'T-1', 'secure_token' => Str::random(24)]);
        $cat = MenuCategory::create(['restaurant_id' => $restaurant->id, 'name' => 'Main', 'is_active' => true]);
        $item = MenuItem::create(['menu_category_id' => $cat->id, 'restaurant_id' => $restaurant->id, 'name' => 'Burger', 'price' => 10]);

        return [$restaurant, $this->withSession([
            'cart' => [$item->id => ['id' => $item->id, 'name' => 'Burger', 'price' => 10, 'quantity' => 2]],
            'current_restaurant_id' => $restaurant->id,
            'current_table_id' => $table->id,
        ])];
    }

    public function test_guest_checkout_stores_customer_contact_details(): void
    {
        [$restaurant, $http] = $this->guestSession();

        $http->postJson(route('order.place'), [
            'payment_method' => 'cash', 'payment_status' => 'pending',
            'customer_name' => 'Sara', 'customer_phone' => '+923001234567', 'customer_email' => 'sara@example.com',
        ])->assertOk();

        $order = Order::where('restaurant_id', $restaurant->id)->firstOrFail();
        $this->assertSame('Sara', $order->customer_name);
        $this->assertSame('+923001234567', $order->customer_phone);
        $this->assertSame('sara@example.com', $order->customer_email);
    }

    public function test_guest_checkout_rejects_invalid_email_and_works_without_contact_details(): void
    {
        [$restaurant, $http] = $this->guestSession();

        $http->postJson(route('order.place'), ['customer_email' => 'not-an-email'])->assertStatus(422);
        $this->assertSame(0, Order::where('restaurant_id', $restaurant->id)->count());

        $http->postJson(route('order.place'), ['payment_method' => 'cash'])->assertOk();
        $this->assertNull(Order::where('restaurant_id', $restaurant->id)->first()->customer_phone);
    }

    public function test_dashboard_sends_each_role_to_a_page_it_may_open(): void
    {
        $restaurant = $this->makeRestaurant(['waiter_panel', 'cashier_module', 'kitchen_screen']);

        $waiter = $this->makeUser($restaurant, 'staff', ['staff_role' => 'waiter']);
        $this->actingAs($waiter)->get('/dashboard')->assertRedirect(route('staff.waiter.index'));

        $cashier = $this->makeUser($restaurant, 'staff', ['staff_role' => 'cashier']);
        $this->actingAs($cashier)->get('/dashboard')->assertRedirect(route('staff.cashier.index'));

        $kitchen = $this->makeUser($restaurant, 'kitchen');
        $this->actingAs($kitchen)->get('/dashboard')->assertRedirect(route('kitchen.dashboard'));

        $admin = $this->makeUser($restaurant, 'admin');
        $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
    }

    public function test_staff_without_a_granted_panel_never_lands_on_the_admin_dashboard(): void
    {
        $waiter = $this->makeUser($this->makeRestaurant([]), 'staff', ['staff_role' => 'waiter']);
        $this->actingAs($waiter)->get('/dashboard')->assertRedirect(route('profile.edit'));

        $withAttendance = $this->makeUser($this->makeRestaurant(['attendance']), 'staff', ['staff_role' => 'waiter']);
        $this->actingAs($withAttendance)->get('/dashboard')->assertRedirect(route('staff.attendance.my'));
    }
}
