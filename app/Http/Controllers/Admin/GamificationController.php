<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\GamificationGame;
use App\Models\GamificationPrize;
use App\Models\GamificationEligibilityRule;
use App\Models\GamificationGamePlay;
use App\Models\Zone;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GamificationController extends Controller
{
    public function index(Request $request)
    {
        $games = GamificationGame::withCount(['prizes', 'gamePlays'])
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('priority')
            ->paginate(20);

        return view('admin-views.gamification.index', compact('games'));
    }

    public function create()
    {
        $zones = Zone::active()->get();
        $restaurants = Restaurant::active()->get();
        
        return view('admin-views.gamification.create', compact('zones', 'restaurants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:spin_wheel,scratch_card,slot_machine,mystery_box,decision_roulette',
            'plays_per_day' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'background_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $game = new GamificationGame();
            $game->name = $request->name;
            $game->slug = Str::slug($request->name);
            $game->type = $request->type;
            $game->description = $request->description;
            $game->status = $request->has('status') ? 1 : 0;
            $game->first_play_always_wins = $request->has('first_play_always_wins') ? 1 : 0;
            $game->plays_per_day = $request->plays_per_day;
            $game->plays_per_week = $request->plays_per_week;
            $game->cooldown_minutes = $request->cooldown_minutes ?? 0;
            $game->start_date = $request->start_date;
            $game->end_date = $request->end_date;
            $game->priority = $request->priority ?? 0;
            $game->button_text = $request->button_text ?? 'Play Now';
            $game->instructions = $request->instructions;
            
            if ($request->hasFile('background_image')) {
                $game->background_image = Helpers::upload('gamification/games/', 'png', $request->file('background_image'));
            }

            $game->display_settings = [
                'primary_color' => $request->primary_color ?? '#8DC63F',
                'secondary_color' => $request->secondary_color ?? '#F5D800',
                'text_color' => $request->text_color ?? '#1A1A1A',
            ];

            $game->save();

            DB::commit();
            
            Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'GamificationGame', data_id: $game->id, data_value: $game->name);
            Helpers::add_or_update_translations(request: $request, key_data: 'description', name_field: 'description', model_name: 'GamificationGame', data_id: $game->id, data_value: $game->description);

            return redirect()->route('admin.gamification.prizes.index', $game->id)
                ->with('success', 'Game created successfully! Now add prizes.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create game: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $game = GamificationGame::with(['prizes', 'eligibilityRules'])->findOrFail($id);
        $zones = Zone::active()->get();
        $restaurants = Restaurant::active()->get();
        
        return view('admin-views.gamification.edit', compact('game', 'zones', 'restaurants'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:spin_wheel,scratch_card,slot_machine,mystery_box,decision_roulette',
            'plays_per_day' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'background_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $game = GamificationGame::findOrFail($id);
            $game->name = $request->name;
            $game->slug = Str::slug($request->name);
            $game->type = $request->type;
            $game->description = $request->description;
            $game->status = $request->has('status') ? 1 : 0;
            $game->first_play_always_wins = $request->has('first_play_always_wins') ? 1 : 0;
            $game->plays_per_day = $request->plays_per_day;
            $game->plays_per_week = $request->plays_per_week;
            $game->cooldown_minutes = $request->cooldown_minutes ?? 0;
            $game->start_date = $request->start_date;
            $game->end_date = $request->end_date;
            $game->priority = $request->priority ?? 0;
            $game->button_text = $request->button_text ?? 'Play Now';
            $game->instructions = $request->instructions;
            
            if ($request->hasFile('background_image')) {
                $game->background_image = Helpers::update('gamification/games/', $game->background_image, 'png', $request->file('background_image'));
            }

            $game->display_settings = [
                'primary_color' => $request->primary_color ?? '#8DC63F',
                'secondary_color' => $request->secondary_color ?? '#F5D800',
                'text_color' => $request->text_color ?? '#1A1A1A',
            ];

            $game->save();

            DB::commit();
            
            Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'GamificationGame', data_id: $game->id, data_value: $game->name);
            Helpers::add_or_update_translations(request: $request, key_data: 'description', name_field: 'description', model_name: 'GamificationGame', data_id: $game->id, data_value: $game->description);

            return back()->with('success', 'Game updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update game: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $game = GamificationGame::findOrFail($id);
            
            if ($game->background_image) {
                $disk = Helpers::getDisk();
                if (Storage::disk($disk)->exists('gamification/games/' . $game->background_image)) {
                    Storage::disk($disk)->delete('gamification/games/' . $game->background_image);
                }
            }
            
            $game->delete();
            
            return response()->json(['success' => true, 'message' => 'Game deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete game: ' . $e->getMessage()]);
        }
    }

    public function status(Request $request)
    {
        $game = GamificationGame::findOrFail($request->id);
        $game->status = $request->status;
        $game->save();
        
        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

    public function preview($id)
    {
        $game = GamificationGame::with('prizes')->findOrFail($id);
        
        return view('admin-views.gamification.preview', compact('game'));
    }

    public function analytics($id)
    {
        $game = GamificationGame::with(['prizes', 'analytics' => function ($query) {
            $query->orderBy('date', 'desc')->limit(30);
        }])->findOrFail($id);

        $totalPlays = $game->gamePlays()->count();
        $totalWinners = $game->gamePlays()->where('is_winner', 1)->count();
        $totalClaimed = $game->gamePlays()->where('is_claimed', 1)->count();
        $uniquePlayers = $game->gamePlays()->distinct('user_id')->count('user_id');
        
        $prizeDistribution = GamificationGamePlay::where('game_id', $id)
            ->where('is_winner', 1)
            ->with('prize')
            ->select('prize_id', DB::raw('count(*) as count'))
            ->groupBy('prize_id')
            ->get();

        $recentPlays = GamificationGamePlay::where('game_id', $id)
            ->with(['user', 'prize'])
            ->latest()
            ->limit(50)
            ->get();

        return view('admin-views.gamification.analytics', compact(
            'game',
            'totalPlays',
            'totalWinners',
            'totalClaimed',
            'uniquePlayers',
            'prizeDistribution',
            'recentPlays'
        ));
    }
}
