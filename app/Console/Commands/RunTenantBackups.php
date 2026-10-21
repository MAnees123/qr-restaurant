<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\Platform\BackupService;
use Illuminate\Console\Command;

class RunTenantBackups extends Command
{
    protected $signature = 'backups:run {--keep=7 : Number of newest backups to keep per restaurant}';
    protected $description = 'Create a backup for every restaurant that has the backup feature, pruning old ones.';

    public function handle(BackupService $svc): int
    {
        $keep = max(1, (int) $this->option('keep'));
        foreach (Restaurant::all() as $r) {
            if (! $r->hasFeature('backup')) continue;
            $res = $svc->create($r->id);
            foreach (array_slice($svc->list($r->id), $keep) as $old) {
                $svc->delete($r->id, $old['name']);
            }
            $this->info("Restaurant {$r->id}: {$res['name']}");
        }
        return self::SUCCESS;
    }
}
