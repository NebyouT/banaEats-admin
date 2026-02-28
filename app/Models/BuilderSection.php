<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuilderSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id', 'section_type', 'name', 'order',
        'settings', 'style', 'is_visible'
    ];

    protected $casts = [
        'settings' => 'array',
        'style' => 'array',
        'is_visible' => 'boolean',
        'order' => 'integer',
    ];

    // Available section types
    const TYPES = [
        'hero' => ['name' => 'Hero Banner', 'icon' => 'tio-image', 'description' => 'Large banner with text overlay'],
        'products_grid' => ['name' => 'Products Grid', 'icon' => 'tio-restaurant', 'description' => 'Grid of product cards'],
        'products_carousel' => ['name' => 'Products Carousel', 'icon' => 'tio-carousel', 'description' => 'Horizontal scrolling products'],
        'restaurants_grid' => ['name' => 'Restaurants Grid', 'icon' => 'tio-shop', 'description' => 'Grid of restaurant cards'],
        'restaurants_carousel' => ['name' => 'Restaurants Carousel', 'icon' => 'tio-carousel', 'description' => 'Horizontal scrolling restaurants'],
        'text_block' => ['name' => 'Text Block', 'icon' => 'tio-text', 'description' => 'Rich text content'],
        'image_banner' => ['name' => 'Image Banner', 'icon' => 'tio-photo-landscape', 'description' => 'Full-width image'],
        'button_group' => ['name' => 'Button Group', 'icon' => 'tio-cursor', 'description' => 'Action buttons'],
        'spacer' => ['name' => 'Spacer', 'icon' => 'tio-height', 'description' => 'Empty space'],
        'divider' => ['name' => 'Divider', 'icon' => 'tio-minus', 'description' => 'Horizontal line'],
        'categories' => ['name' => 'Categories', 'icon' => 'tio-category', 'description' => 'Category list or grid'],
        'countdown' => ['name' => 'Countdown Timer', 'icon' => 'tio-time', 'description' => 'Countdown to date'],
        'video' => ['name' => 'Video', 'icon' => 'tio-video', 'description' => 'Embedded video'],
        'custom_html' => ['name' => 'Custom HTML', 'icon' => 'tio-code', 'description' => 'Raw HTML content'],
        'columns' => ['name' => 'Columns', 'icon' => 'tio-column', 'description' => 'Multi-column layout'],
        'tabs' => ['name' => 'Tabs', 'icon' => 'tio-tab', 'description' => 'Tabbed container for organizing content'],
        'restaurant_foods' => ['name' => 'Restaurant + Foods', 'icon' => 'tio-store', 'description' => 'Restaurant card with scrollable food list'],
    ];

    // Default settings per section type
    public static function defaultSettings(string $type): array
    {
        $base = [
            'padding_top' => 16,
            'padding_bottom' => 16,
            'padding_left' => 16,
            'padding_right' => 16,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'background_color' => 'transparent',
            'background_image' => null,
            'border_radius' => 0,
        ];

        $typeSettings = [
            'hero' => [
                'height' => 300,
                'text_align' => 'center',
                'overlay_color' => 'rgba(0,0,0,0.3)',
                'title_size' => 28,
                'subtitle_size' => 16,
            ],
            'products_grid' => [
                'columns' => 2,
                'gap' => 12,
                'card_style' => 'default', // default, compact, detailed
                'show_price' => true,
                'show_rating' => true,
                'show_restaurant' => true,
                'max_items' => 6,
            ],
            'products_carousel' => [
                'card_width' => 160,
                'gap' => 12,
                'show_price' => true,
                'show_rating' => true,
                'auto_scroll' => false,
                'max_items' => 10,
            ],
            'restaurants_grid' => [
                'columns' => 2,
                'gap' => 12,
                'card_style' => 'default',
                'show_rating' => true,
                'show_delivery_time' => true,
                'max_items' => 6,
            ],
            'restaurants_carousel' => [
                'card_width' => 200,
                'gap' => 12,
                'show_rating' => true,
                'auto_scroll' => false,
                'max_items' => 10,
            ],
            'text_block' => [
                'text_align' => 'left',
                'font_size' => 14,
                'line_height' => 1.6,
            ],
            'image_banner' => [
                'height' => 200,
                'object_fit' => 'cover',
                'border_radius' => 12,
            ],
            'button_group' => [
                'alignment' => 'center',
                'gap' => 12,
                'direction' => 'row', // row, column
            ],
            'spacer' => [
                'height' => 24,
            ],
            'divider' => [
                'color' => '#e0e0e0',
                'thickness' => 1,
                'style' => 'solid', // solid, dashed, dotted
                'width' => '100%',
            ],
            'categories' => [
                'layout' => 'scroll', // scroll, grid
                'columns' => 4,
                'show_name' => true,
                'icon_size' => 48,
            ],
            'countdown' => [
                'target_date' => null,
                'show_days' => true,
                'show_hours' => true,
                'show_minutes' => true,
                'show_seconds' => true,
                'expired_text' => 'Offer Expired',
            ],
            'video' => [
                'video_url' => null,
                'autoplay' => false,
                'muted' => true,
                'controls' => true,
                'aspect_ratio' => '16:9',
            ],
            'columns' => [
                'columns' => 2,
                'gap' => 16,
                'column_widths' => [50, 50], // percentages
            ],
            'tabs' => [
                'tab_labels' => ['Tab 1', 'Tab 2'],
                'active_tab' => 0,
                'tab_style' => 'default',
                'tab_bg_color' => '#ffffff',
                'tab_active_color' => '#FC6A57',
                'tab_text_color' => '#333333',
                'tab_active_text_color' => '#ffffff',
                'tab_border_radius' => 8,
                'content_padding' => 16,
            ],
            'restaurant_foods' => [
                'restaurant_id' => null,
                'show_restaurant_logo' => true,
                'show_restaurant_name' => true,
                'show_restaurant_rating' => true,
                'food_display_mode' => 'scroll',
                'food_count' => 10,
                'food_selection' => 'auto',
                'selected_food_ids' => [],
                'card_aspect_ratio' => '3:1',
                'show_food_price' => true,
                'show_food_name' => true,
                'show_food_image' => true,
                'card_bg_color' => '#ffffff',
                'food_card_bg_color' => '#f8f9fa',
            ],
        ];

        return array_merge($base, $typeSettings[$type] ?? []);
    }

    public function page()
    {
        return $this->belongsTo(BuilderPage::class, 'page_id');
    }

    public function components()
    {
        return $this->hasMany(BuilderComponent::class, 'section_id')->orderBy('order');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    public function toBuilderJson(): array
    {
        return [
            'id' => $this->id,
            'section_type' => $this->section_type,
            'name' => $this->name,
            'order' => $this->order,
            'settings' => $this->settings,
            'style' => $this->style,
            'is_visible' => $this->is_visible,
            'components' => $this->components->map(function ($component) {
                return $component->toBuilderJson();
            })->toArray(),
        ];
    }

    public static function importFromJson(int $pageId, array $data, int $order): self
    {
        $section = self::create([
            'page_id' => $pageId,
            'section_type' => $data['section_type'] ?? 'text_block',
            'name' => $data['name'] ?? null,
            'order' => $order,
            'settings' => $data['settings'] ?? self::defaultSettings($data['section_type'] ?? 'text_block'),
            'style' => $data['style'] ?? null,
            'is_visible' => $data['is_visible'] ?? true,
        ]);

        if (!empty($data['components'])) {
            foreach ($data['components'] as $compOrder => $compData) {
                BuilderComponent::importFromJson($section->id, $compData, $compOrder);
            }
        }

        return $section;
    }
}
