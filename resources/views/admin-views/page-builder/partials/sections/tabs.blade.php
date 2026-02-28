@php
    $tabLabels = $settings['tab_labels'] ?? ['Tab 1', 'Tab 2'];
    $activeColor = $settings['tab_active_color'] ?? ($pageSettings['primary_color'] ?? '#FC6A57');
    $textColor = $settings['tab_text_color'] ?? '#333333';
    $activeTextColor = $settings['tab_active_text_color'] ?? '#ffffff';
    $tabBg = $settings['tab_bg_color'] ?? '#ffffff';
    $borderRadius = $settings['tab_border_radius'] ?? 8;
    $contentPadding = $settings['content_padding'] ?? 16;
    $sectionId = 'tabs_' . $section->id;

    // Group components by tab index (stored in component settings.tab_index)
    $tabComponents = [];
    foreach ($section->components as $component) {
        $tabIdx = $component->settings['tab_index'] ?? 0;
        if (!isset($tabComponents[$tabIdx])) $tabComponents[$tabIdx] = [];
        $tabComponents[$tabIdx][] = $component;
    }
@endphp

<div class="pb-tabs" id="{{ $sectionId }}">
    <div class="pb-tabs-nav" style="display:flex;gap:0;background:{{ $tabBg }};border-bottom:2px solid #eee;overflow-x:auto;">
        @foreach($tabLabels as $idx => $label)
        <div class="pb-tab-btn {{ $idx === 0 ? 'active' : '' }}"
             data-tab="{{ $sectionId }}_{{ $idx }}"
             style="padding:10px 20px;font-size:13px;font-weight:600;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;white-space:nowrap;
                    color:{{ $idx === 0 ? $activeTextColor : $textColor }};
                    {{ $idx === 0 ? 'border-bottom-color:'.$activeColor.';background:'.$activeColor.';border-radius:'.$borderRadius.'px '.$borderRadius.'px 0 0;' : '' }}">
            {{ $label }}
        </div>
        @endforeach
    </div>
    @foreach($tabLabels as $idx => $label)
    <div class="pb-tab-content {{ $idx === 0 ? 'active' : '' }}" id="{{ $sectionId }}_{{ $idx }}"
         style="padding:{{ $contentPadding }}px;{{ $idx !== 0 ? 'display:none;' : '' }}">
        @if(!empty($tabComponents[$idx]))
            @foreach($tabComponents[$idx] as $component)
                @if($component->is_visible)
                    @include('admin-views.page-builder.partials.component', ['component' => $component, 'pageSettings' => $pageSettings])
                @endif
            @endforeach
        @else
            <div style="text-align:center;color:#aaa;padding:20px;font-size:13px;">Tab content</div>
        @endif
    </div>
    @endforeach
</div>

<script>
(function() {
    var tabsEl = document.getElementById('{{ $sectionId }}');
    if (!tabsEl) return;
    var btns = tabsEl.querySelectorAll('.pb-tab-btn');
    btns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            btns.forEach(function(b) {
                b.classList.remove('active');
                b.style.borderBottomColor = 'transparent';
                b.style.background = 'transparent';
                b.style.color = '{{ $textColor }}';
            });
            this.classList.add('active');
            this.style.borderBottomColor = '{{ $activeColor }}';
            this.style.background = '{{ $activeColor }}';
            this.style.color = '{{ $activeTextColor }}';
            tabsEl.querySelectorAll('.pb-tab-content').forEach(function(c) { c.style.display = 'none'; });
            document.getElementById(this.getAttribute('data-tab')).style.display = 'block';
        });
    });
})();
</script>
