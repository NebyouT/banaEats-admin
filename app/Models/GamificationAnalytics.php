<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'date',
        'total_plays',
        'total_winners',
        'total_claimed',
        'unique_players',
        'conversion_rate',
        'prize_distribution',
    ];

    protected $casts = [
        'date' => 'date',
        'conversion_rate' => 'decimal:2',
        'prize_distribution' => 'array',
    ];

    public function game()
    {
        return $this->belongsTo(GamificationGame::class, 'game_id');
    }

    public static function recordPlay($gameId, $isWinner = false, $prizeId = null)
    {
        $today = now()->toDateString();
        
        $analytics = self::firstOrCreate(
            ['game_id' => $gameId, 'date' => $today],
            ['total_plays' => 0, 'total_winners' => 0, 'total_claimed' => 0, 'unique_players' => 0, 'prize_distribution' => []]
        );

        $analytics->increment('total_plays');
        
        if ($isWinner) {
            $analytics->increment('total_winners');
            
            if ($prizeId) {
                $distribution = $analytics->prize_distribution ?? [];
                $distribution[$prizeId] = ($distribution[$prizeId] ?? 0) + 1;
                $analytics->update(['prize_distribution' => $distribution]);
            }
        }

        $uniquePlayers = GamificationGamePlay::where('game_id', $gameId)
            ->whereDate('created_at', $today)
            ->distinct('user_id')
            ->count('user_id');
        
        $analytics->update(['unique_players' => $uniquePlayers]);
    }

    public static function recordClaim($gameId)
    {
        $today = now()->toDateString();
        
        $analytics = self::where('game_id', $gameId)
            ->where('date', $today)
            ->first();

        if ($analytics) {
            $analytics->increment('total_claimed');
            
            if ($analytics->total_winners > 0) {
                $conversionRate = ($analytics->total_claimed / $analytics->total_winners) * 100;
                $analytics->update(['conversion_rate' => $conversionRate]);
            }
        }
    }
}
