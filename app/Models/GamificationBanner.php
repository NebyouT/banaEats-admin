<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Model;

class GamificationBanner extends Model
{
    protected $fillable = [
        'game_id', 'title', 'subtitle', 'image', 'image_storage',
        'background_color', 'text_color', 'button_text', 'button_color',
        'placement', 'priority', 'status', 'start_date', 'end_date', 'zone_ids',
    ];

    protected $casts = [
        'status' => 'boolean',
        'zone_ids' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $appends = ['image_full_url'];

    public function game()
    {
        return $this->belongsTo(GamificationGame::class, 'game_id');
    }

    public function getImageFullUrlAttribute()
    {
        if ($this->image) {
            return Helpers::get_full_url('gamification/banners', $this->image, $this->image_storage ?? 'public');
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }

    public function scopeByPlacement($query, $placement)
    {
        return $query->where('placement', $placement);
    }
}
