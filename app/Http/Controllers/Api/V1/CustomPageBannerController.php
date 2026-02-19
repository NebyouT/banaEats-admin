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
        $query = CustomPageBanner::with('storage')->active();

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
        $banner = CustomPageBanner::with(['storage', 'linkedPage'])->active()->find($id);

        if (!$banner) {
            return response()->json([
                'errors' => [['code' => 'not_found', 'message' => 'Banner not found or inactive.']],
            ], 404);
        }

        $formatted = $this->formatBanner($banner);

        $page = $banner->linkedPage;
        $formatted['linked_page'] = $page ? [
            'id'                        => $page->id,
            'title'                     => $page->title,
            'slug'                      => $page->slug,
            'subtitle'                  => $page->subtitle,
            'promotional_text'          => $page->promotional_text,
            'background_color'          => $page->background_color,
            'background_media_type'     => $page->background_media_type ?? 'image',
            'background_image_full_url' => $page->background_image_full_url,
        ] : null;

        return response()->json($formatted);
    }

    private function formatBanner(CustomPageBanner $banner): array
    {
        return [
            'id'             => $banner->id,
            'title'          => $banner->title,
            'type'           => $banner->type,
            'aspect_ratio'   => $banner->type === 'square' ? '1:1' : '5:1',
            'media_type'     => $banner->media_type ?? 'image',
            'media_full_url' => $banner->image_full_url,
            'page_id'        => $banner->page_id,
            'status'         => $banner->status,
            'is_active'      => (bool) $banner->status,
            'created_at'     => $banner->created_at,
            'updated_at'     => $banner->updated_at,
        ];
    }
}
