<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\GamificationBanner;
use App\Models\GamificationGame;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GamificationBannerController extends Controller
{
    public function index()
    {
        $banners = GamificationBanner::with('game')
            ->latest()
            ->paginate(config('default_pagination', 25));

        return view('admin-views.gamification.banners.index', compact('banners'));
    }

    public function create()
    {
        $games = GamificationGame::where('status', 1)->get();
        $zones = Zone::active()->get();
        return view('admin-views.gamification.banners.create', compact('games', 'zones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:gamification_games,id',
            'title' => 'required|string|max:255',
            'placement' => 'required|in:home,restaurant,checkout,cart',
            'image' => 'nullable|image|max:2048',
        ]);

        $banner = new GamificationBanner();
        $banner->game_id = $request->game_id;
        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->background_color = $request->background_color ?? '#8DC63F';
        $banner->text_color = $request->text_color ?? '#FFFFFF';
        $banner->button_text = $request->button_text ?? 'Play Now';
        $banner->button_color = $request->button_color ?? '#F5D800';
        $banner->placement = $request->placement;
        $banner->priority = $request->priority ?? 0;
        $banner->status = $request->has('status') ? 1 : 0;
        $banner->start_date = $request->start_date;
        $banner->end_date = $request->end_date;
        $banner->zone_ids = $request->zone_ids;

        if ($request->hasFile('image')) {
            $banner->image = Helpers::upload('gamification/banners/', 'png', $request->file('image'));
        }

        $banner->save();

        return redirect()->route('admin.gamification.banners.index')
            ->with('success', 'Banner created successfully!');
    }

    public function edit($id)
    {
        $banner = GamificationBanner::findOrFail($id);
        $games = GamificationGame::where('status', 1)->get();
        $zones = Zone::active()->get();
        return view('admin-views.gamification.banners.edit', compact('banner', 'games', 'zones'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'game_id' => 'required|exists:gamification_games,id',
            'title' => 'required|string|max:255',
            'placement' => 'required|in:home,restaurant,checkout,cart',
            'image' => 'nullable|image|max:2048',
        ]);

        $banner = GamificationBanner::findOrFail($id);
        $banner->game_id = $request->game_id;
        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->background_color = $request->background_color ?? '#8DC63F';
        $banner->text_color = $request->text_color ?? '#FFFFFF';
        $banner->button_text = $request->button_text ?? 'Play Now';
        $banner->button_color = $request->button_color ?? '#F5D800';
        $banner->placement = $request->placement;
        $banner->priority = $request->priority ?? 0;
        $banner->status = $request->has('status') ? 1 : 0;
        $banner->start_date = $request->start_date;
        $banner->end_date = $request->end_date;
        $banner->zone_ids = $request->zone_ids;

        if ($request->hasFile('image')) {
            if ($banner->image) {
                $disk = Helpers::getDisk();
                if (Storage::disk($disk)->exists('gamification/banners/' . $banner->image)) {
                    Storage::disk($disk)->delete('gamification/banners/' . $banner->image);
                }
            }
            $banner->image = Helpers::upload('gamification/banners/', 'png', $request->file('image'));
        }

        $banner->save();

        return redirect()->route('admin.gamification.banners.index')
            ->with('success', 'Banner updated successfully!');
    }

    public function status(Request $request)
    {
        $banner = GamificationBanner::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();

        return response()->json(['success' => true, 'message' => 'Status updated!']);
    }

    public function destroy($id)
    {
        $banner = GamificationBanner::findOrFail($id);

        if ($banner->image) {
            $disk = Helpers::getDisk();
            if (Storage::disk($disk)->exists('gamification/banners/' . $banner->image)) {
                Storage::disk($disk)->delete('gamification/banners/' . $banner->image);
            }
        }

        $banner->delete();

        return redirect()->route('admin.gamification.banners.index')
            ->with('success', 'Banner deleted successfully!');
    }
}
