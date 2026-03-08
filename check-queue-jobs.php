<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Queue Jobs ===\n\n";

$jobs = DB::table('jobs')->orderBy('id', 'desc')->limit(10)->get();

if ($jobs->isEmpty()) {
    echo "No jobs in queue\n\n";
} else {
    foreach ($jobs as $job) {
        $payload = json_decode($job->payload);
        echo "Job ID: {$job->id}\n";
        echo "Queue: {$job->queue}\n";
        echo "Attempts: {$job->attempts}\n";
        echo "Type: " . ($payload->displayName ?? 'Unknown') . "\n";
        echo "Created: " . date('Y-m-d H:i:s', $job->created_at) . "\n";
        echo "---\n";
    }
}

echo "\n=== Checking Failed Jobs ===\n\n";

$failedJobs = DB::table('failed_jobs')->orderBy('id', 'desc')->limit(5)->get();

if ($failedJobs->isEmpty()) {
    echo "No failed jobs\n";
} else {
    foreach ($failedJobs as $job) {
        echo "Failed Job ID: {$job->id}\n";
        echo "Connection: {$job->connection}\n";
        echo "Queue: {$job->queue}\n";
        echo "Exception: " . substr($job->exception, 0, 200) . "...\n";
        echo "Failed at: {$job->failed_at}\n";
        echo "---\n";
    }
}
