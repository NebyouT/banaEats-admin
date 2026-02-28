<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BuilderPage;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class PageBuilderController extends Controller
{
    /**
     * Get list of published pages
     */
    public function index(Request $request)
    {
        $pages = BuilderPage::published()
            ->select(['id', 'title', 'slug', 'description', 'page_type', 'settings', 'published_at'])
            ->latest('published_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pages->map(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'description' => $page->description,
                    'page_type' => $page->page_type,
                    'web_url' => url('/page/' . $page->slug),
                    'published_at' => $page->published_at?->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * Get page by slug - returns full structure for native rendering
     */
    public function show(Request $request, $slug)
    {
        $page = BuilderPage::where('slug', $slug)
            ->published()
            ->with(['sections.components'])
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        // Track view
        $page->views()->create([
            'user_id' => $request->user()?->id,
            'session_id' => $request->header('X-Session-Id'),
            'device_type' => $request->header('User-Agent'),
            'referrer' => $request->header('Referer'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->formatPageForApi($page),
        ]);
    }

    /**
     * Get page WebView URL
     */
    public function getWebViewUrl(Request $request, $slug)
    {
        $page = BuilderPage::where('slug', $slug)->published()->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'url' => url('/page/' . $page->slug),
                'title' => $page->title,
            ],
        ]);
    }

    /**
     * Format page data for API response
     */
    private function formatPageForApi(BuilderPage $page): array
    {
        $sections = [];

        foreach ($page->sections as $section) {
            if (!$section->is_visible) continue;

            $sectionData = [
                'id' => $section->id,
                'type' => $section->section_type,
                'name' => $section->name,
                'settings' => $section->settings,
                'components' => [],
            ];

            foreach ($section->components as $component) {
                if (!$component->is_visible) continue;

                $componentData = [
                    'id' => $component->id,
                    'type' => $component->component_type,
                    'content' => $component->content,
                    'settings' => $component->settings,
                    'action' => $component->action,
                ];

                // Expand product/restaurant data
                if (in_array($component->component_type, ['product_card', 'product_list'])) {
                    $componentData['products'] = $this->getProductsData($component);
                }

                if (in_array($component->component_type, ['restaurant_card', 'restaurant_list'])) {
                    $componentData['restaurants'] = $this->getRestaurantsData($component);
                }

                $sectionData['components'][] = $componentData;
            }

            $sections[] = $sectionData;
        }

        return [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'description' => $page->description,
            'page_type' => $page->page_type,
            'settings' => $page->settings,
            'sections' => $sections,
            'web_url' => url('/page/' . $page->slug),
        ];
    }

    /**
     * Get products data for a component
     */
    private function getProductsData($component): array
    {
        $content = $component->content ?? [];
        $ids = [];

        if (!empty($content['product_id'])) {
            $ids = [$content['product_id']];
        } elseif (!empty($content['product_ids'])) {
            $ids = $content['product_ids'];
        }

        if (empty($ids)) return [];

        $products = Food::whereIn('id', $ids)
            ->with(['restaurant:id,name', 'storage'])
            ->get();

        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'discount' => (float) ($product->discount ?? 0),
                'discount_type' => $product->discount_type ?? 'percent',
                'image' => $product->image_full_url,
                'rating' => (float) ($product->avg_rating ?? 0),
                'rating_count' => (int) ($product->rating_count ?? 0),
                'restaurant_id' => $product->restaurant_id,
                'restaurant_name' => $product->restaurant->name ?? '',
                'is_available' => (bool) $product->status,
            ];
        })->toArray();
    }

    /**
     * Get restaurants data for a component
     */
    private function getRestaurantsData($component): array
    {
        $content = $component->content ?? [];
        $ids = [];

        if (!empty($content['restaurant_id'])) {
            $ids = [$content['restaurant_id']];
        } elseif (!empty($content['restaurant_ids'])) {
            $ids = $content['restaurant_ids'];
        }

        if (empty($ids)) return [];

        $restaurants = Restaurant::whereIn('id', $ids)
            ->with(['storage'])
            ->get();

        return $restaurants->map(function ($restaurant) {
            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'logo' => $restaurant->logo_full_url,
                'cover_photo' => $restaurant->cover_photo_full_url ?? null,
                'address' => $restaurant->address,
                'rating' => (float) ($restaurant->rating ?? 0),
                'rating_count' => (int) ($restaurant->rating_count ?? 0),
                'delivery_time' => $restaurant->delivery_time ?? '20-30',
                'delivery_fee' => (float) ($restaurant->delivery_charge ?? 0),
                'minimum_order' => (float) ($restaurant->minimum_order ?? 0),
                'is_open' => (bool) ($restaurant->active ?? true),
            ];
        })->toArray();
    }
}
