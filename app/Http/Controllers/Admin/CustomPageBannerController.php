<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use App\Models\CustomPageBanner;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomPageBannerController extends Controller
{
    // Allowed MIME types for banner media
    private const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp'];
    private const GIF_MIMES   = ['image/gif'];
    private const VIDEO_MIMES = ['video/mp4', 'video/webm', 'video/quicktime'];

    public function index()
    {
        $banners = CustomPageBanner::with(['storage', 'linkedPage'])->latest()->paginate(config('default_pagination'));
        return view('admin-views.custom-page-banner.index', compact('banners'));
    }

    public function create()
    {
        $pages = CustomPage::where('status', 1)->orderBy('title')->get(['id', 'title', 'slug']);
        return view('admin-views.custom-page-banner.create', compact('pages'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|max:191',
            'type'    => 'required|in:square,wide',
            'page_id' => 'nullable|exists:custom_pages,id',
            'media'   => 'required|file|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $file      = $request->file('media');
        $mediaType = $this->detectMediaType($file);

        if (!$mediaType) {
            return response()->json(['errors' => [['code' => 'media', 'message' => 'Unsupported file type. Allowed: JPG, PNG, WebP, GIF, MP4, WebM, MOV.']]]);
        }

        $banner             = new CustomPageBanner();
        $banner->title      = $request->title;
        $banner->type       = $request->type;
        $banner->page_id    = $request->page_id ?: null;
        $banner->media_type = $mediaType;
        $banner->status     = 1;
        $banner->image      = $this->uploadMedia($file, $mediaType);
        $banner->save();

        Toastr::success('Banner created successfully.');
        return response()->json([], 200);
    }

    public function edit(CustomPageBanner $custom_page_banner)
    {
        $custom_page_banner->load('storage');
        $pages = CustomPage::where('status', 1)->orderBy('title')->get(['id', 'title', 'slug']);
        return view('admin-views.custom-page-banner.edit', compact('custom_page_banner', 'pages'));
    }

    public function update(Request $request, CustomPageBanner $custom_page_banner)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|max:191',
            'type'    => 'required|in:square,wide',
            'page_id' => 'nullable|exists:custom_pages,id',
            'media'   => 'nullable|file|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $custom_page_banner->title   = $request->title;
        $custom_page_banner->type    = $request->type;
        $custom_page_banner->page_id = $request->page_id ?: null;

        if ($request->hasFile('media')) {
            $file      = $request->file('media');
            $mediaType = $this->detectMediaType($file);

            if (!$mediaType) {
                return response()->json(['errors' => [['code' => 'media', 'message' => 'Unsupported file type. Allowed: JPG, PNG, WebP, GIF, MP4, WebM, MOV.']]]);
            }

            // Delete old file
            if ($custom_page_banner->image) {
                Helpers::check_and_delete('custom-page-banner', $custom_page_banner->image);
            }

            $custom_page_banner->media_type = $mediaType;
            $custom_page_banner->image      = $this->uploadMedia($file, $mediaType);
        }

        $custom_page_banner->save();

        Toastr::success('Banner updated successfully.');
        return response()->json([], 200);
    }

    public function status(Request $request)
    {
        $banner = CustomPageBanner::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();
        Toastr::success('Status updated successfully.');
        return back();
    }

    public function delete(CustomPageBanner $custom_page_banner)
    {
        if ($custom_page_banner->image) {
            Helpers::check_and_delete('custom-page-banner', $custom_page_banner->image);
        }
        $custom_page_banner->delete();
        Toastr::success('Banner deleted successfully.');
        return back();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function detectMediaType($file): ?string
    {
        $mime = $file->getMimeType();
        if (in_array($mime, self::IMAGE_MIMES)) return 'image';
        if (in_array($mime, self::GIF_MIMES))   return 'gif';
        if (in_array($mime, self::VIDEO_MIMES))  return 'video';
        return null;
    }

    private function uploadMedia($file, string $mediaType): string
    {
        $ext       = $mediaType === 'video' ? $file->getClientOriginalExtension() : ($mediaType === 'gif' ? 'gif' : 'png');
        $filename  = Str::uuid() . '.' . $ext;
        $file->storeAs('custom-page-banner', $filename, 'public');
        return $filename;
    }
}
