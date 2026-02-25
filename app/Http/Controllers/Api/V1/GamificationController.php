<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\GamificationGame;
use App\Models\GamificationPrize;
use App\Models\GamificationGamePlay;
use App\Models\GamificationAnalytics;
use App\Models\GamificationBanner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GamificationController extends Controller
{
    // ────────────────────────────────────────────────
    //  GAMES
    // ────────────────────────────────────────────────

    public function available_games(Request $request)
    {
        $user = $request->user();

        $games = GamificationGame::with(['prizes' => function ($q) {
                $q->where('status', 1)->orderBy('position');
            }])
            ->where('status', 1)
            ->where(function ($q) { $q->whereNull('start_date')->orWhere('start_date', '<=', now()); })
            ->where(function ($q) { $q->whereNull('end_date')->orWhere('end_date', '>=', now()); })
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(fn ($game) => $game->canUserPlay($user))
            ->map(function ($game) use ($user) {
                $playsToday = GamificationGamePlay::where('game_id', $game->id)
                    ->where('user_id', $user->id)->whereDate('created_at', today())->count();
                $remaining = max(0, $game->plays_per_day - $playsToday);

                return [
                    'id' => $game->id,
                    'name' => $game->name,
                    'slug' => $game->slug,
                    'type' => $game->type,
                    'description' => $game->description,
                    'instructions' => $game->instructions,
                    'background_image' => $game->background_image_full_url,
                    'button_text' => $game->button_text,
                    'display_settings' => $game->display_settings,
                    'plays_remaining_today' => $remaining,
                    'can_play_now' => $remaining > 0,
                    'prizes' => $game->prizes->map(fn ($p) => [
                        'id' => $p->id, 'name' => $p->name, 'description' => $p->description,
                        'type' => $p->type, 'display_value' => $p->display_value,
                        'color' => $p->color, 'image' => $p->image_full_url, 'position' => $p->position,
                    ]),
                ];
            })->values();

        return response()->json(['success' => true, 'data' => $games]);
    }

    public function play_game(Request $request, $gameId)
    {
        $user = $request->user();
        $game = GamificationGame::with('prizes')->findOrFail($gameId);

        if (!$game->status) {
            return response()->json(['success' => false, 'message' => 'Game is not active.'], 403);
        }
        if (!$game->canUserPlay($user)) {
            return response()->json(['success' => false, 'message' => 'You are not eligible.'], 403);
        }

        $playsToday = GamificationGamePlay::where('game_id', $game->id)
            ->where('user_id', $user->id)->whereDate('created_at', today())->count();
        if ($playsToday >= $game->plays_per_day) {
            return response()->json(['success' => false, 'message' => 'Daily play limit reached.'], 403);
        }

        if ($game->cooldown_minutes > 0) {
            $last = GamificationGamePlay::where('game_id', $game->id)
                ->where('user_id', $user->id)->latest()->first();
            if ($last && $last->created_at->addMinutes($game->cooldown_minutes) > now()) {
                $wait = $last->created_at->addMinutes($game->cooldown_minutes)->diffInMinutes(now());
                return response()->json(['success' => false, 'message' => "Wait {$wait} minutes."], 403);
            }
        }

        $prize = $this->selectPrize($game, $user);

        $gp = new GamificationGamePlay();
        $gp->game_id = $game->id;
        $gp->user_id = $user->id;
        $gp->prize_id = $prize?->id;
        $gp->is_winner = (bool) $prize;
        $gp->prize_status = $prize ? 'locked' : 'locked';
        $gp->ip_address = $request->ip();
        $gp->user_agent = $request->userAgent();

        if ($prize) {
            $gp->prize_code = strtoupper(Str::random(8));
            $gp->expires_at = now()->addDays($prize->expiry_days);
            if ($prize->total_quantity) $prize->decrement('remaining_quantity');
        }
        $gp->save();

        GamificationAnalytics::recordPlay($game->id, (bool) $prize, $prize?->id);

        return response()->json([
            'success' => true,
            'message' => $prize ? 'Congratulations! You won a prize!' : 'Better luck next time!',
            'data' => [
                'is_winner' => $gp->is_winner,
                'prize' => $prize ? [
                    'id' => $prize->id, 'name' => $prize->name, 'description' => $prize->description,
                    'type' => $prize->type, 'display_value' => $prize->display_value,
                    'color' => $prize->color, 'image' => $prize->image_full_url,
                    'prize_code' => $gp->prize_code, 'prize_status' => 'locked',
                    'expires_at' => $gp->expires_at->format('Y-m-d H:i:s'),
                ] : null,
            ],
        ]);
    }

    // ────────────────────────────────────────────────
    //  PRIZE WALLET (locked → unlocked → applied)
    // ────────────────────────────────────────────────

    public function my_prizes(Request $request)
    {
        $user = $request->user();
        $filter = $request->query('status'); // locked, unlocked, applied, expired, all

        $query = GamificationGamePlay::with(['game', 'prize'])
            ->where('user_id', $user->id)
            ->where('is_winner', true)
            ->whereNotNull('prize_id')
            ->latest();

        if ($filter && $filter !== 'all') {
            if ($filter === 'expired') {
                $query->where('expires_at', '<', now())->where('prize_status', '!=', 'applied');
            } else {
                $query->where('prize_status', $filter);
            }
        }

        $plays = $query->paginate(20);

        $data = $plays->getCollection()->map(function ($gp) {
            $isExpired = $gp->expires_at && $gp->expires_at < now() && $gp->prize_status !== 'applied';
            $effectiveStatus = $isExpired ? 'expired' : $gp->prize_status;

            return [
                'id' => $gp->id,
                'game_name' => $gp->game->name ?? '',
                'game_type' => $gp->game->type ?? '',
                'prize_name' => $gp->prize->name ?? '',
                'prize_description' => $gp->prize->description ?? '',
                'prize_type' => $gp->prize->type ?? '',
                'prize_value' => $gp->prize->value ?? 0,
                'display_value' => $gp->prize->display_value ?? '',
                'prize_color' => $gp->prize->color ?? '#8DC63F',
                'prize_image' => $gp->prize->image_full_url ?? null,
                'prize_code' => $gp->prize_code,
                'prize_status' => $effectiveStatus,
                'is_locked' => $effectiveStatus === 'locked',
                'is_unlocked' => $effectiveStatus === 'unlocked',
                'is_applied' => $effectiveStatus === 'applied',
                'is_expired' => $isExpired,
                'can_apply' => $effectiveStatus === 'unlocked',
                'can_unlock' => $effectiveStatus === 'locked' && !$isExpired,
                'expires_at' => $gp->expires_at?->format('Y-m-d H:i:s'),
                'unlocked_at' => $gp->unlocked_at?->format('Y-m-d H:i:s'),
                'applied_at' => $gp->applied_at?->format('Y-m-d H:i:s'),
                'applied_to_order_id' => $gp->applied_to_order_id,
                'won_at' => $gp->created_at->format('Y-m-d H:i:s'),
                // Conditions for applying
                'conditions' => $this->getPrizeConditions($gp->prize),
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $plays->total(),
            'data' => $data,
        ]);
    }

    public function unlock_prize(Request $request, $playId)
    {
        $user = $request->user();
        $gp = GamificationGamePlay::where('id', $playId)
            ->where('user_id', $user->id)->where('is_winner', true)->firstOrFail();

        if ($gp->prize_status !== 'locked') {
            return response()->json(['success' => false, 'message' => 'Prize is already unlocked or applied.'], 400);
        }
        if ($gp->expires_at && $gp->expires_at < now()) {
            $gp->update(['prize_status' => 'expired']);
            return response()->json(['success' => false, 'message' => 'Prize has expired.'], 400);
        }

        $gp->update(['prize_status' => 'unlocked', 'unlocked_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Prize unlocked! You can now apply it.']);
    }

    public function apply_prize(Request $request, $playId)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'order_id' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => Helpers::error_processor($validator)], 422);
        }

        $gp = GamificationGamePlay::with('prize')
            ->where('id', $playId)->where('user_id', $user->id)
            ->where('is_winner', true)->firstOrFail();

        if ($gp->prize_status !== 'unlocked') {
            return response()->json(['success' => false, 'message' => 'Prize must be unlocked first.'], 400);
        }
        if ($gp->expires_at && $gp->expires_at < now()) {
            $gp->update(['prize_status' => 'expired']);
            return response()->json(['success' => false, 'message' => 'Prize has expired.'], 400);
        }

        $prize = $gp->prize;
        $result = $this->applyPrizeByType($prize, $user, $request->order_id);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        $gp->update([
            'prize_status' => 'applied',
            'is_claimed' => true,
            'claimed_at' => now(),
            'applied_at' => now(),
            'applied_to_order_id' => $request->order_id,
            'applied_details' => $result['details'],
        ]);

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'applied_type' => $prize->type,
                'applied_value' => $prize->value,
                'details' => $result['details'],
            ],
        ]);
    }

    public function validate_prize_for_order(Request $request, $playId)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'order_amount' => 'required|numeric|min:0',
            'restaurant_id' => 'nullable|integer',
            'zone_id' => 'nullable|integer',
            'order_type' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'delivery_distance_km' => 'nullable|numeric',
            'cart_items_count' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => Helpers::error_processor($validator)], 422);
        }

        $gp = GamificationGamePlay::with('prize')
            ->where('id', $playId)->where('user_id', $user->id)
            ->where('is_winner', true)->firstOrFail();

        $prize = $gp->prize;
        $issues = [];

        if ($gp->prize_status === 'applied') $issues[] = 'Prize already applied.';
        if ($gp->prize_status === 'locked') $issues[] = 'Prize must be unlocked first.';
        if ($gp->expires_at && $gp->expires_at < now()) $issues[] = 'Prize has expired.';

        if ($prize->min_order_amount && $request->order_amount < $prize->min_order_amount) {
            $issues[] = "Minimum order amount is {$prize->min_order_amount}.";
        }
        if ($prize->restaurant_ids && $request->restaurant_id && !in_array($request->restaurant_id, $prize->restaurant_ids)) {
            $issues[] = 'Prize not valid for this restaurant.';
        }
        if ($prize->zone_ids && $request->zone_id && !in_array($request->zone_id, $prize->zone_ids)) {
            $issues[] = 'Prize not valid in your zone.';
        }
        if ($prize->valid_order_types && $request->order_type && !in_array($request->order_type, $prize->valid_order_types)) {
            $issues[] = 'Prize not valid for this order type.';
        }
        if ($prize->valid_payment_methods && $request->payment_method && !in_array($request->payment_method, $prize->valid_payment_methods)) {
            $issues[] = 'Prize not valid for this payment method.';
        }
        if ($prize->max_delivery_distance_km && $request->delivery_distance_km > $prize->max_delivery_distance_km) {
            $issues[] = "Max delivery distance is {$prize->max_delivery_distance_km}km.";
        }
        if ($prize->min_cart_items && $request->cart_items_count < $prize->min_cart_items) {
            $issues[] = "Minimum {$prize->min_cart_items} items required.";
        }
        if ($prize->new_customer_only) {
            $orderCount = DB::table('orders')->where('user_id', $user->id)->where('order_status', 'delivered')->count();
            if ($orderCount > 0) $issues[] = 'Prize is for new customers only.';
        }
        if ($prize->schedule_type === 'specific_time') {
            $now = now();
            if ($prize->valid_from_time && $now->format('H:i:s') < $prize->valid_from_time) {
                $issues[] = "Valid from {$prize->valid_from_time}.";
            }
            if ($prize->valid_until_time && $now->format('H:i:s') > $prize->valid_until_time) {
                $issues[] = "Valid until {$prize->valid_until_time}.";
            }
        }
        if ($prize->valid_days && !in_array(now()->dayOfWeek, $prize->valid_days)) {
            $issues[] = 'Prize not valid today.';
        }

        $discount = 0;
        if (empty($issues)) {
            if ($prize->type === 'discount_percentage') {
                $discount = $request->order_amount * ($prize->value / 100);
                if ($prize->max_discount_amount) $discount = min($discount, $prize->max_discount_amount);
            } elseif ($prize->type === 'discount_fixed') {
                $discount = min($prize->value, $request->order_amount);
            }
        }

        return response()->json([
            'success' => empty($issues),
            'is_valid' => empty($issues),
            'issues' => $issues,
            'discount_amount' => round($discount, 2),
            'prize_type' => $prize->type,
            'prize_value' => $prize->value,
        ]);
    }

    // ────────────────────────────────────────────────
    //  BANNERS
    // ────────────────────────────────────────────────

    public function banners(Request $request)
    {
        $placement = $request->query('placement', 'home');
        $zoneId = $request->query('zone_id');

        $banners = GamificationBanner::with('game')
            ->active()
            ->byPlacement($placement)
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(function ($banner) use ($zoneId) {
                if ($zoneId && $banner->zone_ids && !in_array($zoneId, $banner->zone_ids)) return false;
                return $banner->game && $banner->game->status;
            })
            ->map(fn ($b) => [
                'id' => $b->id,
                'game_id' => $b->game_id,
                'game_type' => $b->game->type,
                'title' => $b->title,
                'subtitle' => $b->subtitle,
                'image' => $b->image_full_url,
                'background_color' => $b->background_color,
                'text_color' => $b->text_color,
                'button_text' => $b->button_text,
                'button_color' => $b->button_color,
                'placement' => $b->placement,
            ])->values();

        return response()->json(['success' => true, 'data' => $banners]);
    }

    public function prize_details(Request $request, $prizeCode)
    {
        $user = $request->user();
        $gp = GamificationGamePlay::with(['game', 'prize'])
            ->where('user_id', $user->id)
            ->where('prize_code', strtoupper($prizeCode))->first();

        if (!$gp) {
            return response()->json(['success' => false, 'message' => 'Prize not found.'], 404);
        }

        $isExpired = $gp->expires_at && $gp->expires_at < now() && $gp->prize_status !== 'applied';

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $gp->id,
                'game_name' => $gp->game->name,
                'prize_name' => $gp->prize->name,
                'prize_description' => $gp->prize->description,
                'prize_type' => $gp->prize->type,
                'prize_value' => $gp->prize->value,
                'display_value' => $gp->prize->display_value,
                'prize_code' => $gp->prize_code,
                'prize_status' => $isExpired ? 'expired' : $gp->prize_status,
                'is_expired' => $isExpired,
                'can_unlock' => $gp->prize_status === 'locked' && !$isExpired,
                'can_apply' => $gp->prize_status === 'unlocked' && !$isExpired,
                'expires_at' => $gp->expires_at?->format('Y-m-d H:i:s'),
                'won_at' => $gp->created_at->format('Y-m-d H:i:s'),
                'conditions' => $this->getPrizeConditions($gp->prize),
            ],
        ]);
    }

    // ────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ────────────────────────────────────────────────

    private function applyPrizeByType($prize, $user, $orderId = null)
    {
        switch ($prize->type) {
            case 'discount_percentage':
            case 'discount_fixed':
                return [
                    'success' => true,
                    'message' => "Discount applied! Use code {$prize->name} at checkout.",
                    'details' => [
                        'action' => 'discount',
                        'discount_type' => $prize->type === 'discount_percentage' ? 'percent' : 'amount',
                        'discount_value' => $prize->value,
                        'max_discount' => $prize->max_discount_amount,
                        'min_order' => $prize->min_order_amount,
                    ],
                ];

            case 'free_delivery':
                return [
                    'success' => true,
                    'message' => 'Free delivery applied to your next order!',
                    'details' => [
                        'action' => 'free_delivery',
                        'max_distance_km' => $prize->max_delivery_distance_km,
                    ],
                ];

            case 'loyalty_points':
                $user->increment('loyalty_point', (int) $prize->value);
                return [
                    'success' => true,
                    'message' => (int) $prize->value . ' loyalty points added to your account!',
                    'details' => [
                        'action' => 'loyalty_points_credit',
                        'points_added' => (int) $prize->value,
                        'new_balance' => $user->fresh()->loyalty_point,
                    ],
                ];

            case 'wallet_credit':
                DB::table('user_infos')->updateOrInsert(
                    ['user_id' => $user->id],
                    ['wallet_balance' => DB::raw("wallet_balance + {$prize->value}")]
                );
                return [
                    'success' => true,
                    'message' => Helpers::format_currency($prize->value) . ' added to your wallet!',
                    'details' => [
                        'action' => 'wallet_credit',
                        'amount_credited' => $prize->value,
                    ],
                ];

            case 'free_item':
                return [
                    'success' => true,
                    'message' => 'Free item prize applied! Add the item to your cart.',
                    'details' => [
                        'action' => 'free_item',
                        'food_ids' => $prize->valid_food_ids,
                        'description' => $prize->description,
                    ],
                ];

            case 'mystery':
                $types = ['discount_percentage', 'loyalty_points', 'wallet_credit', 'free_delivery'];
                $mysteryType = $types[array_rand($types)];
                return $this->applyMysteryPrize($mysteryType, $prize, $user);

            default:
                return ['success' => false, 'message' => 'Unknown prize type.', 'details' => []];
        }
    }

    private function applyMysteryPrize($type, $prize, $user)
    {
        switch ($type) {
            case 'loyalty_points':
                $points = rand(10, 100);
                $user->increment('loyalty_point', $points);
                return ['success' => true, 'message' => "Mystery reveal: {$points} loyalty points!", 'details' => ['action' => 'loyalty_points_credit', 'points_added' => $points]];
            case 'wallet_credit':
                $amount = rand(5, 50);
                DB::table('user_infos')->updateOrInsert(['user_id' => $user->id], ['wallet_balance' => DB::raw("wallet_balance + {$amount}")]);
                return ['success' => true, 'message' => "Mystery reveal: wallet credit!", 'details' => ['action' => 'wallet_credit', 'amount_credited' => $amount]];
            case 'free_delivery':
                return ['success' => true, 'message' => 'Mystery reveal: free delivery!', 'details' => ['action' => 'free_delivery']];
            default:
                $pct = rand(5, 25);
                return ['success' => true, 'message' => "Mystery reveal: {$pct}% off!", 'details' => ['action' => 'discount', 'discount_type' => 'percent', 'discount_value' => $pct]];
        }
    }

    private function getPrizeConditions($prize)
    {
        if (!$prize) return [];
        $c = [];
        if ($prize->min_order_amount) $c[] = ['type' => 'min_order', 'value' => $prize->min_order_amount, 'label' => "Min order: " . Helpers::format_currency($prize->min_order_amount)];
        if ($prize->max_delivery_distance_km) $c[] = ['type' => 'max_distance', 'value' => $prize->max_delivery_distance_km, 'label' => "Max distance: {$prize->max_delivery_distance_km}km"];
        if ($prize->min_delivery_time_gap_minutes) $c[] = ['type' => 'delivery_time', 'value' => $prize->min_delivery_time_gap_minutes, 'label' => "Delivery time: {$prize->min_delivery_time_gap_minutes}-{$prize->max_delivery_time_gap_minutes} min"];
        if ($prize->valid_order_types) $c[] = ['type' => 'order_types', 'value' => $prize->valid_order_types, 'label' => 'Order types: ' . implode(', ', $prize->valid_order_types)];
        if ($prize->valid_payment_methods) $c[] = ['type' => 'payment', 'value' => $prize->valid_payment_methods, 'label' => 'Payment: ' . implode(', ', $prize->valid_payment_methods)];
        if ($prize->max_discount_amount) $c[] = ['type' => 'max_discount', 'value' => $prize->max_discount_amount, 'label' => "Max discount: " . Helpers::format_currency($prize->max_discount_amount)];
        if ($prize->min_cart_items) $c[] = ['type' => 'min_items', 'value' => $prize->min_cart_items, 'label' => "Min cart items: {$prize->min_cart_items}"];
        if ($prize->new_customer_only) $c[] = ['type' => 'new_customer', 'value' => true, 'label' => 'New customers only'];
        if ($prize->schedule_type === 'specific_time') $c[] = ['type' => 'time_window', 'value' => "{$prize->valid_from_time}-{$prize->valid_until_time}", 'label' => "Valid {$prize->valid_from_time} - {$prize->valid_until_time}"];
        if ($prize->valid_days) $c[] = ['type' => 'valid_days', 'value' => $prize->valid_days, 'label' => 'Specific days only'];
        if ($prize->restaurant_ids) $c[] = ['type' => 'restaurants', 'value' => $prize->restaurant_ids, 'label' => 'Specific restaurants only'];
        if ($prize->zone_ids) $c[] = ['type' => 'zones', 'value' => $prize->zone_ids, 'label' => 'Specific zones only'];
        return $c;
    }

    private function selectPrize($game, $user)
    {
        $isFirstPlay = GamificationGamePlay::where('game_id', $game->id)
            ->where('user_id', $user->id)->count() == 0;

        $prizes = $game->prizes()->where('status', 1)
            ->where(function ($q) { $q->whereNull('total_quantity')->orWhere('remaining_quantity', '>', 0); })
            ->get();

        if ($prizes->isEmpty()) return null;

        if ($game->first_play_always_wins && $isFirstPlay) {
            return $prizes->sortByDesc('probability')->first();
        }

        $total = $prizes->sum('probability');
        $rand = mt_rand(1, (int)($total * 100)) / 100;
        $cum = 0;
        foreach ($prizes as $prize) {
            $cum += $prize->probability;
            if ($rand <= $cum) {
                if (!$prize->allow_multiple_wins) {
                    if (GamificationGamePlay::where('user_id', $user->id)->where('prize_id', $prize->id)->exists()) continue;
                }
                return $prize;
            }
        }
        return null;
    }
}
