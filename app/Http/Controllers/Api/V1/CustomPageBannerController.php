<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustomPageBanner;
use Illuminate\Http\Request;

class CustomPageBannerController extends Controller
{
    /**
     * GET /api/v1/custom-page-banners
     * Returns all active banners, optionally filtered by type.
     */
    public function list(Request $request)
    {
        $query = CustomPageBanner::with(['storage', 'linkedPage', 'builderPage'])->active();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $banners = $query->latest()->get();

        return response()->json([
            'banners' => $banners->map(function ($b) {
                return $this->formatBanner($b);
            })->values(),
        ]);
    }

    /**
     * GET /api/v1/custom-page-banners/{id}
     * Returns a single banner with its one linked page.
     */
    public function details($id)
    {
        $banner = CustomPageBanner::with(['storage', 'linkedPage', 'builderPage'])->active()->find($id);

        if (!$banner) {
            return response()->json([
                'errors' => [['code' => 'not_found', 'message' => 'Banner not found or inactive.']],
            ], 404);
        }

        $formatted = $this->formatBanner($banner);

        // Check if it's a page builder page or old custom page
        if ($banner->page_type === 'builder' && $banner->builderPage) {
            $page = $banner->builderPage;
            $formatted['linked_page'] = [
                'id'          => $page->id,
                'title'       => $page->title,
                'slug'        => $page->slug,
                'description' => $page->description,
                'page_type'   => 'builder',
                'web_url'     => url('/page/' . $page->slug),
                'is_published' => (bool) $page->is_published,
            ];
        } else if ($banner->linkedPage) {
            $page = $banner->linkedPage;
            $formatted['linked_page'] = [
                'id'                        => $page->id,
                'title'                     => $page->title,
                'slug'                      => $page->slug,
                'subtitle'                  => $page->subtitle,
                'promotional_text'          => $page->promotional_text,
                'background_color'          => $page->background_color,
                'background_media_type'     => $page->background_media_type ?? 'image',
                'background_image_full_url' => $page->background_image_full_url,
                'page_type'                 => 'custom',
            ];
        } else {
            $formatted['linked_page'] = null;
        }

        return response()->json($formatted);
    }

    private function formatBanner(CustomPageBanner $banner): array
    {
        $data = [
            'id'             => $banner->id,
            'title'          => $banner->title,
            'type'           => $banner->type,
            'aspect_ratio'   => $banner->type === 'square' ? '1:1' : '5:1',
            'media_type'     => $banner->media_type ?? 'image',
            'media_full_url' => $banner->image_full_url,
            'page_type'      => $banner->page_type ?? 'custom',
            'status'         => $banner->status,
            'is_active'      => (bool) $banner->status,
            'created_at'     => $banner->created_at,
            'updated_at'     => $banner->updated_at,
        ];

        // Add appropriate page ID and web URL based on page type
        if ($banner->page_type === 'builder' && $banner->builderPage) {
            $data['page_id'] = $banner->builder_page_id;
            $data['web_url'] = url('/page/' . $banner->builderPage->slug);
        } else if ($banner->linkedPage) {
            $data['page_id'] = $banner->page_id;
            $data['web_url'] = null; // Old custom pages don't have web URLs
        } else {
            $data['page_id'] = null;
            $data['web_url'] = null;
        }

        return $data;
    }
}
