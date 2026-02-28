<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Restaurant Display Issue ===\n\n";

try {
    // First verify all restaurants are in database
    $totalCount = DB::table('restaurants')->count();
    echo "Total restaurants in database: {$totalCount}\n\n";
    
    // Check status distribution
    $statusCounts = DB::table('restaurants')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
    
    echo "=== Status Distribution ===\n";
    foreach ($statusCounts as $status) {
        $statusText = $status->status ? 'Active (1)' : 'Inactive (0)';
        echo "{$statusText}: {$status->count} restaurants\n";
    }
    
    // Check active distribution
    $activeCounts = DB::table('restaurants')
        ->select('active', DB::raw('COUNT(*) as count'))
        ->groupBy('active')
        ->get();
    
    echo "\n=== Active Distribution ===\n";
    foreach ($activeCounts as $active) {
        $activeText = $active->active ? 'Active (1)' : 'Inactive (0)';
        echo "{$activeText}: {$active->count} restaurants\n";
    }
    
    // Check vendor distribution
    $vendorCounts = DB::table('restaurants')
        ->select('vendor_id', DB::raw('COUNT(*) as count'))
        ->groupBy('vendor_id')
        ->orderBy('vendor_id')
        ->get();
    
    echo "\n=== Vendor Distribution ===\n";
    foreach ($vendorCounts as $vendor) {
        echo "Vendor ID {$vendor->vendor_id}: {$vendor->count} restaurants\n";
    }
    
    // Check for any NULL values that might cause issues
    echo "\n=== NULL Value Check ===\n";
    $nullChecks = [
        'name' => DB::table('restaurants')->whereNull('name')->count(),
        'phone' => DB::table('restaurants')->whereNull('phone')->count(),
        'vendor_id' => DB::table('restaurants')->whereNull('vendor_id')->count(),
        'status' => DB::table('restaurants')->whereNull('status')->count(),
        'active' => DB::table('restaurants')->whereNull('active')->count(),
    ];
    
    foreach ($nullChecks as $field => $count) {
        echo "NULL {$field}: {$count}\n";
    }
    
    // Show sample of all restaurants with key fields
    echo "\n=== All Restaurants Sample ===\n";
    $allRestaurants = DB::table('restaurants')
        ->select(['id', 'name', 'phone', 'status', 'active', 'vendor_id', 'created_at'])
        ->orderBy('id')
        ->get();
    
    foreach ($allRestaurants as $restaurant) {
        $statusIcon = $restaurant->status ? '✓' : '✗';
        $activeIcon = $restaurant->active ? '✓' : '✗';
        echo "ID: {$restaurant->id} | {$restaurant->name} | Phone: {$restaurant->phone} | Status: {$statusIcon} | Active: {$activeIcon} | Vendor: {$restaurant->vendor_id}\n";
    }
    
    // Check if there are any common filtering conditions
    echo "\n=== Common Filter Checks ===\n";
    
    // Check restaurants that would pass common filters
    $commonFilters = DB::table('restaurants')
        ->where('status', 1)  // Active status
        ->where('active', 1)  // Active flag
        ->whereNotNull('vendor_id')  // Has vendor
        ->whereNotNull('name')  // Has name
        ->whereNotNull('phone')  // Has phone
        ->count();
    
    echo "Restaurants passing common filters (status=1, active=1): {$commonFilters}\n";
    
    // Check restaurants with vendor_id = 1 (common default)
    $vendor1Count = DB::table('restaurants')->where('vendor_id', 1)->count();
    echo "Restaurants with vendor_id = 1: {$vendor1Count}\n";
    
    // Check recent restaurants (migrated ones)
    $recentCount = DB::table('restaurants')
        ->where('id', '>', 1)
        ->count();
    echo "Migrated restaurants (ID > 1): {$recentCount}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
