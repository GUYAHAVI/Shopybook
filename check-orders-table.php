<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Orders Table Structure ===\n\n";

$columns = DB::select("SHOW COLUMNS FROM orders");

foreach ($columns as $column) {
    echo "Column: {$column->Field}\n";
    echo "  Type: {$column->Type}\n";
    echo "  Null: {$column->Null}\n";
    echo "  Default: {$column->Default}\n";
    echo "\n";
}
