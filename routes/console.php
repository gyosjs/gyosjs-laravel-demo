<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\DemoWorkspace;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('demo:prune {--hours=24}', function () {
    $cutoff = now()->subHours(max(1, (int) $this->option('hours')));
    $count = DemoWorkspace::where('last_seen_at', '<', $cutoff)->delete();
    $this->info("Pruned $count expired demo workspace(s).");
})->purpose('Remove expired public demo workspaces');

Schedule::command('demo:prune')->hourly();
