<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use Carbon\Carbon;

echo "Current: " . Carbon::now() . PHP_EOL;
echo "Dec check: " . Carbon::now()->format('Y-m') . PHP_EOL . PHP_EOL;

echo "Months calculation:" . PHP_EOL;
for ($i = 11; $i >= 0; $i--) {
    $m = Carbon::now()->subMonths($i);
    echo $m->format('M') . ' - ' . $m->format('Y-m-d') . PHP_EOL;
}