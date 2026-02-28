<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fixing Vendor Status Issue ===\n\n";

try {
    // Check current vendor status
    $vendors = DB::table('vendors')->get();
    echo "Current vendor statuses:\n";
    foreach ($vendors as $vendor) {
        $statusText = $vendor->status ? 'ACTIVE' : 'INACTIVE';
        echo "- Vendor ID: {$vendor->id}, Name: {$vendor->f_name} {$vendor->l_name}, Status: {$statusText}\n";
    }
    
    // Get vendors that need to be activated (those who own restaurants)
    $vendorsWithRestaurants = DB::table('vendors')
        ->join('restaurants', 'vendors.id', '=', 'restaurants.vendor_id')
        ->distinct()
        ->select('vendors.*')
        ->get();
    
    echo "\n=== Activating Vendors with Restaurants ===\n";
    
    $activatedCount = 0;
    foreach ($vendorsWithRestaurants as $vendor) {
        if ($vendor->status == 0) {
            // Activate the vendor
            DB::table('vendors')
                ->where('id', $vendor->id)
                ->update(['status' => 1]);
            
            $vendorName = trim($vendor->f_name . ' ' . $vendor->l_name);
            echo "✓ Activated Vendor: {$vendorName} (ID: {$vendor->id})\n";
            $activatedCount++;
        } else {
            $vendorName = trim($vendor->f_name . ' ' . $vendor->l_name);
            echo "- Vendor already active: {$vendorName} (ID: {$vendor->id})\n";
        }
    }
    
    echo "\n=== Verification ===\n";
    
    // Check new vendor status distribution
    $newVendorStatusCounts = DB::table('vendors')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
    
    echo "New vendor status distribution:\n";
    foreach ($newVendorStatusCounts as $status) {
        $statusText = $status->status ? 'Active (1)' : 'Inactive (0)';
        echo "{$statusText}: {$status->count} vendors\n";
    }
    
    // Check restaurant visibility after fix
    $restaurantsWithActiveVendors = DB::table('restaurants')
        ->join('vendors', 'restaurants.vendor_id', '=', 'vendors.id')
        ->where('vendors.status', 1)
        ->count();
    
    $totalRestaurants = DB::table('restaurants')->count();
    
    echo "\nRestaurant visibility after fix:\n";
    echo "Total restaurants: {$totalRestaurants}\n";
    echo "Restaurants with active vendors: {$restaurantsWithActiveVendors}\n";
    echo "Restaurants now visible in admin: {$restaurantsWithActiveVendors}\n";
    
    if ($restaurantsWithActiveVendors == $totalRestaurants) {
        echo "✅ SUCCESS: All restaurants will now be visible in the admin panel!\n";
    } else {
        echo "⚠️  Some restaurants may still not be visible.\n";
    }
    
    // Show which restaurants are now visible
    echo "\n=== Sample of Restaurants Now Visible ===\n";
    $visibleRestaurants = DB::table('restaurants')
        ->join('vendors', 'restaurants.vendor_id', '=', 'vendors.id')
        ->where('vendors.status', 1)
        ->select('restaurants.id', 'restaurants.name', 'vendors.f_name', 'vendors.l_name')
        ->limit(10)
        ->get();
    
    foreach ($visibleRestaurants as $restaurant) {
        $vendorName = trim($restaurant->f_name . ' ' . $restaurant->l_name);
        echo "- Restaurant: {$restaurant->name} (ID: {$restaurant->id}), Vendor: {$vendorName}\n";
    }
    
    echo "\n=== Summary ===\n";
    echo "Activated {$activatedCount} vendors\n";
    echo "All {$restaurantsWithActiveVendors} restaurants should now be visible at http://127.0.0.1:8000/admin/restaurant/list\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
