@php
    $targetDate = $settings['target_date'] ?? null;
    $title = $settings['title'] ?? 'Offer ends in';
    $bgColor = $settings['background_color'] ?? '#1a1a1a';
    $textColor = $settings['text_color'] ?? '#ffffff';
@endphp

<div style="text-align: center; padding: 20px; background: {{ $bgColor }}; border-radius: 16px;">
    @if($title)
        <div style="color: {{ $textColor }}; font-size: 14px; margin-bottom: 12px; opacity: 0.8;">{{ $title }}</div>
    @endif
    
    @if($targetDate)
        <div class="pb-countdown" data-target="{{ $targetDate }}" style="color: {{ $textColor }};">
            <div class="pb-countdown-item days">
                <div class="pb-countdown-value">00</div>
                <div class="pb-countdown-label">Days</div>
            </div>
            <div class="pb-countdown-item hours">
                <div class="pb-countdown-value">00</div>
                <div class="pb-countdown-label">Hours</div>
            </div>
            <div class="pb-countdown-item minutes">
                <div class="pb-countdown-value">00</div>
                <div class="pb-countdown-label">Min</div>
            </div>
            <div class="pb-countdown-item seconds">
                <div class="pb-countdown-value">00</div>
                <div class="pb-countdown-label">Sec</div>
            </div>
        </div>
    @else
        <div style="color: {{ $textColor }}; opacity: 0.6;">No target date set</div>
    @endif
</div>
