@php
    $layout = $section->section_type === 'products_carousel' ? 'carousel' : 'grid';
    $columns = $settings['columns'] ?? 2;
    $productIds = [];
    
    // Collect product IDs from components
    foreach ($section->components as $comp) {
        if (in_array($comp->component_type, ['product_card', 'product_list'])) {
            $content = $comp->content ?? [];
            if (!empty($content['product_id'])) {
                $productIds[] = $content['product_id'];
            }
            if (!empty($content['product_ids'])) {
                $productIds = array_merge($productIds, $content['product_ids']);
            }
        }
    }
    
    $products = \App\Models\Food::whereIn('id', $productIds)->with(['restaurant', 'storage'])->get();
    $title = $settings['title'] ?? null;
    $showViewAll = $settings['show_view_all'] ?? false;
    $primaryColor = $pageSettings['primary_color'] ?? '#FC6A57';
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
    @forelse($products as $product)
        <div class="pb-product-card" 
             data-action='{"type":"navigate_product","product_id":{{ $product->id }}}'>
            <div class="card-img">
                @if($product->image_full_url)
                    <img src="{{ $product->image_full_url }}" alt="{{ $product->name }}">
                @endif
            </div>
            <div class="card-body">
                <div class="card-title">{{ $product->name }}</div>
                @if($settings['show_restaurant'] ?? true)
                    <div class="card-restaurant">{{ $product->restaurant->name ?? '' }}</div>
                @endif
                <div class="card-footer">
                    <span class="card-price">ETB {{ number_format($product->price, 2) }}</span>
                    @if($settings['show_add_button'] ?? true)
                        <button class="add-btn">+</button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #888;">
            No products selected
        </div>
    @endforelse
</div>
