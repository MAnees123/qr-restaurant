<?php

namespace App\Console\Commands;

use App\Models\Table;
use Illuminate\Console\Command;

class ReleaseOccupiedTables extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tables:release-expired';

    /**
     * The console command description.
     */
    protected $description = 'Automatically release occupied tables whose 30-minute countdown has expired and have no active orders.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tables = Table::where('status', 'occupied')
            ->whereNotNull('auto_release_at')
            ->where('auto_release_at', '<=', now())
            ->get();

        $released = 0;

        foreach ($tables as $table) {
            // Double-check: only free if no active/pending orders remain
            if (!$table->hasActiveOrders()) {
                $table->update([
                    'status' => 'free',
                    'auto_release_at' => null,
                ]);
                $released++;
                $this->info("Table #{$table->table_number} released (Restaurant #{$table->restaurant_id})");
            } else {
                // There are still active orders — cancel the timer and keep occupied
                $table->update(['auto_release_at' => null]);
                $this->warn("Table #{$table->table_number} still has active orders — keeping occupied.");
            }
        }

        if ($released === 0 && $tables->isEmpty()) {
            $this->info('No tables to release.');
        } else {
            $this->info("Released {$released} table(s).");
        }

        return self::SUCCESS;
    }
}
