<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MenuCategory;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MenuItem::truncate();
        MenuCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            [
                'id'          => 1,
                'name'        => 'Starters & Appetizers',
                'description' => 'Crispy appetising finger foods to kickstart your meal',
                'sort_order'  => 0,
            ],
            [
                'id'          => 2,
                'name'        => 'Burgers & Sandwiches',
                'description' => 'Handcrafted smash patties, artisan brioche buns and crunch chicken',
                'sort_order'  => 1,
            ],
            [
                'id'          => 3,
                'name'        => 'Stone-Baked Pizzas',
                'description' => 'Authentic thin-crust Italian pizzas baked at 450°C',
                'sort_order'  => 2,
            ],
            [
                'id'          => 4,
                'name'        => "Chef's Grills & Steaks",
                'description' => 'Prime cut sizzlers, succulent tenderloin and BBQ smoke platters',
                'sort_order'  => 3,
            ],
            [
                'id'          => 5,
                'name'        => 'Traditional & Karahi',
                'description' => 'Rich aromatic gravies cooked in pure clarified butter and spices',
                'sort_order'  => 4,
            ],
            [
                'id'          => 6,
                'name'        => 'Artisan Desserts',
                'description' => 'Decadent warm sweets, cakes and premium ice cream pairings',
                'sort_order'  => 5,
            ],
            [
                'id'          => 7,
                'name'        => 'Beverages & Mocktails',
                'description' => 'Chilled craft refreshers, fresh juices, shakes and brewed coffees',
                'sort_order'  => 6,
            ],
        ];

        foreach ($categories as $cat) {
            MenuCategory::create([
                'id'            => $cat['id'],
                'restaurant_id' => 1,
                'name'          => $cat['name'],
                'description'   => $cat['description'],
                'sort_order'    => $cat['sort_order'],
                'is_active'     => true,
            ]);
        }

        $items = [
            // Category 1: Starters & Appetizers
            [
                'cat_id'      => 1,
                'name'        => 'Crispy Mozzarella Sticks',
                'desc'        => 'Golden crumb-fried mozzarella fingers served with house marinara sauce.',
                'price'       => 8.50,
                'orig_price'  => 10.00,
                'prep_time'   => 12,
                'image'       => 'menu_items/mozzarella_sticks.jpg',
            ],
            [
                'cat_id'      => 1,
                'name'        => 'Loaded Cheesy Nachos',
                'desc'        => 'Crispy tortilla chips topped with melted cheddar, jalapeños, salsa & sour cream.',
                'price'       => 11.00,
                'orig_price'  => 13.50,
                'prep_time'   => 15,
                'image'       => 'menu_items/cheesy_nachos.jpg',
            ],
            [
                'cat_id'      => 1,
                'name'        => 'Dynamite Shrimps',
                'desc'        => 'Crispy batter-fried shrimps tossed in our signature spicy dynamite sauce.',
                'price'       => 14.50,
                'orig_price'  => 17.00,
                'prep_time'   => 15,
                'image'       => 'menu_items/dynamite_shrimps.jpg',
            ],
            [
                'cat_id'      => 1,
                'name'        => 'Buffalo Chicken Wings',
                'desc'        => 'Tender chicken wings glazed in spicy tangy buffalo glaze with ranch dip.',
                'price'       => 12.00,
                'orig_price'  => 14.00,
                'prep_time'   => 18,
                'image'       => 'menu_items/buffalo_wings.jpg',
            ],
            [
                'cat_id'      => 1,
                'name'        => 'Crispy Dahi Bhallay',
                'desc'        => 'Soft lentil dumplings topped with creamy yogurt, tamarind and mint chutney.',
                'price'       => 6.50,
                'orig_price'  => 8.00,
                'prep_time'   => 10,
                'image'       => 'menu_items/dahi_bhallay.jpg',
            ],
            [
                'cat_id'      => 1,
                'name'        => 'Street Style Gol Gappay',
                'desc'        => 'Crispy hollow puris filled with spiced chickpeas, served with chilled mint water.',
                'price'       => 7.00,
                'orig_price'  => 9.00,
                'prep_time'   => 10,
                'image'       => 'menu_items/gol_gappay.jpg',
            ],

            // Category 2: Burgers & Sandwiches
            [
                'cat_id'      => 2,
                'name'        => 'Smokey Angus Beef Burger',
                'desc'        => '200g prime Angus beef patty, cheddar slice, caramelized onions, BBQ secret sauce.',
                'price'       => 16.50,
                'orig_price'  => 19.50,
                'prep_time'   => 20,
                'image'       => 'menu_items/angus_burger.jpg',
            ],
            [
                'cat_id'      => 2,
                'name'        => 'Crispy Zinger Crunch Burger',
                'desc'        => 'Crispy buttermilk battered chicken breast, iceberg lettuce and garlic mayo in toasted brioche.',
                'price'       => 13.50,
                'orig_price'  => 16.00,
                'prep_time'   => 15,
                'image'       => 'menu_items/zinger_burger.jpg',
            ],
            [
                'cat_id'      => 2,
                'name'        => 'Truffle Mushroom Burger',
                'desc'        => 'Juicy beef patty infused with sauteed button mushrooms, Swiss cheese and black truffle mayo.',
                'price'       => 17.50,
                'orig_price'  => 21.00,
                'prep_time'   => 20,
                'image'       => 'menu_items/truffle_burger.jpg',
            ],
            [
                'cat_id'      => 2,
                'name'        => 'Club Supreme Sandwich',
                'desc'        => 'Triple-decker toasted bread filled with grilled chicken, turkey bacon, egg and cheddar.',
                'price'       => 11.50,
                'orig_price'  => 13.00,
                'prep_time'   => 15,
                'image'       => 'menu_items/club_sandwich.jpg',
            ],

            // Category 3: Stone-Baked Pizzas
            [
                'cat_id'      => 3,
                'name'        => 'Margherita Wood-Fired Pizza',
                'desc'        => 'San Marzano tomato sauce, fresh mozzarella fior di latte, extra virgin olive oil & fresh basil.',
                'price'       => 14.00,
                'orig_price'  => 16.50,
                'prep_time'   => 20,
                'image'       => 'menu_items/margherita_pizza.jpg',
            ],
            [
                'cat_id'      => 3,
                'name'        => 'Pepperoni Passion Pizza',
                'desc'        => 'Loaded with artisan beef pepperoni, mozzarella blend and oregano on crisp thin crust.',
                'price'       => 17.50,
                'orig_price'  => 20.00,
                'prep_time'   => 20,
                'image'       => 'menu_items/pepperoni_pizza.jpg',
            ],
            [
                'cat_id'      => 3,
                'name'        => 'Creamy Chicken Alfredo Pasta',
                'desc'        => 'Fettuccine pasta in rich garlic parmesan cream sauce with grilled chicken and parsley.',
                'price'       => 15.00,
                'orig_price'  => 18.00,
                'prep_time'   => 22,
                'image'       => 'menu_items/chicken_alfredo.jpg',
            ],

            // Category 4: Grills & Steaks
            [
                'cat_id'      => 4,
                'name'        => 'Prime Ribeye Steak with Garlic Butter',
                'desc'        => 'Charbroiled 300g ribeye steak topped with roasted garlic herb compound butter and mash.',
                'price'       => 29.50,
                'orig_price'  => 34.00,
                'prep_time'   => 25,
                'image'       => 'menu_items/ribeye_steak.jpg',
            ],
            [
                'cat_id'      => 4,
                'name'        => 'Tandoori Mixed Grill Platter',
                'desc'        => 'Skewers of chicken tikka, beef seekh kabab, fish tikka and grilled prawns with mint dip.',
                'price'       => 26.00,
                'orig_price'  => 31.00,
                'prep_time'   => 25,
                'image'       => 'menu_items/mixed_grill.jpg',
            ],
            [
                'cat_id'      => 4,
                'name'        => 'BBQ Peri Peri Chicken Half',
                'desc'        => 'Flame-grilled half chicken basted in African bird eye peri-peri marinade with spiced fries.',
                'price'       => 16.00,
                'orig_price'  => 19.00,
                'prep_time'   => 20,
                'image'       => 'menu_items/bbq_chicken.jpg',
            ],

            // Category 5: Traditional & Karahi
            [
                'cat_id'      => 5,
                'name'        => 'Royal Chicken Biryani',
                'desc'        => 'Fragrant aged basmati rice layered with spiced marinated chicken, saffron, potatoes and raita.',
                'price'       => 14.00,
                'orig_price'  => 16.50,
                'prep_time'   => 20,
                'image'       => 'menu_items/chicken_biryani.jpg',
            ],
            [
                'cat_id'      => 5,
                'name'        => 'Peshawari Shinwari Beef Karahi',
                'desc'        => 'Authentic beef cooked in cast iron wok with fresh tomatoes, green chilies, ginger & black pepper.',
                'price'       => 24.00,
                'orig_price'  => 28.00,
                'prep_time'   => 25,
                'image'       => 'menu_items/beef_karahi.jpg',
            ],
            [
                'cat_id'      => 5,
                'name'        => 'Butter Chicken Makhani',
                'desc'        => 'Tender tandoori chicken cooked in velvety tomato, butter and fenugreek cream gravy.',
                'price'       => 15.50,
                'orig_price'  => 18.00,
                'prep_time'   => 20,
                'image'       => 'menu_items/butter_chicken.jpg',
            ],
            [
                'cat_id'      => 5,
                'name'        => 'Garlic Naan Basket',
                'desc'        => 'Fluffy clay oven baked naans brushed with crushed garlic and melted coriander butter (3 pcs).',
                'price'       => 4.50,
                'orig_price'  => 5.50,
                'prep_time'   => 8,
                'image'       => 'menu_items/garlic_naan.jpg',
            ],

            // Category 6: Artisan Desserts
            [
                'cat_id'      => 6,
                'name'        => 'Belgian Molten Lava Cake',
                'desc'        => 'Warm dark chocolate cake with a molten chocolate center, served with vanilla bean ice cream.',
                'price'       => 8.50,
                'orig_price'  => 10.50,
                'prep_time'   => 12,
                'image'       => 'menu_items/lava_cake.jpg',
            ],
            [
                'cat_id'      => 6,
                'name'        => 'Royal Pistachio Kheer',
                'desc'        => 'Slow-simmered basmati rice and milk pudding infused with cardamom, saffron and silver leaf.',
                'price'       => 6.50,
                'orig_price'  => 8.00,
                'prep_time'   => 10,
                'image'       => 'menu_items/royal_kheer.jpg',
            ],
            [
                'cat_id'      => 6,
                'name'        => 'Hot Gulab Jamun with Ice Cream',
                'desc'        => 'Golden milk dumplings soaked in saffron sugar syrup, served warm with vanilla scoop.',
                'price'       => 6.00,
                'orig_price'  => 7.50,
                'prep_time'   => 8,
                'image'       => 'menu_items/gulab_jamun.jpg',
            ],
            [
                'cat_id'      => 6,
                'name'        => 'Classic New York Cheesecake',
                'desc'        => 'Dense, rich cheesecake on graham cracker crust drizzled with wild strawberry coulis.',
                'price'       => 9.00,
                'orig_price'  => 11.00,
                'prep_time'   => 8,
                'image'       => 'menu_items/cheesecake.jpg',
            ],

            // Category 7: Beverages & Mocktails
            [
                'cat_id'      => 7,
                'name'        => 'Fresh Mango Mint Fusion',
                'desc'        => 'Chilled puree of seasonal sweet mangoes shaken with fresh garden mint leaves and lime.',
                'price'       => 5.50,
                'orig_price'  => 7.00,
                'prep_time'   => 6,
                'image'       => 'menu_items/mango_juice.jpg',
            ],
            [
                'cat_id'      => 7,
                'name'        => 'Royal Punjabi Sweet Lassi',
                'desc'        => 'Traditional thick yogurt drink churned with malai, cardamom and crushed almonds.',
                'price'       => 5.00,
                'orig_price'  => 6.50,
                'prep_time'   => 6,
                'image'       => 'menu_items/lassi.jpg',
            ],
            [
                'cat_id'      => 7,
                'name'        => 'Mint Margarita Cooler',
                'desc'        => 'Blended lime juice, fresh spearmint, crushed ice, sprite and a rim of black rock salt.',
                'price'       => 5.50,
                'orig_price'  => 7.00,
                'prep_time'   => 6,
                'image'       => 'menu_items/mint_margarita.jpg',
            ],
            [
                'cat_id'      => 7,
                'name'        => 'Iced Caramel Macchiato',
                'desc'        => 'Espresso shots layered with cold milk and sweet vanilla syrup, finished with buttery caramel drizzle.',
                'price'       => 6.50,
                'orig_price'  => 8.00,
                'prep_time'   => 7,
                'image'       => 'menu_items/caramel_macchiato.jpg',
            ],
        ];

        foreach ($items as $index => $item) {
            MenuItem::create([
                'id'               => $index + 1,
                'restaurant_id'    => 1,
                'menu_category_id' => $item['cat_id'],
                'name'             => $item['name'],
                'description'      => $item['desc'],
                'price'            => $item['price'],
                'original_price'   => $item['orig_price'],
                'preparation_time' => $item['prep_time'],
                'image'            => $item['image'],
                'is_available'     => true,
                'sort_order'       => $index,
            ]);
        }
    }
}
