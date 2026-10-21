<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\Table;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Reservation::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tables = Table::where('restaurant_id', 1)->get();
        if ($tables->isEmpty()) return;

        $reservations = [
            // Upcoming today & tomorrow
            [
                'customer_name'    => 'David Sterling',
                'customer_phone'   => '+1 (555) 234-8901',
                'reservation_date' => now()->toDateString(),
                'reservation_time' => '19:30:00',
                'guests'           => 4,
                'status'           => 'confirmed',
                'event_type'       => 'Business Dinner',
                'notes'            => 'Quiet corner table preferred for meeting.',
                'table_id'         => $tables->firstWhere('table_number', 'T-04')?->id ?? $tables->first()->id,
            ],
            [
                'customer_name'    => 'Sophia Hernandez',
                'customer_phone'   => '+1 (555) 876-5432',
                'reservation_date' => now()->toDateString(),
                'reservation_time' => '20:00:00',
                'guests'           => 8,
                'status'           => 'confirmed',
                'event_type'       => 'Birthday Celebration',
                'notes'            => 'Bringing their own birthday cake. Please provide dessert plates.',
                'table_id'         => $tables->firstWhere('table_number', 'VIP-1')?->id ?? $tables->first()->id,
            ],
            [
                'customer_name'    => 'Alexander Wright',
                'customer_phone'   => '+1 (555) 345-6789',
                'reservation_date' => now()->addDay()->toDateString(),
                'reservation_time' => '18:45:00',
                'guests'           => 2,
                'status'           => 'pending',
                'event_type'       => 'Anniversary',
                'notes'            => 'Romantic setting requested with candle light.',
                'table_id'         => $tables->firstWhere('table_number', 'Patio-01')?->id ?? $tables->first()->id,
            ],
            [
                'customer_name'    => 'Emma Watson',
                'customer_phone'   => '+1 (555) 987-1234',
                'reservation_date' => now()->addDays(2)->toDateString(),
                'reservation_time' => '20:30:00',
                'guests'           => 6,
                'status'           => 'confirmed',
                'event_type'       => 'Family Gathering',
                'notes'            => 'High chair needed for a toddler.',
                'table_id'         => $tables->firstWhere('table_number', 'VIP-3')?->id ?? $tables->first()->id,
            ],
            [
                'customer_name'    => 'James Miller',
                'customer_phone'   => '+1 (555) 654-3210',
                'reservation_date' => now()->addDays(3)->toDateString(),
                'reservation_time' => '19:00:00',
                'guests'           => 5,
                'status'           => 'pending',
                'event_type'       => 'Casual Dining',
                'notes'            => 'First time visiting, looking forward to steak.',
                'table_id'         => $tables->firstWhere('table_number', 'Roof-02')?->id ?? $tables->first()->id,
            ],
            // Past seated / completed
            [
                'customer_name'    => 'Olivia Brown',
                'customer_phone'   => '+1 (555) 432-8765',
                'reservation_date' => now()->subDay()->toDateString(),
                'reservation_time' => '19:00:00',
                'guests'           => 4,
                'status'           => 'completed',
                'event_type'       => 'Dinner',
                'notes'            => 'Table seated on time.',
                'table_id'         => $tables->firstWhere('table_number', 'T-02')?->id ?? $tables->first()->id,
            ],
        ];

        foreach ($reservations as $r) {
            Reservation::create([
                'restaurant_id'    => 1,
                'table_id'         => $r['table_id'],
                'customer_name'    => $r['customer_name'],
                'customer_phone'   => $r['customer_phone'],
                'reservation_date' => $r['reservation_date'],
                'reservation_time' => $r['reservation_time'],
                'guests'           => $r['guests'],
                'status'           => $r['status'],
                'event_type'       => $r['event_type'],
                'notes'            => $r['notes'],
            ]);
        }
    }
}
