@php
    $textAlign = $settings['text_align'] ?? 'left';
    $fontSize = $settings['font_size'] ?? 14;
@endphp

@foreach($section->components as $comp)
    @if($comp->is_visible)
        @php
            $content = $comp->content ?? [];
            $compSettings = $comp->settings ?? [];
        @endphp
        
        @if($comp->component_type === 'heading')
            <{{ $content['level'] ?? 'h2' }} class="pb-heading {{ $content['level'] ?? 'h2' }}" 
                style="color: {{ $compSettings['color'] ?? '#1a1a1a' }}; text-align: {{ $compSettings['text_align'] ?? $textAlign }};">
                {{ $content['text'] ?? '' }}
            </{{ $content['level'] ?? 'h2' }}>
        @elseif($comp->component_type === 'text')
            <p class="pb-text" style="color: {{ $compSettings['color'] ?? '#333' }}; font-size: {{ $compSettings['font_size'] ?? $fontSize }}px; text-align: {{ $compSettings['text_align'] ?? $textAlign }}; line-height: {{ $compSettings['line_height'] ?? 1.6 }};">
                {{ $content['text'] ?? '' }}
            </p>
        @endif
    @endif
@endforeach
