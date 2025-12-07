<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use Carbon\Carbon;
use App\Models\Order;

$monthlySales = [];
for ($i = 11; $i >= 0; $i--) {
    $month = Carbon::now()->subMonths($i);
    $sales = Order::whereYear('created_at', $month->year)
        ->whereMonth('created_at', $month->month)
        ->where('status', '!=', 'dibatalkan')
        ->sum('total_harga');

    echo "Month: " . $month->format('M Y') . " | Sales: " . var_export($sales, true) . PHP_EOL;

    $monthlySales[] = [
        'month' => $month->format('M'),
        'amount' => $sales ?? 0
    ];
}

echo "\nFinal monthlySales array:\n";
echo json_encode($monthlySales, JSON_PRETTY_PRINT);
