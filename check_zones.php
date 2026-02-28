<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Zone Analysis ===\n\n";

try {
    // Check zone distribution of restaurants
    $zoneDistribution = DB::table('restaurants')
        ->select('zone_id', DB::raw('COUNT(*) as count'))
        ->groupBy('zone_id')
        ->orderBy('zone_id')
        ->get();
    
    echo "=== Restaurant Distribution by Zone ===\n";
    foreach ($zoneDistribution as $zone) {
        $zoneId = $zone->zone_id ?? 'NULL';
        echo "Zone {$zoneId}: {$zone->count} restaurants\n";
    }
    
    // Check what zones exist in the zones table
    echo "\n=== Available Zones ===\n";
    $zones = DB::table('zones')->select('id', 'name')->get();
    foreach ($zones as $zone) {
        echo "Zone ID: {$zone->id} - Name: {$zone->name}\n";
    }
    
    // Check which restaurants have NULL zone_id
    $nullZoneCount = DB::table('restaurants')->whereNull('zone_id')->count();
    echo "\nRestaurants with NULL zone_id: {$nullZoneCount}\n";
    
    // Check restaurants with zone_id that don't exist in zones table
    echo "\n=== Checking for Invalid Zone IDs ===\n";
    $invalidZones = DB::table('restaurants')
        ->leftJoin('zones', 'restaurants.zone_id', '=', 'zones.id')
        ->whereNull('zones.id')
        ->whereNotNull('restaurants.zone_id')
        ->select('restaurants.id', 'restaurants.name', 'restaurants.zone_id')
        ->get();
    
    if ($invalidZones->count() > 0) {
        echo "Restaurants with invalid zone_id:\n";
        foreach ($invalidZones as $restaurant) {
            echo "- ID: {$restaurant->id}, Name: {$restaurant->name}, Zone ID: {$restaurant->zone_id}\n";
        }
    } else {
        echo "All restaurants have valid zone_ids\n";
    }
    
    // Test API call with different zone scenarios
    echo "\n=== API Simulation ===\n";
    
    // Test with zone 1 (most common)
    $zone1Restaurants = DB::table('restaurants')->where('zone_id', 1)->count();
    echo "Restaurants in zone 1: {$zone1Restaurants}\n";
    
    // Test with zone 2
    $zone2Restaurants = DB::table('restaurants')->where('zone_id', 2)->count();
    echo "Restaurants in zone 2: {$zone2Restaurants}\n";
    
    // Test with multiple zones (array)
    $multiZoneRestaurants = DB::table('restaurants')->whereIn('zone_id', [1, 2])->count();
    echo "Restaurants in zones [1,2]: {$multiZoneRestaurants}\n";
    
    // Test with all zones that exist
    $allValidZoneIds = DB::table('zones')->pluck('id')->toArray();
    $allZoneRestaurants = DB::table('restaurants')->whereIn('zone_id', $allValidZoneIds)->count();
    echo "Restaurants in all valid zones: {$allZoneRestaurants}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
