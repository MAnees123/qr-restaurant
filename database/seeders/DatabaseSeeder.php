<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\QrCode;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@restaurant.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create Kitchen User
        $kitchen = User::updateOrCreate(
            ['email' => 'kitchen@restaurant.com'],
            [
                'name' => 'Kitchen Staff',
                'password' => Hash::make('password'),
                'role' => 'kitchen',
            ]
        );

        // Create Restaurant
        $restaurant = Restaurant::updateOrCreate(
            ['name' => 'Demo Restaurant'],
            [
                'cuisine_type' => 'Italian',
                'address' => '123 Main St, Food City',
                'phone' => '555-1234',
            ]
        );

        // Assign restaurant to users
        $admin->update(['restaurant_id' => $restaurant->id]);
        $kitchen->update(['restaurant_id' => $restaurant->id]);

        // Create Tables
        for ($i = 1; $i <= 4; $i++) {
            $table = Table::create([
                'restaurant_id' => $restaurant->id,
                'table_number' => "T-$i",
                'capacity' => 4,
            ]);

            QrCode::create([
                'table_id' => $table->id,
                'code' => Str::uuid(),
            ]);
        }

        // Create Menu Categories
        $categories = ['Starters & Chaat', 'Main Course', 'Desserts', 'Drinks'];
        $menuCategories = [];

        foreach ($categories as $index => $catName) {
            $menuCategories[] = MenuCategory::create([
                'restaurant_id' => $restaurant->id,
                'name' => $catName,
                'sort_order' => $index,
            ]);
        }

        // Create Menu Items
        $items = [
            ['name' => 'Dahi Bhallay', 'cat_id' => $menuCategories[0]->id, 'price' => 150.00, 'desc' => 'Soft lentil dumplings topped with creamy yogurt and tamarind chutney.', 'image' => 'menu_items/dahi_bhallay.png'],
            ['name' => 'Gol Gappay', 'cat_id' => $menuCategories[0]->id, 'price' => 200.00, 'desc' => 'Crispy hollow puris filled with chickpeas, served with spicy mint water.', 'image' => 'menu_items/gol_gappay.png'],
            
            ['name' => 'Chicken Biryani', 'cat_id' => $menuCategories[1]->id, 'price' => 450.00, 'desc' => 'Classic spicy chicken biryani made with fragrant basmati rice.', 'image' => 'menu_items/chicken_biryani.png'],
            ['name' => 'Beef Karahi', 'cat_id' => $menuCategories[1]->id, 'price' => 1200.00, 'desc' => 'Traditional beef karahi cooked with tomatoes and green chilies.', 'image' => null],
            
            ['name' => 'Kheer', 'cat_id' => $menuCategories[2]->id, 'price' => 250.00, 'desc' => 'Rich and creamy traditional rice pudding topped with nuts.', 'image' => null],
            ['name' => 'Gulab Jamun', 'cat_id' => $menuCategories[2]->id, 'price' => 150.00, 'desc' => 'Soft and sweet deep-fried milk dough balls in sugar syrup.', 'image' => null],
            
            ['name' => 'Fresh Mango Juice', 'cat_id' => $menuCategories[3]->id, 'price' => 300.00, 'desc' => 'Refreshing seasonal mango juice.', 'image' => 'menu_items/mango_juice.png'],
            ['name' => 'Lassi', 'cat_id' => $menuCategories[3]->id, 'price' => 200.00, 'desc' => 'Sweet or salty yogurt-based traditional drink.', 'image' => null],
        ];

        foreach ($items as $index => $item) {
            MenuItem::create([
                'restaurant_id' => $restaurant->id,
                'menu_category_id' => $item['cat_id'],
                'name' => $item['name'],
                'description' => $item['desc'],
                'price' => $item['price'],
                'image' => $item['image'],
                'sort_order' => $index,
            ]);
        }
        
        // Seed Dummy Orders for Graph
        $statuses = ['served', 'served', 'served', 'served', 'pending', 'preparing', 'ready'];
        
        for ($i = 0; $i < 30; $i++) {
            $randomDaysAgo = rand(0, 6);
            $orderDate = now()->subDays($randomDaysAgo)->subHours(rand(1, 10));
            $randomTable = Table::where('restaurant_id', $restaurant->id)->inRandomOrder()->first();
            
            $order = \App\Models\Order::create([
                'restaurant_id' => $restaurant->id,
                'table_id' => $randomTable->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(6)),
                'total_amount' => 0, // Will update
                'status' => $statuses[array_rand($statuses)],
                'notes' => rand(0, 1) ? 'Make it extra spicy!' : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            $totalAmount = 0;
            $numItems = rand(1, 4);

            for ($j = 0; $j < $numItems; $j++) {
                $randomItem = MenuItem::where('restaurant_id', $restaurant->id)->inRandomOrder()->first();
                $qty = rand(1, 3);
                $subtotal = $randomItem->price * $qty;
                
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $randomItem->id,
                    'quantity' => $qty,
                    'unit_price' => $randomItem->price,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $order->update(['total_amount' => $totalAmount]);
        }
    }
}
