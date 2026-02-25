<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\GamificationGame;
use App\Models\GamificationPrize;
use App\Models\GamificationGamePlay;
use App\Models\GamificationAnalytics;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GamificationController extends Controller
{
    /**
     * Get available games for the authenticated customer
     */
    public function available_games(Request $request)
    {
        $user = $request->user();
        
        $games = GamificationGame::with(['prizes' => function($query) {
                $query->where('status', 1)
                      ->orderBy('position');
            }])
            ->where('status', 1)
            ->where(function($query) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(function($game) use ($user) {
                return $game->canUserPlay($user);
            })
            ->map(function($game) use ($user) {
                $playsToday = GamificationGamePlay::where('game_id', $game->id)
                    ->where('user_id', $user->id)
                    ->whereDate('created_at', today())
                    ->count();
                
                $playsRemaining = max(0, $game->plays_per_day - $playsToday);
                
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
                    'plays_remaining_today' => $playsRemaining,
                    'can_play_now' => $playsRemaining > 0,
                    'prizes' => $game->prizes->map(function($prize) {
                        return [
                            'id' => $prize->id,
                            'name' => $prize->name,
                            'description' => $prize->description,
                            'type' => $prize->type,
                            'display_value' => $prize->display_value,
                            'color' => $prize->color,
                            'image' => $prize->image_full_url,
                            'position' => $prize->position,
                        ];
                    }),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $games,
        ]);
    }

    /**
     * Play a game and get a prize
     */
    public function play_game(Request $request, $gameId)
    {
        $validator = Validator::make($request->all(), [
            'game_id' => 'required|exists:gamification_games,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => Helpers::error_processor($validator),
            ], 403);
        }

        $user = $request->user();
        $game = GamificationGame::with('prizes')->findOrFail($gameId);

        // Check if game is active
        if (!$game->status) {
            return response()->json([
                'success' => false,
                'message' => 'This game is not currently active.',
            ], 403);
        }

        // Check if user can play
        if (!$game->canUserPlay($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not eligible to play this game.',
            ], 403);
        }

        // Check play limits
        $playsToday = GamificationGamePlay::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        if ($playsToday >= $game->plays_per_day) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached your daily play limit for this game.',
            ], 403);
        }

        // Check cooldown
        if ($game->cooldown_minutes > 0) {
            $lastPlay = GamificationGamePlay::where('game_id', $game->id)
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            if ($lastPlay && $lastPlay->created_at->addMinutes($game->cooldown_minutes) > now()) {
                $waitMinutes = $lastPlay->created_at->addMinutes($game->cooldown_minutes)->diffInMinutes(now());
                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$waitMinutes} minutes before playing again.",
                ], 403);
            }
        }

        // Select a prize
        $prize = $this->selectPrize($game, $user);

        // Create game play record
        $gamePlay = new GamificationGamePlay();
        $gamePlay->game_id = $game->id;
        $gamePlay->user_id = $user->id;
        $gamePlay->prize_id = $prize ? $prize->id : null;
        $gamePlay->is_winner = $prize ? true : false;
        $gamePlay->ip_address = $request->ip();
        $gamePlay->user_agent = $request->userAgent();

        if ($prize) {
            $gamePlay->prize_code = strtoupper(Str::random(8));
            $gamePlay->expires_at = now()->addDays($prize->expiry_days);
            
            // Decrement prize quantity
            if ($prize->total_quantity) {
                $prize->decrement('remaining_quantity');
            }
        }

        $gamePlay->save();

        // Record analytics
        GamificationAnalytics::recordPlay($game->id, $prize ? true : false, $prize ? $prize->id : null);

        return response()->json([
            'success' => true,
            'message' => $prize ? 'Congratulations! You won a prize!' : 'Better luck next time!',
            'data' => [
                'is_winner' => $gamePlay->is_winner,
                'prize' => $prize ? [
                    'id' => $prize->id,
                    'name' => $prize->name,
                    'description' => $prize->description,
                    'type' => $prize->type,
                    'display_value' => $prize->display_value,
                    'color' => $prize->color,
                    'image' => $prize->image_full_url,
                    'prize_code' => $gamePlay->prize_code,
                    'expires_at' => $gamePlay->expires_at->format('Y-m-d H:i:s'),
                ] : null,
            ],
        ]);
    }

    /**
     * Get user's won prizes
     */
    public function my_prizes(Request $request)
    {
        $user = $request->user();
        
        $prizes = GamificationGamePlay::with(['game', 'prize'])
            ->where('user_id', $user->id)
            ->where('is_winner', true)
            ->whereNotNull('prize_id')
            ->latest()
            ->paginate(20);

        $data = $prizes->map(function($gamePlay) {
            return [
                'id' => $gamePlay->id,
                'game_name' => $gamePlay->game->name,
                'prize_name' => $gamePlay->prize->name,
                'prize_description' => $gamePlay->prize->description,
                'prize_type' => $gamePlay->prize->type,
                'prize_value' => $gamePlay->prize->display_value,
                'prize_code' => $gamePlay->prize_code,
                'is_claimed' => $gamePlay->is_claimed,
                'claimed_at' => $gamePlay->claimed_at,
                'expires_at' => $gamePlay->expires_at,
                'is_expired' => $gamePlay->expires_at < now(),
                'won_at' => $gamePlay->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $prizes->total(),
            'data' => $data,
        ]);
    }

    /**
     * Get prize details by code
     */
    public function prize_details(Request $request, $prizeCode)
    {
        $user = $request->user();
        
        $gamePlay = GamificationGamePlay::with(['game', 'prize'])
            ->where('user_id', $user->id)
            ->where('prize_code', strtoupper($prizeCode))
            ->first();

        if (!$gamePlay) {
            return response()->json([
                'success' => false,
                'message' => 'Prize not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $gamePlay->id,
                'game_name' => $gamePlay->game->name,
                'prize_name' => $gamePlay->prize->name,
                'prize_description' => $gamePlay->prize->description,
                'prize_type' => $gamePlay->prize->type,
                'prize_value' => $gamePlay->prize->display_value,
                'prize_code' => $gamePlay->prize_code,
                'is_claimed' => $gamePlay->is_claimed,
                'claimed_at' => $gamePlay->claimed_at,
                'expires_at' => $gamePlay->expires_at,
                'is_expired' => $gamePlay->expires_at < now(),
                'won_at' => $gamePlay->created_at->format('Y-m-d H:i:s'),
                'min_order_amount' => $gamePlay->prize->min_order_amount,
            ],
        ]);
    }

    /**
     * Select a prize based on probability
     */
    private function selectPrize($game, $user)
    {
        // Check if first play always wins
        $isFirstPlay = GamificationGamePlay::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->count() == 0;

        $prizes = $game->prizes()
            ->where('status', 1)
            ->where(function($query) {
                $query->whereNull('total_quantity')
                      ->orWhere('remaining_quantity', '>', 0);
            })
            ->get();

        if ($prizes->isEmpty()) {
            return null;
        }

        // If first play always wins, select highest probability prize
        if ($game->first_play_always_wins && $isFirstPlay) {
            return $prizes->sortByDesc('probability')->first();
        }

        // Weighted random selection
        $totalProbability = $prizes->sum('probability');
        $random = mt_rand(1, $totalProbability * 100) / 100;
        
        $cumulativeProbability = 0;
        foreach ($prizes as $prize) {
            $cumulativeProbability += $prize->probability;
            if ($random <= $cumulativeProbability) {
                // Check if user can win this prize multiple times
                if (!$prize->allow_multiple_wins) {
                    $alreadyWon = GamificationGamePlay::where('user_id', $user->id)
                        ->where('prize_id', $prize->id)
                        ->exists();
                    
                    if ($alreadyWon) {
                        continue; // Skip this prize, try next
                    }
                }
                
                return $prize;
            }
        }

        return null; // No prize won
    }
}
