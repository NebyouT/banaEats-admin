<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PageBuilderBanner;
use Illuminate\Http\Request;

class PageBuilderBannerController extends Controller
{
    /**
     * GET /api/v1/page-builder-banners
     * Returns all active page builder banners, optionally filtered by type.
     */
    public function list(Request $request)
    {
        $query = PageBuilderBanner::with(['storage', 'builderPage'])->active();

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
     * GET /api/v1/page-builder-banners/{id}
     * Returns a single banner with its linked page builder page.
     */
    public function details($id)
    {
        $banner = PageBuilderBanner::with(['storage', 'builderPage'])->active()->find($id);

        if (!$banner) {
            return response()->json([
                'errors' => [['code' => 'not_found', 'message' => 'Banner not found or inactive.']],
            ], 404);
        }

        $formatted = $this->formatBanner($banner);

        // Add linked page builder page details
        if ($banner->builderPage) {
            $page = $banner->builderPage;
            $formatted['linked_page'] = [
                'id'          => $page->id,
                'title'       => $page->title,
                'slug'        => $page->slug,
                'description' => $page->description,
                'web_url'     => url('/page/' . $page->slug),
                'is_published' => (bool) $page->is_published,
            ];
        } else {
            $formatted['linked_page'] = null;
        }

        return response()->json($formatted);
    }

    private function formatBanner(PageBuilderBanner $banner): array
    {
        $data = [
            'id'             => $banner->id,
            'title'          => $banner->title,
            'type'           => $banner->type,
            'aspect_ratio'   => $banner->type === 'square' ? '1:1' : '5:1',
            'media_type'     => $banner->media_type ?? 'image',
            'media_full_url' => $banner->image_full_url,
            'page_id'        => $banner->builder_page_id,
            'status'         => $banner->status,
            'is_active'      => (bool) $banner->status,
            'created_at'     => $banner->created_at,
            'updated_at'     => $banner->updated_at,
        ];

        // Add web URL if page exists
        if ($banner->builderPage) {
            $data['web_url'] = url('/page/' . $banner->builderPage->slug);
        } else {
            $data['web_url'] = null;
        }

        return $data;
    }
}
