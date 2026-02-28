@extends('layouts.admin.app')

@section('title', translate('Preview') . ' - ' . $page->title)

@push('css_or_js')
<style>
.preview-toolbar { padding: 12px 16px; background: #fff; border-bottom: 1px solid #e7eaf3; display: flex; align-items: center; gap: 12px; }
.preview-wrap { display: flex; justify-content: center; padding: 20px; background: #f0f2f5; min-height: calc(100vh - 130px); }
.phone-frame { background: #1a1a1a; border-radius: 40px; padding: 12px; box-shadow: 0 20px 60px rgba(0,0,0,.3); transition: width .3s; }
.phone-screen { width: 100%; height: 100%; background: #fff; border-radius: 28px; overflow: hidden; }
.phone-status-bar { height: 44px; background: linear-gradient(135deg,#1a1a1a,#2a2a2a); display: flex; align-items: center; justify-content: space-between; padding: 0 20px; color: #fff; font-size: 11px; }
.phone-content { height: calc(100% - 78px); overflow-y: auto; }
.phone-home-bar { height: 34px; display: flex; align-items: center; justify-content: center; background: #fff; }
.phone-home-bar::after { content: ''; width: 120px; height: 4px; background: #ddd; border-radius: 2px; }
.device-btn { padding: 6px 14px; border: 1.5px solid #ddd; border-radius: 8px; background: #fff; font-size: 12px; font-weight: 600; cursor: pointer; transition: .2s; }
.device-btn.active, .device-btn:hover { border-color: #FC6A57; background: #fff5f4; color: #FC6A57; }
</style>
@endpush

@section('content')
<div class="content container-fluid p-0">
    <div class="preview-toolbar">
        <a href="{{ route('admin.page-builder.edit', $page->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="tio-arrow-backward mr-1"></i> {{ translate('Back to Editor') }}
        </a>
        <span class="font-weight-bold">{{ $page->title }}</span>
        <div class="ml-auto d-flex gap-2">
            <button class="device-btn active" data-w="375" data-h="812">Mobile</button>
            <button class="device-btn" data-w="768" data-h="1024">Tablet</button>
            <button class="device-btn" data-w="1024" data-h="768">Desktop</button>
        </div>
        <a href="{{ route('admin.page-builder.render', $page->id) }}" target="_blank" class="btn btn-sm btn-primary ml-3">
            <i class="tio-open-in-new mr-1"></i> {{ translate('Open Full Page') }}
        </a>
    </div>

    <div class="preview-wrap">
        <div class="phone-frame" id="phoneFrame" style="width: 399px; height: 836px;">
            <div class="phone-screen">
                <div class="phone-status-bar">
                    <span id="statusTime">9:41</span>
                    <div>
                        <i class="tio-signal"></i>
                        <i class="tio-wifi ml-1"></i>
                        <i class="tio-battery-full ml-1"></i>
                    </div>
                </div>
                <div class="phone-content">
                    <iframe src="{{ route('admin.page-builder.render', $page->id) }}" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
                <div class="phone-home-bar"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
$(document).ready(function() {
    // Update time
    function updateTime() {
        const now = new Date();
        $('#statusTime').text(now.getHours() + ':' + String(now.getMinutes()).padStart(2, '0'));
    }
    updateTime();
    setInterval(updateTime, 60000);

    // Device switcher
    $('.device-btn').on('click', function() {
        $('.device-btn').removeClass('active');
        $(this).addClass('active');
        const w = $(this).data('w');
        const h = $(this).data('h');
        $('#phoneFrame').css({ width: (w + 24) + 'px', height: (h + 24) + 'px' });
    });
});
</script>
@endpush
