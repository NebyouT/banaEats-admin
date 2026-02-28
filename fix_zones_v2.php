<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fixing Zone Configuration (Version 2) ===\n\n";

try {
    // Check current zones and their coordinates
    $currentZones = DB::table('zones')->get();
    echo "Current zones in database:\n";
    foreach ($currentZones as $zone) {
        echo "- Zone ID: {$zone->id}, Name: {$zone->name}, Coordinates: {$zone->coordinates}\n";
    }
    
    // Get the coordinates format from existing zone
    $existingZone = DB::table('zones')->first();
    $coordinatesFormat = $existingZone ? $existingZone->coordinates : null;
    
    echo "\nUsing coordinates format from existing zone: {$coordinatesFormat}\n";
    
    // Get all unique zone_ids from restaurants that don't exist in zones table
    $missingZoneIds = DB::table('restaurants')
        ->leftJoin('zones', 'restaurants.zone_id', '=', 'zones.id')
        ->whereNull('zones.id')
        ->whereNotNull('restaurants.zone_id')
        ->distinct()
        ->pluck('restaurants.zone_id')
        ->toArray();
    
    echo "\nMissing zone IDs that need to be created: " . implode(', ', $missingZoneIds) . "\n";
    
    if (!empty($missingZoneIds)) {
        echo "\nCreating missing zones...\n";
        
        foreach ($missingZoneIds as $zoneId) {
            // Get a sample restaurant name for this zone
            $sampleRestaurant = DB::table('restaurants')
                ->where('zone_id', $zoneId)
                ->first();
            
            $zoneName = "Zone {$zoneId}";
            if ($sampleRestaurant) {
                $zoneName = "Zone {$zoneId}";
            }
            
            // Use the same coordinates format as existing zone
            $zoneData = [
                'name' => $zoneName,
                'coordinates' => $coordinatesFormat, // Use existing format
                'status' => 1, // Active
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Add default values for other columns
            $zoneData['minimum_shipping_charge'] = 0;
            $zoneData['per_km_shipping_charge'] = 0;
            $zoneData['maximum_shipping_charge'] = 0;
            $zoneData['max_cod_order_amount'] = 0;
            $zoneData['increased_delivery_fee'] = 0;
            $zoneData['increased_delivery_fee_status'] = 0;
            $zoneData['increase_delivery_charge_message'] = '';
            $zoneData['display_name'] = $zoneName;
            $zoneData['restaurant_wise_topic'] = '';
            $zoneData['customer_wise_topic'] = '';
            $zoneData['deliveryman_wise_topic'] = '';
            
            DB::table('zones')->insert($zoneData);
            echo "✓ Created zone: {$zoneData['name']} (ID: {$zoneId})\n";
        }
        
        echo "\n=== Verification ===\n";
        
        // Check restaurants per zone after fix
        $zoneDistribution = DB::table('restaurants')
            ->select('zone_id', DB::raw('COUNT(*) as count'))
            ->groupBy('zone_id')
            ->orderBy('zone_id')
            ->get();
        
        echo "Restaurant distribution after fix:\n";
        foreach ($zoneDistribution as $zone) {
            echo "- Zone {$zone->zone_id}: {$zone->count} restaurants\n";
        }
        
        // Test API simulation
        $allValidZoneIds = DB::table('zones')->pluck('id')->toArray();
        $restaurantsInValidZones = DB::table('restaurants')->whereIn('zone_id', $allValidZoneIds)->count();
        
        echo "\nRestaurants now visible to API: {$restaurantsInValidZones}/44\n";
        
        if ($restaurantsInValidZones === 44) {
            echo "✅ SUCCESS: All restaurants will now be visible in the API!\n";
        } else {
            echo "⚠️  WARNING: Some restaurants may still not be visible.\n";
        }
        
        // Show all zones
        echo "\nAll zones after fix:\n";
        $allZones = DB::table('zones')->get();
        foreach ($allZones as $zone) {
            echo "- Zone ID: {$zone->id}, Name: {$zone->name}, Status: {$zone->status}\n";
        }
        
    } else {
        echo "No missing zones found. All restaurants have valid zone_ids.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
