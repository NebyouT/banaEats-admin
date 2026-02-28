<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuilderTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'thumbnail', 'structure', 'is_system', 'status'
    ];

    protected $casts = [
        'structure' => 'array',
        'is_system' => 'boolean',
        'status' => 'boolean',
    ];

    const CATEGORIES = [
        'general' => 'General',
        'promotion' => 'Promotions',
        'food' => 'Food & Menu',
        'restaurant' => 'Restaurants',
        'event' => 'Events',
        'landing' => 'Landing Pages',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }
}
