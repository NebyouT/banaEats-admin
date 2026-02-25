<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\GamificationGame;
use App\Models\GamificationPrize;
use App\Models\Restaurant;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GamificationPrizeController extends Controller
{
    public function index($gameId)
    {
        $game = GamificationGame::with('prizes')->findOrFail($gameId);
        
        return view('admin-views.gamification.prizes.index', compact('game'));
    }

    public function create($gameId)
    {
        $game = GamificationGame::findOrFail($gameId);
        $restaurants = Restaurant::active()->get();
        $zones = Zone::active()->get();
        
        return view('admin-views.gamification.prizes.create', compact('game', 'restaurants', 'zones'));
    }

    public function store(Request $request, $gameId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:discount_percentage,discount_fixed,free_delivery,loyalty_points,wallet_credit,free_item,mystery',
            'value' => 'required|numeric|min:0',
            'probability' => 'required|numeric|min:0|max:100',
            'expiry_days' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $prize = new GamificationPrize();
            $prize->game_id = $gameId;
            $prize->name = $request->name;
            $prize->type = $request->type;
            $prize->value = $request->value;
            $prize->description = $request->description;
            $prize->probability = $request->probability;
            $prize->total_quantity = $request->total_quantity;
            $prize->remaining_quantity = $request->total_quantity;
            $prize->allow_multiple_wins = $request->has('allow_multiple_wins') ? 1 : 0;
            $prize->expiry_days = $request->expiry_days;
            $prize->min_order_amount = $request->min_order_amount;
            $prize->max_discount_amount = $request->max_discount_amount;
            $prize->max_delivery_distance_km = $request->max_delivery_distance_km;
            $prize->min_delivery_time_gap_minutes = $request->min_delivery_time_gap_minutes;
            $prize->max_delivery_time_gap_minutes = $request->max_delivery_time_gap_minutes;
            $prize->valid_order_types = $request->valid_order_types;
            $prize->valid_payment_methods = $request->valid_payment_methods;
            $prize->min_cart_items = $request->min_cart_items;
            $prize->min_order_count = $request->min_order_count;
            $prize->schedule_type = $request->schedule_type;
            $prize->valid_from_time = $request->valid_from_time;
            $prize->valid_until_time = $request->valid_until_time;
            $prize->valid_days = $request->valid_days;
            $prize->new_customer_only = $request->has('new_customer_only') ? 1 : 0;
            $prize->restaurant_ids = $request->restaurant_ids;
            $prize->zone_ids = $request->zone_ids;
            $prize->color = $request->color ?? '#8DC63F';
            $prize->status = $request->has('status') ? 1 : 0;
            $prize->position = GamificationPrize::where('game_id', $gameId)->max('position') + 1;
            
            if ($request->hasFile('image')) {
                $prize->image = Helpers::upload('gamification/prizes/', 'png', $request->file('image'));
            }

            $prize->save();

            DB::commit();
            
            Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'GamificationPrize', data_id: $prize->id, data_value: $prize->name);

            return redirect()->route('admin.gamification.prizes.index', $gameId)
                ->with('success', 'Prize created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create prize: ' . $e->getMessage());
        }
    }

    public function edit($gameId, $id)
    {
        $game = GamificationGame::findOrFail($gameId);
        $prize = GamificationPrize::where('game_id', $gameId)->findOrFail($id);
        $restaurants = Restaurant::active()->get();
        $zones = Zone::active()->get();
        
        return view('admin-views.gamification.prizes.edit', compact('game', 'prize', 'restaurants', 'zones'));
    }

    public function update(Request $request, $gameId, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:discount_percentage,discount_fixed,free_delivery,loyalty_points,wallet_credit,free_item,mystery',
            'value' => 'required|numeric|min:0',
            'probability' => 'required|numeric|min:0|max:100',
            'expiry_days' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $prize = GamificationPrize::where('game_id', $gameId)->findOrFail($id);
            $prize->name = $request->name;
            $prize->type = $request->type;
            $prize->value = $request->value;
            $prize->description = $request->description;
            $prize->probability = $request->probability;
            $prize->total_quantity = $request->total_quantity;
            
            if ($request->total_quantity !== null) {
                $used = $prize->total_quantity - $prize->remaining_quantity;
                $prize->remaining_quantity = max(0, $request->total_quantity - $used);
            }
            
            $prize->allow_multiple_wins = $request->has('allow_multiple_wins') ? 1 : 0;
            $prize->expiry_days = $request->expiry_days;
            $prize->min_order_amount = $request->min_order_amount;
            $prize->max_discount_amount = $request->max_discount_amount;
            $prize->max_delivery_distance_km = $request->max_delivery_distance_km;
            $prize->min_delivery_time_gap_minutes = $request->min_delivery_time_gap_minutes;
            $prize->max_delivery_time_gap_minutes = $request->max_delivery_time_gap_minutes;
            $prize->valid_order_types = $request->valid_order_types;
            $prize->valid_payment_methods = $request->valid_payment_methods;
            $prize->min_cart_items = $request->min_cart_items;
            $prize->min_order_count = $request->min_order_count;
            $prize->schedule_type = $request->schedule_type;
            $prize->valid_from_time = $request->valid_from_time;
            $prize->valid_until_time = $request->valid_until_time;
            $prize->valid_days = $request->valid_days;
            $prize->new_customer_only = $request->has('new_customer_only') ? 1 : 0;
            $prize->restaurant_ids = $request->restaurant_ids;
            $prize->zone_ids = $request->zone_ids;
            $prize->color = $request->color ?? '#8DC63F';
            $prize->status = $request->has('status') ? 1 : 0;
            
            if ($request->hasFile('image')) {
                $prize->image = Helpers::update('gamification/prizes/', $prize->image, 'png', $request->file('image'));
            }

            $prize->save();

            DB::commit();
            
            Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'GamificationPrize', data_id: $prize->id, data_value: $prize->name);

            return back()->with('success', 'Prize updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update prize: ' . $e->getMessage());
        }
    }

    public function destroy($gameId, $id)
    {
        try {
            $prize = GamificationPrize::where('game_id', $gameId)->findOrFail($id);
            
            if ($prize->image) {
                $disk = Helpers::getDisk();
                if (Storage::disk($disk)->exists('gamification/prizes/' . $prize->image)) {
                    Storage::disk($disk)->delete('gamification/prizes/' . $prize->image);
                }
            }
            
            $prize->delete();
            
            return response()->json(['success' => true, 'message' => 'Prize deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete prize: ' . $e->getMessage()]);
        }
    }

    public function status(Request $request)
    {
        $prize = GamificationPrize::findOrFail($request->id);
        $prize->status = $request->status;
        $prize->save();
        
        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

    public function updatePosition(Request $request, $gameId)
    {
        try {
            $positions = $request->positions;
            
            foreach ($positions as $id => $position) {
                GamificationPrize::where('id', $id)
                    ->where('game_id', $gameId)
                    ->update(['position' => $position]);
            }
            
            return response()->json(['success' => true, 'message' => 'Prize positions updated!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update positions: ' . $e->getMessage()]);
        }
    }
}
