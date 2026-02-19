<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomPageBanner extends Model
{
    protected $fillable = [
        'title',
        'image',
        'media_type',
        'type',
        'page_id',
        'status',
    ];

    protected $casts = [
        'page_id' => 'integer',
        'status'  => 'integer',
    ];

    protected $appends = ['image_full_url'];

    public function getImageFullUrlAttribute()
    {
        $value = $this->image;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'image') {
                    return Helpers::get_full_url('custom-page-banner', $value, $storage['value']);
                }
            }
        }
        return Helpers::get_full_url('custom-page-banner', $value, 'public');
    }

    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }

    public function linkedPage()
    {
        return $this->belongsTo(CustomPage::class, 'page_id');
    }

    public function isVideo(): bool
    {
        return in_array($this->media_type, ['video', 'gif']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            if ($model->isDirty('image')) {
                $value = Helpers::getDisk();
                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id'   => $model->id,
                    'key'       => 'image',
                ], [
                    'value'      => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
