<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class GamificationGame extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'config',
        'status',
        'first_play_always_wins',
        'plays_per_day',
        'plays_per_week',
        'cooldown_minutes',
        'start_date',
        'end_date',
        'priority',
        'background_image',
        'button_text',
        'instructions',
        'display_settings',
    ];

    protected $casts = [
        'config' => 'array',
        'display_settings' => 'array',
        'status' => 'boolean',
        'first_play_always_wins' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $appends = ['background_image_full_url'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function prizes()
    {
        return $this->hasMany(GamificationPrize::class, 'game_id')->orderBy('position');
    }

    public function eligibilityRules()
    {
        return $this->hasMany(GamificationEligibilityRule::class, 'game_id')->orderBy('priority');
    }

    public function gamePlays()
    {
        return $this->hasMany(GamificationGamePlay::class, 'game_id');
    }

    public function analytics()
    {
        return $this->hasMany(GamificationAnalytics::class, 'game_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function isActive(): bool
    {
        if (!$this->status) {
            return false;
        }

        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }

    public function canUserPlay($userId): array
    {
        if (!$this->isActive()) {
            return ['can_play' => false, 'reason' => 'Game is not active'];
        }

        $today = now()->startOfDay();
        $playsToday = $this->gamePlays()
            ->where('user_id', $userId)
            ->where('created_at', '>=', $today)
            ->count();

        if ($playsToday >= $this->plays_per_day) {
            return ['can_play' => false, 'reason' => 'Daily play limit reached'];
        }

        if ($this->plays_per_week) {
            $weekStart = now()->startOfWeek();
            $playsThisWeek = $this->gamePlays()
                ->where('user_id', $userId)
                ->where('created_at', '>=', $weekStart)
                ->count();

            if ($playsThisWeek >= $this->plays_per_week) {
                return ['can_play' => false, 'reason' => 'Weekly play limit reached'];
            }
        }

        if ($this->cooldown_minutes > 0) {
            $lastPlay = $this->gamePlays()
                ->where('user_id', $userId)
                ->latest()
                ->first();

            if ($lastPlay && $lastPlay->created_at->addMinutes($this->cooldown_minutes)->isFuture()) {
                $minutesLeft = now()->diffInMinutes($lastPlay->created_at->addMinutes($this->cooldown_minutes));
                return ['can_play' => false, 'reason' => "Please wait {$minutesLeft} minutes"];
            }
        }

        return ['can_play' => true];
    }

    public function getBackgroundImageFullUrlAttribute()
    {
        if (!$this->background_image) {
            return null;
        }
        return \App\CentralLogics\Helpers::get_full_url('gamification/games', $this->background_image, 'public');
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'spin_wheel' => 'Spin the Wheel',
            'scratch_card' => 'Scratch Card',
            'slot_machine' => 'Slot Machine',
            'mystery_box' => 'Mystery Box',
            'decision_roulette' => 'Decision Roulette',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
