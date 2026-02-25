<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationPrize extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 'name', 'type', 'value', 'description', 'probability',
        'total_quantity', 'remaining_quantity', 'allow_multiple_wins', 'expiry_days',
        'min_order_amount', 'max_delivery_distance_km',
        'min_delivery_time_gap_minutes', 'max_delivery_time_gap_minutes',
        'valid_order_types', 'valid_payment_methods', 'max_discount_amount',
        'valid_food_ids', 'valid_category_ids', 'valid_cuisine_ids',
        'min_cart_items', 'schedule_type', 'valid_from_time', 'valid_until_time',
        'valid_days', 'new_customer_only', 'min_order_count',
        'restaurant_ids', 'zone_ids', 'image', 'color', 'status', 'position',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'probability' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'restaurant_ids' => 'array',
        'zone_ids' => 'array',
        'valid_order_types' => 'array',
        'valid_payment_methods' => 'array',
        'valid_food_ids' => 'array',
        'valid_category_ids' => 'array',
        'valid_cuisine_ids' => 'array',
        'valid_days' => 'array',
        'allow_multiple_wins' => 'boolean',
        'new_customer_only' => 'boolean',
        'status' => 'boolean',
    ];

    protected $appends = ['image_full_url', 'type_name'];

    public function game()
    {
        return $this->belongsTo(GamificationGame::class, 'game_id');
    }

    public function gamePlays()
    {
        return $this->hasMany(GamificationGamePlay::class, 'prize_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function isAvailable(): bool
    {
        if (!$this->status) {
            return false;
        }

        if ($this->total_quantity !== null && $this->remaining_quantity <= 0) {
            return false;
        }

        return true;
    }

    public function decrementQuantity(): void
    {
        if ($this->total_quantity !== null && $this->remaining_quantity > 0) {
            $this->decrement('remaining_quantity');
        }
    }

    public function getImageFullUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        return \App\CentralLogics\Helpers::get_full_url('gamification/prizes', $this->image, 'public');
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'discount_percentage' => 'Discount (%)',
            'discount_fixed' => 'Discount (Fixed)',
            'free_delivery' => 'Free Delivery',
            'loyalty_points' => 'Loyalty Points',
            'wallet_credit' => 'Wallet Credit',
            'free_item' => 'Free Item',
            'mystery' => 'Mystery Prize',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function getDisplayValueAttribute(): string
    {
        return match($this->type) {
            'discount_percentage' => $this->value . '% OFF',
            'discount_fixed' => '$' . number_format($this->value, 2) . ' OFF',
            'free_delivery' => 'FREE DELIVERY',
            'loyalty_points' => $this->value . ' Points',
            'wallet_credit' => '$' . number_format($this->value, 2),
            'free_item' => $this->name,
            'mystery' => '???',
            default => $this->value,
        };
    }
}
