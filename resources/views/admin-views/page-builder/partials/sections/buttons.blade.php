@php
    $alignment = $settings['alignment'] ?? 'center';
    $direction = $settings['direction'] ?? 'row';
    $gap = $settings['gap'] ?? 12;
@endphp

<div style="display: flex; flex-direction: {{ $direction }}; gap: {{ $gap }}px; justify-content: {{ $alignment }}; flex-wrap: wrap;">
    @foreach($section->components as $comp)
        @if($comp->is_visible && $comp->component_type === 'button')
            @php
                $content = $comp->content ?? [];
                $compSettings = $comp->settings ?? [];
                $action = $comp->action ?? null;
            @endphp
            <a href="#" class="pb-button" 
               style="background: {{ $compSettings['background_color'] ?? $pageSettings['primary_color'] ?? '#FC6A57' }}; color: {{ $compSettings['text_color'] ?? '#fff' }}; border-radius: {{ $compSettings['border_radius'] ?? 12 }}px; {{ ($compSettings['full_width'] ?? false) ? 'width: 100%;' : '' }}"
               @if($action) data-action='@json($action)' @endif>
                {{ $content['text'] ?? 'Button' }}
            </a>
        @endif
    @endforeach
</div>
