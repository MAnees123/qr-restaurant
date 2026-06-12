<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Restaurant;
use App\Services\AnalyticsAggregationService;
use App\Services\HotItemDetectionService;
use Carbon\Carbon;

class AggregateSalesAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AnalyticsAggregationService $analytics, HotItemDetectionService $hotItems): void
    {
        $restaurants = Restaurant::all();
        $now = Carbon::now();

        foreach ($restaurants as $r) {
            // Aggregate metrics
            $analytics->aggregateForPeriod($r->id, 'hour', $now);
            $analytics->aggregateForPeriod($r->id, 'day', $now);
            $analytics->aggregateForPeriod($r->id, 'week', $now);
            $analytics->aggregateForPeriod($r->id, 'month', $now);

            // Re-calculate hot items
            $hotItems->calculateHotItems($r->id);
        }
    }
}
