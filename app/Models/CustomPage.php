<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'subtitle', 'promotional_text',
        'background_image', 'background_media_type', 'background_color',
        'product_ids', 'restaurant_ids', 'status',
    ];

    protected $casts = [
        'product_ids'    => 'array',
        'restaurant_ids' => 'array',
        'status'         => 'integer',
        'background_media_type' => 'string',
    ];

    protected $appends = ['background_image_full_url', 'background_media_full_url'];

    public function getBackgroundImageFullUrlAttribute()
    {
        $value = $this->background_image;
        if (!$value) return null;

        $storage = DB::table('storages')
            ->where('data_type', self::class)
            ->where('data_id', $this->id)
            ->where('key', 'background_image')
            ->first();

        return Helpers::get_full_url('custom-page', $value, $storage->value ?? 'public');
    }

    /**
     * Alias that always returns the background media URL regardless of type.
     * Flutter should use background_media_type to decide how to render it.
     */
    public function getBackgroundMediaFullUrlAttribute()
    {
        return $this->background_image_full_url;
    }

    public function isBackgroundVideo(): bool
    {
        return in_array($this->background_media_type, ['video', 'gif']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function products()
    {
        $ids = $this->product_ids ?? [];
        if (empty($ids)) return collect();
        return Food::whereIn('id', $ids)->get();
    }

    public function restaurants()
    {
        $ids = $this->restaurant_ids ?? [];
        if (empty($ids)) return collect();
        return Restaurant::whereIn('id', $ids)->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . Str::random(6);
            }
        });

        static::saved(function ($model) {
            if ($model->isDirty('background_image') && $model->background_image) {
                $disk = Helpers::getDisk();
                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id'   => $model->id,
                    'key'       => 'background_image',
                ], [
                    'value'      => $disk,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
