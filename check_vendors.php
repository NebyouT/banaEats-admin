<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Vendor Status Issue ===\n\n";

try {
    // Check vendor status distribution
    $vendorStatusCounts = DB::table('vendors')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
    
    echo "=== Vendor Status Distribution ===\n";
    foreach ($vendorStatusCounts as $status) {
        $statusText = $status->status ? 'Active (1)' : 'Inactive (0)';
        echo "{$statusText}: {$status->count} vendors\n";
    }
    
    // Check restaurant-vendor relationship
    echo "\n=== Restaurant-Vendor Status Analysis ===\n";
    
    $restaurantVendorStatus = DB::table('restaurants')
        ->join('vendors', 'restaurants.vendor_id', '=', 'vendors.id')
        ->select('vendors.status as vendor_status', DB::raw('COUNT(*) as count'))
        ->groupBy('vendors.status')
        ->get();
    
    foreach ($restaurantVendorStatus as $status) {
        $statusText = $status->vendor_status ? 'Active Vendor (1)' : 'Inactive Vendor (0)';
        echo "{$statusText}: {$status->count} restaurants\n";
    }
    
    // Show restaurants with inactive vendors
    echo "\n=== Restaurants with Inactive Vendors ===\n";
    $inactiveVendorRestaurants = DB::table('restaurants')
        ->join('vendors', 'restaurants.vendor_id', '=', 'vendors.id')
        ->where('vendors.status', 0)
        ->select('restaurants.id', 'restaurants.name', 'restaurants.vendor_id', 'vendors.f_name', 'vendors.l_name', 'vendors.email')
        ->limit(10)
        ->get();
    
    if ($inactiveVendorRestaurants->count() > 0) {
        foreach ($inactiveVendorRestaurants as $restaurant) {
            $vendorName = trim($restaurant->f_name . ' ' . $restaurant->l_name);
            echo "- Restaurant: {$restaurant->name} (ID: {$restaurant->id})\n";
            echo "  Vendor: {$vendorName} (ID: {$restaurant->vendor_id}, Email: {$restaurant->email}) - INACTIVE\n";
            echo "  ---\n";
        }
    } else {
        echo "No restaurants with inactive vendors found.\n";
    }
    
    // Show restaurants with active vendors
    echo "\n=== Restaurants with Active Vendors (These should be visible) ===\n";
    $activeVendorRestaurants = DB::table('restaurants')
        ->join('vendors', 'restaurants.vendor_id', '=', 'vendors.id')
        ->where('vendors.status', 1)
        ->select('restaurants.id', 'restaurants.name', 'restaurants.vendor_id', 'vendors.f_name', 'vendors.l_name', 'vendors.email')
        ->limit(10)
        ->get();
    
    foreach ($activeVendorRestaurants as $restaurant) {
        $vendorName = trim($restaurant->f_name . ' ' . $restaurant->l_name);
        echo "- Restaurant: {$restaurant->name} (ID: {$restaurant->id})\n";
        echo "  Vendor: {$vendorName} (ID: {$restaurant->vendor_id}, Email: {$restaurant->email}) - ACTIVE\n";
        echo "  ---\n";
    }
    
    // Summary
    $totalRestaurants = DB::table('restaurants')->count();
    $restaurantsWithActiveVendors = DB::table('restaurants')
        ->join('vendors', 'restaurants.vendor_id', '=', 'vendors.id')
        ->where('vendors.status', 1)
        ->count();
    
    echo "\n=== Summary ===\n";
    echo "Total restaurants: {$totalRestaurants}\n";
    echo "Restaurants with active vendors: {$restaurantsWithActiveVendors}\n";
    echo "Restaurants that should be visible in admin: {$restaurantsWithActiveVendors}\n";
    
    if ($restaurantsWithActiveVendors == 2) {
        echo "✅ This matches what you're seeing in the admin panel!\n";
        echo "The issue is that most vendors have status = 0 (inactive).\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
