<?php

namespace App\Http\Controllers;

use App\Models\GamificationGame;
use Illuminate\Http\Request;

class GamificationPlayController extends Controller
{
    /**
     * Display the game play page for mobile WebView
     * 
     * @param int $gameId
     * @return \Illuminate\View\View
     */
    public function play($gameId)
    {
        $game = GamificationGame::with(['prizes' => function ($q) {
            $q->where('status', 1)->orderBy('position');
        }])->findOrFail($gameId);

        // Check if game is active
        if (!$game->status) {
            abort(404, 'Game not found or inactive');
        }

        // Check if game is within schedule
        if ($game->start_date && $game->start_date > now()) {
            abort(404, 'Game has not started yet');
        }

        if ($game->end_date && $game->end_date < now()) {
            abort(404, 'Game has ended');
        }

        // Prepare prizes data for JavaScript
        $prizesData = $game->prizes->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'color' => $p->color,
                'type' => $p->type,
                'value' => $p->value,
                'probability' => $p->probability
            ];
        });

        return view('gamification.play', compact('game', 'prizesData'));
    }
}
