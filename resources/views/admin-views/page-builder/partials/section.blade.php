@php
    $settings = $section->settings ?? [];
    $style = $section->style ?? [];
    $bgColor = $settings['background_color'] ?? 'transparent';
    $padding = ($settings['padding_top'] ?? 16) . 'px ' . ($settings['padding_right'] ?? 16) . 'px ' . ($settings['padding_bottom'] ?? 16) . 'px ' . ($settings['padding_left'] ?? 16) . 'px';
    $margin = ($settings['margin_top'] ?? 0) . 'px 0 ' . ($settings['margin_bottom'] ?? 0) . 'px 0';
    $borderRadius = ($settings['border_radius'] ?? 0) . 'px';
    $bgImage = !empty($settings['background_image']) ? "background-image:url('{$settings['background_image']}');background-size:cover;background-position:center;" : '';
    $opacity = isset($settings['opacity']) && $settings['opacity'] != 1 ? "opacity:{$settings['opacity']};" : '';
    $animation = !empty($settings['animation']) ? "animation:{$settings['animation']} " . ($settings['animation_duration'] ?? '0.5s') . " ease;" : '';
    $extraStyle = '';
    if (!empty($style['position']) && $style['position'] !== 'static') $extraStyle .= "position:{$style['position']};";
    if (!empty($style['top'])) $extraStyle .= "top:{$style['top']};";
    if (!empty($style['left'])) $extraStyle .= "left:{$style['left']};";
    if (!empty($style['right'])) $extraStyle .= "right:{$style['right']};";
    if (!empty($style['bottom'])) $extraStyle .= "bottom:{$style['bottom']};";
    if (!empty($style['z_index'])) $extraStyle .= "z-index:{$style['z_index']};";
    if (!empty($style['text_align'])) $extraStyle .= "text-align:{$style['text_align']};";
    if (!empty($style['border'])) $extraStyle .= "border:{$style['border']};";
    if (!empty($style['box_shadow'])) $extraStyle .= "box-shadow:{$style['box_shadow']};";
    if (!empty($style['width'])) $extraStyle .= "width:{$style['width']};";
    if (!empty($style['height'])) $extraStyle .= "height:{$style['height']};";
    if (!empty($style['overflow'])) $extraStyle .= "overflow:{$style['overflow']};";
@endphp

<div class="pb-section {{ $section->section_type }}" style="background:{{ $bgColor }};padding:{{ $padding }};margin:{{ $margin }};border-radius:{{ $borderRadius }};{{ $bgImage }}{{ $opacity }}{{ $animation }}{{ $extraStyle }}">
    @switch($section->section_type)
        @case('hero')
            @include('admin-views.page-builder.partials.sections.hero', ['section' => $section, 'settings' => $settings])
            @break
        
        @case('products_grid')
        @case('products_carousel')
            @include('admin-views.page-builder.partials.sections.products', ['section' => $section, 'settings' => $settings, 'pageSettings' => $pageSettings])
            @break
        
        @case('restaurants_grid')
        @case('restaurants_carousel')
            @include('admin-views.page-builder.partials.sections.restaurants', ['section' => $section, 'settings' => $settings, 'pageSettings' => $pageSettings])
            @break
        
        @case('text_block')
            @include('admin-views.page-builder.partials.sections.text', ['section' => $section, 'settings' => $settings])
            @break
        
        @case('image_banner')
            @include('admin-views.page-builder.partials.sections.image', ['section' => $section, 'settings' => $settings])
            @break
        
        @case('button_group')
            @include('admin-views.page-builder.partials.sections.buttons', ['section' => $section, 'settings' => $settings, 'pageSettings' => $pageSettings])
            @break
        
        @case('spacer')
            <div class="pb-spacer" style="height: {{ $settings['height'] ?? 24 }}px;"></div>
            @break
        
        @case('divider')
            <hr class="pb-divider" style="border-color: {{ $settings['color'] ?? '#e0e0e0' }}; border-width: {{ $settings['thickness'] ?? 1 }}px; border-style: {{ $settings['style'] ?? 'solid' }};">
            @break
        
        @case('countdown')
            @include('admin-views.page-builder.partials.sections.countdown', ['section' => $section, 'settings' => $settings, 'pageSettings' => $pageSettings])
            @break

        @case('tabs')
            @include('admin-views.page-builder.partials.sections.tabs', ['section' => $section, 'settings' => $settings, 'pageSettings' => $pageSettings])
            @break

        @case('restaurant_foods')
            @include('admin-views.page-builder.partials.sections.restaurant-foods', ['section' => $section, 'settings' => $settings, 'pageSettings' => $pageSettings])
            @break
        
        @default
            @foreach($section->components as $component)
                @if($component->is_visible)
                    @include('admin-views.page-builder.partials.component', ['component' => $component, 'pageSettings' => $pageSettings])
                @endif
            @endforeach
    @endswitch
</div>
