<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class BuilderPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'page_type',
        'settings', 'status', 'is_published', 'published_at'
    ];

    protected $casts = [
        'settings' => 'array',
        'status' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = ['preview_url', 'public_url'];

    // Default settings structure
    public static function defaultSettings(): array
    {
        return [
            'background_color' => '#ffffff',
            'background_image' => null,
            'background_type' => 'color', // color, image, gradient
            'gradient_start' => '#ffffff',
            'gradient_end' => '#f5f5f5',
            'gradient_direction' => 'to bottom',
            'font_family' => 'Inter, sans-serif',
            'primary_color' => '#FC6A57',
            'secondary_color' => '#8DC63F',
            'text_color' => '#1a1a1a',
            'padding_top' => 0,
            'padding_bottom' => 0,
            'max_width' => 'full', // full, 1200, 992, 768
        ];
    }

    public function getPreviewUrlAttribute(): string
    {
        return route('admin.page-builder.preview', $this->id);
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/page/' . $this->slug);
    }

    public function sections()
    {
        return $this->hasMany(BuilderSection::class, 'page_id')->orderBy('order');
    }

    public function views()
    {
        return $this->hasMany(BuilderPageView::class, 'page_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', 1)->where('status', 1);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . Str::random(6);
            }
            if (empty($model->settings)) {
                $model->settings = self::defaultSettings();
            }
        });
    }

    // Export full page structure as JSON
    public function toBuilderJson(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'page_type' => $this->page_type,
            'settings' => $this->settings,
            'sections' => $this->sections->map(function ($section) {
                return $section->toBuilderJson();
            })->toArray(),
        ];
    }

    // Import page structure from JSON
    public static function importFromJson(array $data): self
    {
        $page = self::create([
            'title' => $data['title'] ?? 'Imported Page',
            'description' => $data['description'] ?? null,
            'page_type' => $data['page_type'] ?? 'custom',
            'settings' => $data['settings'] ?? self::defaultSettings(),
        ]);

        if (!empty($data['sections'])) {
            foreach ($data['sections'] as $order => $sectionData) {
                BuilderSection::importFromJson($page->id, $sectionData, $order);
            }
        }

        return $page;
    }
}
