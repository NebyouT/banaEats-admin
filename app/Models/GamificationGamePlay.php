<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GamificationGamePlay extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'prize_id',
        'is_winner',
        'prize_code',
        'is_claimed',
        'claimed_at',
        'expires_at',
        'order_id',
        'game_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_winner' => 'boolean',
        'is_claimed' => 'boolean',
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'game_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->prize_code) && $model->is_winner) {
                $model->prize_code = strtoupper(Str::random(8));
            }
        });
    }

    public function game()
    {
        return $this->belongsTo(GamificationGame::class, 'game_id');
    }

    public function prize()
    {
        return $this->belongsTo(GamificationPrize::class, 'prize_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function scopeWinners($query)
    {
        return $query->where('is_winner', 1);
    }

    public function scopeUnclaimed($query)
    {
        return $query->where('is_winner', 1)
            ->where('is_claimed', 0)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('is_winner', 1)
            ->where('is_claimed', 0)
            ->where('expires_at', '<=', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canClaim(): bool
    {
        return $this->is_winner 
            && !$this->is_claimed 
            && !$this->isExpired();
    }

    public function claim($orderId = null): bool
    {
        if (!$this->canClaim()) {
            return false;
        }

        $this->update([
            'is_claimed' => true,
            'claimed_at' => now(),
            'order_id' => $orderId,
        ]);

        return true;
    }
}
