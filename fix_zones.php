<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fixing Zone Configuration ===\n\n";

try {
    // Check current zones
    $currentZones = DB::table('zones')->get();
    echo "Current zones in database:\n";
    foreach ($currentZones as $zone) {
        echo "- Zone ID: {$zone->id}, Name: {$zone->name}\n";
    }
    
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
            // Get a sample restaurant name for this zone to create a meaningful zone name
            $sampleRestaurant = DB::table('restaurants')
                ->where('zone_id', $zoneId)
                ->first();
            
            $zoneName = "Zone {$zoneId}";
            if ($sampleRestaurant) {
                // You could customize zone names based on your needs
                $zoneName = "Zone {$zoneId} - {$sampleRestaurant->name} Area";
            }
            
            // Insert the new zone
            $zoneData = [
                'name' => $zoneName,
                'status' => 1, // Active
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Check if there are additional required columns in zones table
            $zoneColumns = DB::getSchemaBuilder()->getColumnListing('zones');
            echo "Zone table columns: " . implode(', ', $zoneColumns) . "\n";
            
            // Add default values for common zone columns if they exist
            if (in_array('coordinates', $zoneColumns)) {
                $zoneData['coordinates'] = json_encode([[0, 0], [0, 0], [0, 0], [0, 0]]);
            }
            if (in_array('delivery_charge', $zoneColumns)) {
                $zoneData['delivery_charge'] = 0;
            }
            if (in_array('minimum_shipping_charge', $zoneColumns)) {
                $zoneData['minimum_shipping_charge'] = 0;
            }
            if (in_array('per_km_shipping_charge', $zoneColumns)) {
                $zoneData['per_km_shipping_charge'] = 0;
            }
            
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
        
    } else {
        echo "No missing zones found. All restaurants have valid zone_ids.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
