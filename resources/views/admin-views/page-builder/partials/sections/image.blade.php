@php
    $height = $settings['height'] ?? 200;
    $objectFit = $settings['object_fit'] ?? 'cover';
    $borderRadius = $settings['border_radius'] ?? 12;
    
    $imageUrl = null;
    $action = null;
    
    foreach ($section->components as $comp) {
        if ($comp->component_type === 'image' && $comp->is_visible) {
            $content = $comp->content ?? [];
            $imageUrl = $content['url'] ?? null;
            $action = $comp->action ?? null;
            break;
        }
    }
@endphp

@if($imageUrl)
<div class="pb-image" style="border-radius: {{ $borderRadius }}px; overflow: hidden;"
     @if($action) data-action='@json($action)' style="cursor: pointer;" @endif>
    <img src="{{ $imageUrl }}" alt="" style="width: 100%; height: {{ $height }}px; object-fit: {{ $objectFit }};">
</div>
@else
<div class="pb-image" style="height: {{ $height }}px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: {{ $borderRadius }}px;">
    <span style="color: #aaa;">No image selected</span>
</div>
@endif
