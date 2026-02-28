<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuilderComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id', 'component_type', 'order', 'column_span',
        'content', 'settings', 'style', 'data_source', 'action', 'is_visible'
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'style' => 'array',
        'data_source' => 'array',
        'action' => 'array',
        'is_visible' => 'boolean',
        'order' => 'integer',
        'column_span' => 'integer',
    ];

    // Available component types
    const TYPES = [
        'text' => ['name' => 'Text', 'icon' => 'tio-text', 'description' => 'Text content'],
        'heading' => ['name' => 'Heading', 'icon' => 'tio-format-h1', 'description' => 'Heading text'],
        'image' => ['name' => 'Image', 'icon' => 'tio-image', 'description' => 'Single image'],
        'button' => ['name' => 'Button', 'icon' => 'tio-cursor', 'description' => 'Clickable button'],
        'product_card' => ['name' => 'Product Card', 'icon' => 'tio-restaurant', 'description' => 'Single product display'],
        'product_list' => ['name' => 'Product List', 'icon' => 'tio-format-list-bulleted', 'description' => 'List of products'],
        'restaurant_card' => ['name' => 'Restaurant Card', 'icon' => 'tio-shop', 'description' => 'Single restaurant display'],
        'restaurant_list' => ['name' => 'Restaurant List', 'icon' => 'tio-format-list-bulleted', 'description' => 'List of restaurants'],
        'icon' => ['name' => 'Icon', 'icon' => 'tio-star', 'description' => 'Icon element'],
        'badge' => ['name' => 'Badge', 'icon' => 'tio-label', 'description' => 'Label/badge'],
        'rating' => ['name' => 'Rating', 'icon' => 'tio-star-outlined', 'description' => 'Star rating display'],
        'price' => ['name' => 'Price', 'icon' => 'tio-dollar', 'description' => 'Price display'],
        'countdown' => ['name' => 'Countdown', 'icon' => 'tio-time', 'description' => 'Countdown timer'],
        'social_links' => ['name' => 'Social Links', 'icon' => 'tio-share', 'description' => 'Social media links'],
        'map' => ['name' => 'Map', 'icon' => 'tio-map', 'description' => 'Location map'],
        'video' => ['name' => 'Video', 'icon' => 'tio-video', 'description' => 'Video player'],
        'html' => ['name' => 'HTML', 'icon' => 'tio-code', 'description' => 'Custom HTML'],
    ];

    // Action types for click events
    const ACTION_TYPES = [
        'none' => 'No Action',
        'navigate_product' => 'Navigate to Product',
        'navigate_restaurant' => 'Navigate to Restaurant',
        'navigate_category' => 'Navigate to Category',
        'navigate_page' => 'Navigate to Page',
        'open_url' => 'Open External URL',
        'open_search' => 'Open Search',
        'call_phone' => 'Call Phone Number',
        'send_email' => 'Send Email',
    ];

    // Default content per component type
    public static function defaultContent(string $type): array
    {
        $defaults = [
            'text' => ['text' => 'Enter your text here...'],
            'heading' => ['text' => 'Heading', 'level' => 'h2'],
            'image' => ['url' => null, 'alt' => '', 'caption' => ''],
            'button' => ['text' => 'Click Me', 'icon' => null, 'icon_position' => 'left'],
            'product_card' => ['product_id' => null],
            'product_list' => ['product_ids' => [], 'title' => 'Featured Products'],
            'restaurant_card' => ['restaurant_id' => null],
            'restaurant_list' => ['restaurant_ids' => [], 'title' => 'Featured Restaurants'],
            'icon' => ['icon' => 'tio-star', 'size' => 24],
            'badge' => ['text' => 'NEW', 'variant' => 'primary'],
            'rating' => ['value' => 4.5, 'max' => 5, 'show_count' => true, 'count' => 0],
            'price' => ['amount' => 0, 'currency' => 'ETB', 'original_amount' => null],
            'countdown' => ['target_date' => null, 'title' => 'Offer ends in'],
            'social_links' => ['links' => []],
            'map' => ['latitude' => 9.0, 'longitude' => 38.75, 'zoom' => 14],
            'video' => ['url' => null, 'thumbnail' => null],
            'html' => ['code' => ''],
        ];

        return $defaults[$type] ?? [];
    }

    // Default settings per component type
    public static function defaultSettings(string $type): array
    {
        $base = [
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
            'padding' => 0,
        ];

        $typeSettings = [
            'text' => [
                'font_size' => 14,
                'font_weight' => 'normal',
                'color' => '#333333',
                'text_align' => 'left',
                'line_height' => 1.5,
            ],
            'heading' => [
                'font_size' => 24,
                'font_weight' => 'bold',
                'color' => '#1a1a1a',
                'text_align' => 'left',
            ],
            'image' => [
                'width' => '100%',
                'height' => 'auto',
                'object_fit' => 'cover',
                'border_radius' => 8,
            ],
            'button' => [
                'background_color' => '#FC6A57',
                'text_color' => '#ffffff',
                'border_radius' => 8,
                'padding_x' => 24,
                'padding_y' => 12,
                'font_size' => 14,
                'font_weight' => 'bold',
                'full_width' => false,
                'border_width' => 0,
                'border_color' => 'transparent',
            ],
            'product_card' => [
                'show_image' => true,
                'show_name' => true,
                'show_price' => true,
                'show_rating' => true,
                'show_restaurant' => true,
                'show_add_button' => true,
                'image_height' => 120,
                'border_radius' => 12,
            ],
            'product_list' => [
                'layout' => 'grid', // grid, list, carousel
                'columns' => 2,
                'gap' => 12,
                'show_title' => true,
                'show_view_all' => true,
                'card_settings' => [],
            ],
            'restaurant_card' => [
                'show_image' => true,
                'show_name' => true,
                'show_rating' => true,
                'show_delivery_time' => true,
                'show_delivery_fee' => true,
                'show_cuisine' => true,
                'image_height' => 140,
                'border_radius' => 12,
            ],
            'restaurant_list' => [
                'layout' => 'grid',
                'columns' => 2,
                'gap' => 12,
                'show_title' => true,
                'show_view_all' => true,
                'card_settings' => [],
            ],
            'badge' => [
                'background_color' => '#FC6A57',
                'text_color' => '#ffffff',
                'border_radius' => 4,
                'font_size' => 12,
            ],
            'countdown' => [
                'background_color' => '#1a1a1a',
                'text_color' => '#ffffff',
                'accent_color' => '#FC6A57',
            ],
        ];

        return array_merge($base, $typeSettings[$type] ?? []);
    }

    public function section()
    {
        return $this->belongsTo(BuilderSection::class, 'section_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    // Get actual product data if this is a product component
    public function getProductsAttribute()
    {
        if (!in_array($this->component_type, ['product_card', 'product_list'])) {
            return collect();
        }

        $ids = [];
        if ($this->component_type === 'product_card' && !empty($this->content['product_id'])) {
            $ids = [$this->content['product_id']];
        } elseif ($this->component_type === 'product_list' && !empty($this->content['product_ids'])) {
            $ids = $this->content['product_ids'];
        } elseif (!empty($this->data_source['product_ids'])) {
            $ids = $this->data_source['product_ids'];
        }

        if (empty($ids)) return collect();

        return Food::whereIn('id', $ids)->with(['restaurant', 'storage'])->get();
    }

    // Get actual restaurant data if this is a restaurant component
    public function getRestaurantsAttribute()
    {
        if (!in_array($this->component_type, ['restaurant_card', 'restaurant_list'])) {
            return collect();
        }

        $ids = [];
        if ($this->component_type === 'restaurant_card' && !empty($this->content['restaurant_id'])) {
            $ids = [$this->content['restaurant_id']];
        } elseif ($this->component_type === 'restaurant_list' && !empty($this->content['restaurant_ids'])) {
            $ids = $this->content['restaurant_ids'];
        } elseif (!empty($this->data_source['restaurant_ids'])) {
            $ids = $this->data_source['restaurant_ids'];
        }

        if (empty($ids)) return collect();

        return Restaurant::whereIn('id', $ids)->with(['storage'])->get();
    }

    public function toBuilderJson(): array
    {
        return [
            'id' => $this->id,
            'component_type' => $this->component_type,
            'order' => $this->order,
            'column_span' => $this->column_span,
            'content' => $this->content,
            'settings' => $this->settings,
            'style' => $this->style,
            'data_source' => $this->data_source,
            'action' => $this->action,
            'is_visible' => $this->is_visible,
        ];
    }

    public static function importFromJson(int $sectionId, array $data, int $order): self
    {
        return self::create([
            'section_id' => $sectionId,
            'component_type' => $data['component_type'] ?? 'text',
            'order' => $order,
            'column_span' => $data['column_span'] ?? 12,
            'content' => $data['content'] ?? self::defaultContent($data['component_type'] ?? 'text'),
            'settings' => $data['settings'] ?? self::defaultSettings($data['component_type'] ?? 'text'),
            'style' => $data['style'] ?? null,
            'data_source' => $data['data_source'] ?? null,
            'action' => $data['action'] ?? null,
            'is_visible' => $data['is_visible'] ?? true,
        ]);
    }
}
