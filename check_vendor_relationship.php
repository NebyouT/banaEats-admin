<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Vendor-Restaurant Relationship ===\n\n";

try {
    // Check all vendor_ids in restaurants table
    $restaurantVendorIds = DB::table('restaurants')
        ->distinct()
        ->pluck('vendor_id')
        ->toArray();
    
    echo "Vendor IDs found in restaurants table: " . implode(', ', $restaurantVendorIds) . "\n";
    
    // Check all vendor IDs that actually exist
    $existingVendorIds = DB::table('vendors')
        ->pluck('id')
        ->toArray();
    
    echo "Vendor IDs that exist in vendors table: " . implode(', ', $existingVendorIds) . "\n";
    
    // Find missing vendors
    $missingVendorIds = array_diff($restaurantVendorIds, $existingVendorIds);
    
    if (!empty($missingVendorIds)) {
        echo "\n⚠️  MISSING VENDORS: " . implode(', ', $missingVendorIds) . "\n";
        echo "These vendor_ids exist in restaurants but not in vendors table!\n";
        
        // Show restaurants with missing vendors
        echo "\nRestaurants with missing vendors:\n";
        $restaurantsWithMissingVendors = DB::table('restaurants')
            ->whereNotIn('vendor_id', $existingVendorIds)
            ->select('id', 'name', 'vendor_id')
            ->limit(10)
            ->get();
        
        foreach ($restaurantsWithMissingVendors as $restaurant) {
            echo "- Restaurant: {$restaurant->name} (ID: {$restaurant->id}) -> Missing Vendor ID: {$restaurant->vendor_id}\n";
        }
        
        // Create missing vendors
        echo "\n=== Creating Missing Vendors ===\n";
        foreach ($missingVendorIds as $vendorId) {
            // Get a sample restaurant for this vendor
            $sampleRestaurant = DB::table('restaurants')
                ->where('vendor_id', $vendorId)
                ->first();
            
            if ($sampleRestaurant) {
                // Create a basic vendor record
                $vendorData = [
                    'id' => $vendorId, // Use the existing vendor_id
                    'f_name' => 'Vendor',
                    'l_name' => $vendorId,
                    'email' => "vendor{$vendorId}@example.com",
                    'phone' => "000000000{$vendorId}",
                    'password' => bcrypt('password123'), // Default password
                    'status' => 1, // Active
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Check if vendors table has additional required columns
                $vendorColumns = DB::getSchemaBuilder()->getColumnListing('vendors');
                echo "Vendor table columns: " . implode(', ', $vendorColumns) . "\n";
                
                // Add default values for common vendor columns
                if (in_array('email_verified_at', $vendorColumns)) {
                    $vendorData['email_verified_at'] = now();
                }
                
                try {
                    DB::table('vendors')->insert($vendorData);
                    echo "✓ Created missing vendor: Vendor {$vendorId} (ID: {$vendorId})\n";
                } catch (Exception $e) {
                    echo "✗ Failed to create vendor {$vendorId}: " . $e->getMessage() . "\n";
                }
            }
        }
        
    } else {
        echo "\n✅ All vendor_ids in restaurants exist in vendors table.\n";
    }
    
    // Final verification
    echo "\n=== Final Verification ===\n";
    
    $totalRestaurants = DB::table('restaurants')->count();
    $restaurantsWithExistingVendors = DB::table('restaurants')
        ->join('vendors', 'restaurants.vendor_id', '=', 'vendors.id')
        ->count();
    
    $restaurantsWithActiveVendors = DB::table('restaurants')
        ->join('vendors', 'restaurants.vendor_id', '=', 'vendors.id')
        ->where('vendors.status', 1)
        ->count();
    
    echo "Total restaurants: {$totalRestaurants}\n";
    echo "Restaurants with existing vendors: {$restaurantsWithExistingVendors}\n";
    echo "Restaurants with active vendors: {$restaurantsWithActiveVendors}\n";
    
    if ($restaurantsWithExistingVendors == $totalRestaurants) {
        echo "✅ All restaurants now have corresponding vendors!\n";
        
        if ($restaurantsWithActiveVendors == $totalRestaurants) {
            echo "✅ All restaurants will be visible in admin panel!\n";
        } else {
            echo "⚠️  Some vendors are still inactive. Activating all vendors...\n";
            
            // Activate all vendors
            DB::table('vendors')->update(['status' => 1]);
            echo "✓ All vendors activated!\n";
            
            $finalActiveCount = DB::table('restaurants')
                ->join('vendors', 'restaurants.vendor_id', '=', 'vendors.id')
                ->where('vendors.status', 1)
                ->count();
            
            echo "Final count of visible restaurants: {$finalActiveCount}/{$totalRestaurants}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
