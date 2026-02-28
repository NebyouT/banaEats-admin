@php
    $settings = $section->settings ?? [];
    $style = $section->style ?? [];
    $bgColor = $settings['background_color'] ?? 'transparent';
    $padding = ($settings['padding_top'] ?? 16) . 'px ' . ($settings['padding_right'] ?? 16) . 'px ' . ($settings['padding_bottom'] ?? 16) . 'px ' . ($settings['padding_left'] ?? 16) . 'px';
@endphp

<div class="pb-section {{ $section->section_type }}" style="background: {{ $bgColor }}; padding: {{ $padding }};">
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
        
        @default
            @foreach($section->components as $component)
                @if($component->is_visible)
                    @include('admin-views.page-builder.partials.component', ['component' => $component, 'pageSettings' => $pageSettings])
                @endif
            @endforeach
    @endswitch
</div>
