<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CustomPageController extends Controller
{
    /**
     * GET /api/v1/custom-pages
     * Returns a list of all active custom pages (id, title, slug, status).
     */
    public function list(Request $request)
    {
        $pages = CustomPage::active()
            ->select('id', 'title', 'slug', 'subtitle', 'promotional_text',
                     'background_color', 'background_image', 'status', 'updated_at')
            ->latest()
            ->get()
            ->map(function ($page) {
                return [
                    'id'                       => $page->id,
                    'title'                    => $page->title,
                    'slug'                     => $page->slug,
                    'subtitle'                 => $page->subtitle,
                    'promotional_text'         => $page->promotional_text,
                    'background_color'          => $page->background_color,
                    'background_media_type'     => $page->background_media_type ?? 'image',
                    'background_image_full_url' => $page->background_image_full_url,
                    'updated_at'                => $page->updated_at,
                ];
            });

        return response()->json(['pages' => $pages], 200);
    }

    /**
     * GET /api/v1/custom-pages/{slug}
     * Returns full page data including products and restaurants.
     */
    public function details(Request $request, $slug)
    {
        $page = CustomPage::active()->where('slug', $slug)->first();

        if (!$page) {
            return response()->json(['errors' => [['code' => 'not_found', 'message' => 'Page not found']]], 404);
        }

        // Resolve products — load all relations required by product_data_formatting
        $productIds = $page->product_ids ?? [];
        $products   = [];
        if (!empty($productIds)) {
            $rawProducts = Food::withoutGlobalScopes()
                ->with([
                    'storage',
                    'translations',
                    'rating',
                    'tags',
                    'nutritions',
                    'allergies',
                    'taxVats',
                    'restaurant' => function ($q) {
                        $q->with(['discount', 'cuisine', 'restaurant_sub', 'restaurant_config']);
                    },
                ])
                ->whereIn('id', $productIds)
                ->where('status', 1)
                ->get();

            // Preserve the admin-defined order
            $indexed = $rawProducts->keyBy('id');
            foreach ($productIds as $pid) {
                if ($indexed->has($pid)) {
                    $products[] = Helpers::product_data_formatting(
                        data: $indexed->get($pid),
                        multi_data: false,
                        trans: false,
                        local: app()->getLocale()
                    );
                }
            }
        }

        // Resolve restaurants
        $restaurantIds = $page->restaurant_ids ?? [];
        $restaurants   = [];
        if (!empty($restaurantIds)) {
            $rawRestaurants = Restaurant::withoutGlobalScopes()
                ->with(['storage', 'cuisine'])
                ->whereIn('id', $restaurantIds)
                ->where('status', 1)
                ->get();

            $indexed = $rawRestaurants->keyBy('id');
            foreach ($restaurantIds as $rid) {
                if ($indexed->has($rid)) {
                    $r = $indexed->get($rid);
                    $restaurants[] = [
                        'id'                   => $r->id,
                        'name'                 => $r->name,
                        'logo_full_url'        => $r->logo_full_url ?? null,
                        'cover_photo_full_url' => $r->cover_photo_full_url ?? null,
                        'address'              => $r->address,
                        'avg_rating'           => (float) ($r->avg_rating ?? 0),
                        'rating_count'         => (int) ($r->rating_count ?? 0),
                        'delivery_time'        => $r->delivery_time,
                        'minimum_order'        => (float) ($r->minimum_order ?? 0),
                        'cuisines'             => $r->cuisine->pluck('name'),
                    ];
                }
            }
        }

        return response()->json([
            'id'                        => $page->id,
            'title'                     => $page->title,
            'slug'                      => $page->slug,
            'subtitle'                  => $page->subtitle,
            'promotional_text'          => $page->promotional_text,
            'background_color'          => $page->background_color,
            'background_media_type'     => $page->background_media_type ?? 'image',
            'background_image_full_url' => $page->background_image_full_url,
            'products'                  => $products,
            'restaurants'               => $restaurants,
        ], 200);
    }
}
