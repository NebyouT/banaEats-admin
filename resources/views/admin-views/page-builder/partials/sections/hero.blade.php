@php
    $bgImage = $settings['background_image'] ?? null;
    $overlayColor = $settings['overlay_color'] ?? 'rgba(0,0,0,0.4)';
    $height = $settings['height'] ?? 300;
    $textAlign = $settings['text_align'] ?? 'center';
    
    $title = null;
    $subtitle = null;
    $buttonText = null;
    $buttonAction = null;
    
    foreach ($section->components as $comp) {
        $content = $comp->content ?? [];
        if ($comp->component_type === 'heading' && !$title) {
            $title = $content['text'] ?? null;
        }
        if ($comp->component_type === 'text' && !$subtitle) {
            $subtitle = $content['text'] ?? null;
        }
        if ($comp->component_type === 'button') {
            $buttonText = $content['text'] ?? 'Learn More';
            $buttonAction = $comp->action ?? null;
        }
    }
@endphp

<div class="pb-hero" style="min-height: {{ $height }}px; text-align: {{ $textAlign }}; @if($bgImage) background-image: url('{{ $bgImage }}'); @endif">
    <div class="pb-hero-overlay" style="background: {{ $overlayColor }};"></div>
    <div class="pb-hero-content">
        @if($title)
            <h1 class="pb-hero-title">{{ $title }}</h1>
        @endif
        @if($subtitle)
            <p class="pb-hero-subtitle">{{ $subtitle }}</p>
        @endif
        @if($buttonText)
            <a href="#" class="pb-button" style="background: {{ $pageSettings['primary_color'] ?? '#FC6A57' }}; color: #fff;"
               @if($buttonAction) data-action='@json($buttonAction)' @endif>
                {{ $buttonText }}
            </a>
        @endif
    </div>
</div>
