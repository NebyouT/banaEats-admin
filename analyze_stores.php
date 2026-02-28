<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Analyzing easy.stores table structure ===\n";

try {
    // First, let's analyze the restaurants table (current bana database)
    echo "=== Current restaurants table structure ===\n";
    $restaurantColumns = DB::select('DESCRIBE restaurants');
    foreach ($restaurantColumns as $column) {
        echo "- {$column->Field} ({$column->Type})" . ($column->Null == 'NO' ? ' NOT NULL' : ' NULL') . "\n";
    }
    
    echo "\n=== Sample data from restaurants table ===\n";
    $sampleRestaurants = DB::table('restaurants')->limit(3)->get();
    foreach ($sampleRestaurants as $restaurant) {
        echo "ID: {$restaurant->id}, Name: {$restaurant->name}, Phone: {$restaurant->phone}\n";
    }
    
    // Now connect to the easy database
    echo "\n=== Connecting to easy database ===\n";
    
    // Test connection to easy database
    try {
        $easyColumns = DB::connection('easy')->select('DESCRIBE stores');
        echo "=== Easy.stores table structure ===\n";
        foreach ($easyColumns as $column) {
            echo "- {$column->Field} ({$column->Type})" . ($column->Null == 'NO' ? ' NOT NULL' : ' NULL') . "\n";
        }
        
        echo "\n=== Sample data from easy.stores table ===\n";
        $sampleStores = DB::connection('easy')->table('stores')->limit(3)->get();
        foreach ($sampleStores as $store) {
            echo "Store data: " . json_encode($store) . "\n";
        }
        
        echo "\n=== Data count ===\n";
        $storeCount = DB::connection('easy')->table('stores')->count();
        echo "Total stores in easy database: $storeCount\n";
        
        $restaurantCount = DB::table('restaurants')->count();
        echo "Current restaurants in bana database: $restaurantCount\n";
        
    } catch (Exception $e) {
        echo "Error connecting to easy database: " . $e->getMessage() . "\n";
        echo "Please ensure your .env file has the EASY_DB_* configuration:\n";
        echo "EASY_DB_HOST=127.0.0.1\n";
        echo "EASY_DB_PORT=3306\n";
        echo "EASY_DB_DATABASE=easy\n";
        echo "EASY_DB_USERNAME=root\n";
        echo "EASY_DB_PASSWORD=\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
