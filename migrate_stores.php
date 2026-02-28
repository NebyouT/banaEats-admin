<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Starting Migration from easy.stores to bana.restaurants ===\n\n";

try {
    // First, create a backup of the restaurants table
    echo "Creating backup of restaurants table...\n";
    Schema::dropIfExists('restaurants_backup');
    DB::statement('CREATE TABLE restaurants_backup AS SELECT * FROM restaurants');
    echo "Backup created successfully.\n\n";

    // Get all stores from easy database
    $stores = DB::connection('easy')->table('stores')->get();
    echo "Found " . $stores->count() . " stores to migrate.\n\n";

    $migratedCount = 0;
    $skippedCount = 0;
    $errorCount = 0;

    foreach ($stores as $store) {
        try {
            // Check if restaurant with this phone already exists
            $existing = DB::table('restaurants')->where('phone', $store->phone)->first();
            
            if ($existing) {
                echo "SKIPPED: Store '{$store->name}' already exists (phone: {$store->phone})\n";
                $skippedCount++;
                continue;
            }

            // Prepare the data for insertion
            $restaurantData = [
                'name' => $store->name,
                'phone' => $store->phone,
                'email' => $store->email,
                'logo' => $store->logo,
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
                'address' => $store->address,
                'footer_text' => $store->footer_text,
                'minimum_order' => $store->minimum_order ?? 0,
                'comission' => $store->comission ?? 0,
                'schedule_order' => $store->schedule_order ?? 0,
                'opening_time' => null, // stores table doesn't have this, will need to set default
                'closeing_time' => null, // stores table doesn't have this, will need to set default
                'status' => $store->status ?? 1,
                'vendor_id' => $store->vendor_id,
                'free_delivery' => $store->free_delivery ?? 0,
                'rating' => $store->rating,
                'cover_photo' => $store->cover_photo,
                'delivery' => $store->delivery ?? 1,
                'take_away' => $store->take_away ?? 1,
                'food_section' => $store->item_section ?? 1, // stores has item_section
                'tax' => $store->tax ?? 0,
                'zone_id' => $store->zone_id,
                'reviews_section' => $store->reviews_section ?? 1,
                'active' => $store->active ?? 1,
                'off_day' => $store->off_day ?? '',
                'gst' => $store->gst,
                'self_delivery_system' => $store->self_delivery_system ?? 0,
                'pos_system' => $store->pos_system ?? 0,
                'minimum_shipping_charge' => $store->minimum_shipping_charge ?? 0,
                'delivery_time' => $store->delivery_time,
                'veg' => $store->veg ?? 1,
                'non_veg' => $store->non_veg ?? 1,
                'order_count' => $store->order_count ?? 0,
                'total_order' => $store->total_order ?? 0,
                'per_km_shipping_charge' => $store->per_km_shipping_charge ?? 0,
                'restaurant_model' => $store->store_business_model, // stores has store_business_model
                'maximum_shipping_charge' => $store->maximum_shipping_charge,
                'slug' => $store->slug,
                'cutlery' => $store->cutlery ?? 0,
                'meta_title' => $store->meta_title,
                'meta_description' => $store->meta_description,
                'meta_image' => $store->meta_image,
                'announcement' => $store->announcement ?? 0,
                'announcement_message' => $store->announcement_message,
                'qr_code' => null, // stores doesn't have this
                'free_delivery_distance' => null, // stores doesn't have this
                'additional_data' => null, // stores doesn't have this
                'additional_documents' => null, // stores doesn't have this
                'package_id' => $store->package_id,
                'tin' => $store->tin,
                'tin_expire_date' => $store->tin_expire_date,
                'tin_certificate_image' => $store->tin_certificate_image,
                'created_at' => $store->created_at,
                'updated_at' => $store->updated_at,
            ];

            // Insert the restaurant
            $restaurantId = DB::table('restaurants')->insertGetId($restaurantData);
            
            echo "MIGRATED: '{$store->name}' -> Restaurant ID: {$restaurantId}\n";
            $migratedCount++;

        } catch (Exception $e) {
            echo "ERROR migrating '{$store->name}': " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }

    echo "\n=== Migration Summary ===\n";
    echo "Total stores processed: " . $stores->count() . "\n";
    echo "Successfully migrated: {$migratedCount}\n";
    echo "Skipped (duplicates): {$skippedCount}\n";
    echo "Errors: {$errorCount}\n";

    // Verify the results
    echo "\n=== Verification ===\n";
    $newRestaurantCount = DB::table('restaurants')->count();
    echo "Total restaurants after migration: {$newRestaurantCount}\n";
    
    echo "\nSample of migrated restaurants:\n";
    $sample = DB::table('restaurants')
        ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 hour')))
        ->limit(5)
        ->get(['id', 'name', 'phone', 'email']);
    
    foreach ($sample as $restaurant) {
        echo "- ID: {$restaurant->id}, Name: {$restaurant->name}, Phone: {$restaurant->phone}, Email: {$restaurant->email}\n";
    }

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    echo "You can restore from backup using: \n";
    echo "DB::statement('DROP TABLE restaurants');\n";
    echo "DB::statement('RENAME TABLE restaurants_backup TO restaurants');\n";
}
