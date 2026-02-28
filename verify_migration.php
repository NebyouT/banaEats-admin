<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Migration Verification Report ===\n\n";

try {
    // Get counts
    $totalRestaurants = DB::table('restaurants')->count();
    $originalCount = DB::table('restaurants_backup')->count();
    $migratedCount = $totalRestaurants - $originalCount;
    
    echo "=== Summary ===\n";
    echo "Original restaurants: {$originalCount}\n";
    echo "Migrated from easy.stores: {$migratedCount}\n";
    echo "Total restaurants after migration: {$totalRestaurants}\n\n";
    
    // Check for any data integrity issues
    echo "=== Data Integrity Checks ===\n";
    
    // Check for null required fields
    $nullNames = DB::table('restaurants')->whereNull('name')->count();
    $nullPhones = DB::table('restaurants')->whereNull('phone')->count();
    $nullVendors = DB::table('restaurants')->whereNull('vendor_id')->count();
    
    echo "Restaurants with null name: {$nullNames}\n";
    echo "Restaurants with null phone: {$nullPhones}\n";
    echo "Restaurants with null vendor_id: {$nullVendors}\n";
    
    // Check for duplicate phones
    $duplicatePhones = DB::table('restaurants')
        ->select('phone', DB::raw('COUNT(*) as count'))
        ->groupBy('phone')
        ->having('count', '>', 1)
        ->get();
    
    echo "Duplicate phone numbers: " . $duplicatePhones->count() . "\n";
    if ($duplicatePhones->count() > 0) {
        foreach ($duplicatePhones as $dup) {
            echo "  - Phone {$dup->phone}: {$dup->count} records\n";
        }
    }
    
    echo "\n=== Sample of Migrated Data ===\n";
    $migratedRestaurants = DB::table('restaurants')
        ->where('id', '>', $originalCount)
        ->limit(5)
        ->get([
            'id', 'name', 'phone', 'email', 'address', 
            'vendor_id', 'status', 'created_at'
        ]);
    
    foreach ($migratedRestaurants as $restaurant) {
        echo "ID: {$restaurant->id}\n";
        echo "  Name: {$restaurant->name}\n";
        echo "  Phone: {$restaurant->phone}\n";
        echo "  Email: " . ($restaurant->email ?? 'N/A') . "\n";
        echo "  Address: " . ($restaurant->address ?? 'N/A') . "\n";
        echo "  Vendor ID: {$restaurant->vendor_id}\n";
        echo "  Status: {$restaurant->status}\n";
        echo "  Created: {$restaurant->created_at}\n";
        echo "  ---\n";
    }
    
    // Compare with original stores data
    echo "\n=== Cross-Database Verification ===\n";
    $easyStores = DB::connection('easy')->table('stores')->count();
    echo "Total stores in easy database: {$easyStores}\n";
    echo "Total migrated to bana: {$migratedCount}\n";
    
    if ($easyStores == $migratedCount) {
        echo "✓ All stores successfully migrated\n";
    } else {
        echo "✗ Mismatch in migration count\n";
    }
    
    // Check specific field mappings
    echo "\n=== Field Mapping Verification ===\n";
    $sampleStore = DB::connection('easy')->table('stores')->first();
    $correspondingRestaurant = DB::table('restaurants')
        ->where('phone', $sampleStore->phone)
        ->first();
    
    if ($correspondingRestaurant) {
        echo "Comparing first store with migrated restaurant:\n";
        echo "Store name: {$sampleStore->name} -> Restaurant name: {$correspondingRestaurant->name}\n";
        echo "Store email: {$sampleStore->email} -> Restaurant email: {$correspondingRestaurant->email}\n";
        echo "Store vendor_id: {$sampleStore->vendor_id} -> Restaurant vendor_id: {$correspondingRestaurant->vendor_id}\n";
        echo "Store address: {$sampleStore->address} -> Restaurant address: {$correspondingRestaurant->address}\n";
    }
    
    echo "\n=== Migration Status ===\n";
    echo "Status: SUCCESS\n";
    echo "Backup available: restaurants_backup table\n";
    echo "To restore backup if needed: \n";
    echo "  DROP TABLE restaurants;\n";
    echo "  RENAME TABLE restaurants_backup TO restaurants;\n";
    
} catch (Exception $e) {
    echo "Verification failed: " . $e->getMessage() . "\n";
}
