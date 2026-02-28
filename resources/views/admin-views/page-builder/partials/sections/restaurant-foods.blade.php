@php
    $restaurantId = $settings['restaurant_id'] ?? null;
    $showLogo = $settings['show_restaurant_logo'] ?? true;
    $showName = $settings['show_restaurant_name'] ?? true;
    $showRating = $settings['show_restaurant_rating'] ?? true;
    $foodSelection = $settings['food_selection'] ?? 'auto';
    $foodCount = $settings['food_count'] ?? 10;
    $selectedFoodIds = $settings['selected_food_ids'] ?? [];
    $showFoodPrice = $settings['show_food_price'] ?? true;
    $showFoodName = $settings['show_food_name'] ?? true;
    $showFoodImage = $settings['show_food_image'] ?? true;
    $cardBg = $settings['card_bg_color'] ?? '#ffffff';
    $foodCardBg = $settings['food_card_bg_color'] ?? '#f8f9fa';
    $nameColor = $settings['name_color'] ?? '#1a1a1a';
    $primaryColor = $pageSettings['primary_color'] ?? '#FC6A57';

    $restaurant = null;
    $foods = collect();

    if ($restaurantId) {
        $restaurant = \App\Models\Restaurant::with(['storage'])->find($restaurantId);
        if ($restaurant) {
            if ($foodSelection === 'selected' && !empty($selectedFoodIds)) {
                $foods = \App\Models\Food::whereIn('id', $selectedFoodIds)
                    ->where('status', 1)
                    ->with(['storage'])
                    ->limit($foodCount)
                    ->get();
            } else {
                $foods = \App\Models\Food::where('restaurant_id', $restaurantId)
                    ->where('status', 1)
                    ->with(['storage'])
                    ->limit($foodCount)
                    ->get();
            }
        }
    }
@endphp

@if($restaurant)
<div class="pb-resto-foods" style="background:{{ $cardBg }};border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);"
     data-action='{"type":"navigate_restaurant","restaurant_id":{{ $restaurant->id }}}'>
    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;">
        @if($showLogo)
        <div style="width:52px;height:52px;border-radius:12px;overflow:hidden;flex-shrink:0;background:#f5f5f5;">
            @if($restaurant->logo_full_url)
                <img src="{{ $restaurant->logo_full_url }}" style="width:100%;height:100%;object-fit:cover;" alt="{{ $restaurant->name }}">
            @endif
        </div>
        @endif
        <div style="flex:1;min-width:0;">
            @if($showName)
                <div style="font-size:16px;font-weight:700;color:{{ $nameColor }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $restaurant->name }}</div>
            @endif
            @if($showRating)
                <div style="font-size:12px;color:#888;margin-top:2px;">
                    <span style="color:#FFB800;">&#9733;</span> {{ number_format($restaurant->rating ?? 0, 1) }}
                    @if($restaurant->delivery_time)
                        <span style="margin-left:8px;">{{ $restaurant->delivery_time }} min</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if($foods->count() > 0)
    <div style="display:flex;gap:10px;padding:0 16px 14px;overflow-x:auto;-webkit-overflow-scrolling:touch;" class="pb-carousel">
        @foreach($foods as $food)
        <div style="width:120px;flex-shrink:0;background:{{ $foodCardBg }};border-radius:10px;overflow:hidden;cursor:pointer;"
             data-action='{"type":"navigate_product","product_id":{{ $food->id }}}'>
            @if($showFoodImage)
            <div style="height:80px;overflow:hidden;background:#eee;">
                @if($food->image_full_url)
                    <img src="{{ $food->image_full_url }}" style="width:100%;height:100%;object-fit:cover;" alt="{{ $food->name }}">
                @endif
            </div>
            @endif
            <div style="padding:8px 10px;">
                @if($showFoodName)
                    <div style="font-size:12px;font-weight:600;color:{{ $nameColor }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $food->name }}</div>
                @endif
                @if($showFoodPrice)
                    <div style="font-size:13px;font-weight:700;color:{{ $primaryColor }};margin-top:2px;">ETB {{ number_format($food->price, 2) }}</div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:16px;color:#aaa;font-size:13px;">No foods available</div>
    @endif
</div>
@else
<div style="text-align:center;padding:24px;color:#aaa;font-size:13px;background:{{ $cardBg }};border-radius:16px;">
    No restaurant selected
</div>
@endif
