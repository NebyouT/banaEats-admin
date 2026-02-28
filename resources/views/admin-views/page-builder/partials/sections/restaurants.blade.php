@php
    $layout = $section->section_type === 'restaurants_carousel' ? 'carousel' : 'grid';
    $columns = $settings['columns'] ?? 2;
    $restaurantIds = [];
    
    foreach ($section->components as $comp) {
        if (in_array($comp->component_type, ['restaurant_card', 'restaurant_list'])) {
            $content = $comp->content ?? [];
            if (!empty($content['restaurant_id'])) {
                $restaurantIds[] = $content['restaurant_id'];
            }
            if (!empty($content['restaurant_ids'])) {
                $restaurantIds = array_merge($restaurantIds, $content['restaurant_ids']);
            }
        }
    }
    
    $restaurants = \App\Models\Restaurant::whereIn('id', $restaurantIds)->with(['storage'])->get();
    $title = $settings['title'] ?? null;
    $showViewAll = $settings['show_view_all'] ?? false;
@endphp

@if($title || $showViewAll)
<div class="section-header">
    @if($title)
        <h3 class="section-title">{{ $title }}</h3>
    @endif
    @if($showViewAll)
        <a href="#" class="section-link">View All</a>
    @endif
</div>
@endif

@if($layout === 'carousel')
<div class="pb-carousel">
@else
<div class="pb-grid cols-{{ $columns }}">
@endif
    @forelse($restaurants as $restaurant)
        <div class="pb-restaurant-card" 
             data-action='{"type":"navigate_restaurant","restaurant_id":{{ $restaurant->id }}}'>
            <div class="card-img">
                @if($restaurant->logo_full_url)
                    <img src="{{ $restaurant->logo_full_url }}" alt="{{ $restaurant->name }}">
                @endif
            </div>
            <div class="card-body">
                <div class="card-title">{{ $restaurant->name }}</div>
                <div class="card-meta">
                    @if($settings['show_rating'] ?? true)
                        <span class="rating">⭐ {{ number_format($restaurant->rating ?? 0, 1) }}</span>
                    @endif
                    @if($settings['show_delivery_time'] ?? true)
                        <span>{{ $restaurant->delivery_time ?? '20-30' }} min</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #888;">
            No restaurants selected
        </div>
    @endforelse
</div>
