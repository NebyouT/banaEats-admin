<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\PageBuilderBanner;
use App\Models\BuilderPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PageBuilderBannerController extends Controller
{
    public function index(Request $request)
    {
        $query = PageBuilderBanner::with(['builderPage', 'storage']);
        
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status === 'active' ? 1 : 0);
        }
        
        $banners = $query->latest()->paginate(20);
        
        return view('admin-views.page-builder-banner.index', compact('banners'));
    }
    
    public function create()
    {
        $pages = BuilderPage::where('is_published', 1)->orderBy('title')->get();
        return view('admin-views.page-builder-banner.create', compact('pages'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'type' => 'required|in:square,horizontal',
            'builder_page_id' => 'nullable|exists:builder_pages,id',
            'status' => 'required|in:0,1',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $banner = new PageBuilderBanner();
        $banner->title = $request->title;
        $banner->type = $request->type;
        $banner->media_type = $request->media_type ?? 'image';
        $banner->builder_page_id = $request->builder_page_id;
        $banner->status = $request->status;
        
        if ($request->hasFile('image')) {
            $banner->image = Helpers::upload('page-builder-banner/', 'png', $request->file('image'));
        }
        
        $banner->save();
        
        toastr()->success(translate('Banner created successfully'));
        return redirect()->route('admin.page-builder-banner.index');
    }
    
    public function edit($id)
    {
        $banner = PageBuilderBanner::with('builderPage')->findOrFail($id);
        $pages = BuilderPage::where('is_published', 1)->orderBy('title')->get();
        return view('admin-views.page-builder-banner.edit', compact('banner', 'pages'));
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'type' => 'required|in:square,horizontal',
            'builder_page_id' => 'nullable|exists:builder_pages,id',
            'status' => 'required|in:0,1',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $banner = PageBuilderBanner::findOrFail($id);
        $banner->title = $request->title;
        $banner->type = $request->type;
        $banner->media_type = $request->media_type ?? 'image';
        $banner->builder_page_id = $request->builder_page_id;
        $banner->status = $request->status;
        
        if ($request->hasFile('image')) {
            $oldImage = $banner->image;
            $banner->image = Helpers::update('page-builder-banner/', $oldImage, 'png', $request->file('image'));
        }
        
        $banner->save();
        
        toastr()->success(translate('Banner updated successfully'));
        return redirect()->route('admin.page-builder-banner.index');
    }
    
    public function destroy($id)
    {
        $banner = PageBuilderBanner::findOrFail($id);
        
        if ($banner->image) {
            Helpers::delete('page-builder-banner/' . $banner->image);
        }
        
        $banner->delete();
        
        toastr()->success(translate('Banner deleted successfully'));
        return back();
    }
    
    public function status(Request $request)
    {
        $banner = PageBuilderBanner::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();
        
        return response()->json(['success' => true, 'message' => translate('Status updated successfully')]);
    }
}
