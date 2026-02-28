@extends('layouts.admin.app')

@section('title', translate('Edit Page') . ' - ' . $page->title)

@push('css_or_js')
<style>
/* === LAYOUT === */
.builder-container { display: flex; height: calc(100vh - 70px); overflow: hidden; }
.builder-sidebar { width: 260px; background: #fff; border-right: 1px solid #e7eaf3; display: flex; flex-direction: column; flex-shrink: 0; }
.builder-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f0f2f5; }
.builder-canvas-wrap { flex: 1; overflow: auto; padding: 20px; display: flex; justify-content: center; }
.builder-properties { width: 340px; background: #fff; border-left: 1px solid #e7eaf3; overflow-y: auto; flex-shrink: 0; }

/* === SIDEBAR === */
.sidebar-header { padding: 12px 16px; border-bottom: 1px solid #e7eaf3; display: flex; align-items: center; }
.sidebar-tabs { display: flex; border-bottom: 1px solid #e7eaf3; }
.sidebar-tab { flex: 1; padding: 10px; text-align: center; font-size: 11px; font-weight: 600; color: #8c98a4; cursor: pointer; border-bottom: 2px solid transparent; }
.sidebar-tab.active { color: #FC6A57; border-bottom-color: #FC6A57; }
.sidebar-content { flex: 1; overflow-y: auto; padding: 10px; }
.builder-item { display: flex; align-items: center; gap: 8px; padding: 8px 10px; background: #f8f9fa; border: 1px solid #e7eaf3; border-radius: 6px; margin-bottom: 6px; cursor: grab; transition: .15s; }
.builder-item:hover { background: #fff; border-color: #FC6A57; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.builder-item i { font-size: 16px; color: #FC6A57; width: 20px; text-align: center; }
.builder-item-name { font-size: 12px; font-weight: 600; color: #1e2022; }
.builder-item-desc { font-size: 10px; color: #8c98a4; }

/* === TOOLBAR === */
.builder-toolbar { padding: 8px 16px; background: #fff; border-bottom: 1px solid #e7eaf3; display: flex; align-items: center; gap: 12px; }
.toolbar-title { flex: 1; }
.device-switcher { display: flex; gap: 3px; background: #f8f9fa; border-radius: 6px; padding: 3px; }
.device-btn { width: 30px; height: 30px; border: none; background: transparent; border-radius: 5px; cursor: pointer; color: #8c98a4; }
.device-btn.active { background: #fff; color: #FC6A57; box-shadow: 0 1px 3px rgba(0,0,0,.1); }

/* === CANVAS === */
.builder-canvas { width: 100%; max-width: 420px; min-height: 600px; background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.1); overflow: hidden; transition: max-width .3s; }
.canvas-header { padding: 10px 16px; background: #1a1a1a; color: #fff; display: flex; align-items: center; justify-content: space-between; font-size: 13px; font-weight: 600; }
.canvas-body { min-height: 500px; position: relative; }
.canvas-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 400px; color: #8c98a4; text-align: center; padding: 40px; }
.canvas-empty i { font-size: 48px; margin-bottom: 16px; color: #dee2e6; }

/* === SECTIONS ON CANVAS === */
.canvas-section { position: relative; border: 2px dashed transparent; margin: 4px; border-radius: 6px; transition: .15s; min-height: 50px; }
.canvas-section:hover { border-color: #dee2e6; }
.canvas-section.selected { border-color: #FC6A57; background: rgba(252,106,87,.02); }
.canvas-section.sortable-ghost { opacity: .3; }
.section-toolbar { position: absolute; top: -28px; right: 6px; display: none; gap: 2px; background: #1e2022; border-radius: 5px; padding: 3px; z-index: 10; }
.canvas-section:hover .section-toolbar, .canvas-section.selected .section-toolbar { display: flex; }
.section-toolbar button { width: 24px; height: 24px; border: none; background: transparent; color: #fff; border-radius: 3px; cursor: pointer; font-size: 11px; }
.section-toolbar button:hover { background: rgba(255,255,255,.15); }
.section-label { position: absolute; top: 2px; left: 6px; font-size: 9px; font-weight: 700; color: #adb5bd; text-transform: uppercase; letter-spacing: .5px; cursor: grab; padding: 2px 6px; border-radius: 3px; transition: .15s; display: flex; align-items: center; gap: 4px; }
.section-label:hover { background: rgba(0,0,0,.05); color: #FC6A57; }
.section-label:active { cursor: grabbing; }
.section-label .drag-icon { font-size: 10px; opacity: .6; }
.section-components { padding: 22px 10px 10px; min-height: 40px; }

/* === COMPONENTS ON CANVAS === */
.canvas-component { position: relative; border: 1px dashed transparent; border-radius: 5px; padding: 6px; margin-bottom: 6px; transition: .15s; }
.canvas-component:hover { border-color: #8DC63F; }
.canvas-component.selected { border-color: #8DC63F; background: rgba(141,198,63,.04); }
.component-toolbar { position: absolute; top: -24px; right: 2px; display: none; gap: 2px; background: #8DC63F; border-radius: 4px; padding: 2px; z-index: 10; }
.canvas-component:hover .component-toolbar, .canvas-component.selected .component-toolbar { display: flex; }
.component-toolbar button { width: 20px; height: 20px; border: none; background: transparent; color: #fff; border-radius: 3px; cursor: pointer; font-size: 10px; }

/* === PROPERTIES PANEL === */
.properties-header { padding: 12px 16px; border-bottom: 1px solid #e7eaf3; display: flex; align-items: center; justify-content: space-between; }
.properties-header h5 { margin: 0; font-size: 13px; font-weight: 700; }
.properties-body { padding: 12px 16px; }
.prop-group { margin-bottom: 16px; border-bottom: 1px solid #f0f2f5; padding-bottom: 12px; }
.prop-group:last-child { border-bottom: none; }
.prop-group-title { font-size: 10px; font-weight: 700; color: #8c98a4; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; }
.prop-group-title i { font-size: 12px; transition: .2s; }
.prop-group-title.collapsed i { transform: rotate(-90deg); }
.prop-group-content { transition: .2s; }
.prop-group-title.collapsed + .prop-group-content { display: none; }
.prop-row { margin-bottom: 10px; }
.prop-label { font-size: 11px; font-weight: 500; color: #1e2022; margin-bottom: 3px; }
.prop-input { font-size: 12px; }
.prop-input.form-control { height: 32px; padding: 4px 8px; }
.prop-input.form-control-sm { height: 28px; }
select.prop-input { height: 32px; font-size: 12px; }
textarea.prop-input { font-size: 12px; resize: vertical; }
.color-input-wrap { display: flex; gap: 6px; align-items: center; }
.color-input-wrap input[type="color"] { width: 32px; height: 32px; padding: 1px; border: 1px solid #e7eaf3; border-radius: 5px; cursor: pointer; }
.color-input-wrap input[type="text"] { flex: 1; }
/* Inline number group */
.prop-inline { display: flex; gap: 6px; }
.prop-inline .prop-inline-item { flex: 1; text-align: center; }
.prop-inline .prop-inline-item label { font-size: 9px; color: #adb5bd; display: block; margin-bottom: 2px; }
.prop-inline .prop-inline-item input { text-align: center; padding: 2px; }
/* Toggle switch */
.prop-toggle { display: flex; align-items: center; justify-content: space-between; }
.prop-toggle .switch { position: relative; width: 36px; height: 20px; }
.prop-toggle .switch input { display: none; }
.prop-toggle .slider { position: absolute; cursor: pointer; inset: 0; background: #ccc; border-radius: 10px; transition: .2s; }
.prop-toggle .slider:before { content: ""; position: absolute; width: 14px; height: 14px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .2s; }
.prop-toggle input:checked + .slider { background: #FC6A57; }
.prop-toggle input:checked + .slider:before { transform: translateX(16px); }
/* Image upload area */
.image-upload-area { border: 2px dashed #dee2e6; border-radius: 8px; padding: 16px; text-align: center; cursor: pointer; transition: .2s; background: #fafbfc; position: relative; overflow: hidden; }
.image-upload-area:hover { border-color: #FC6A57; background: #fff5f4; }
.image-upload-area img { max-width: 100%; max-height: 120px; border-radius: 6px; }
.image-upload-area .upload-placeholder { color: #adb5bd; }
.image-upload-area .upload-placeholder i { font-size: 24px; display: block; margin-bottom: 4px; }
.image-upload-area input[type="file"] { display: none; }

/* === PREVIEW COMPONENTS === */
.preview-text { font-size: 14px; color: #333; line-height: 1.5; word-break: break-word; }
.preview-heading { font-weight: 700; color: #1a1a1a; word-break: break-word; }
.preview-heading.h1 { font-size: 26px; }
.preview-heading.h2 { font-size: 22px; }
.preview-heading.h3 { font-size: 18px; }
.preview-image { width: 100%; border-radius: 8px; overflow: hidden; background: #f0f0f0; min-height: 80px; display: flex; align-items: center; justify-content: center; color: #aaa; }
.preview-image img { width: 100%; display: block; border-radius: 8px; }
.preview-button { display: inline-block; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 13px; text-align: center; }
.preview-spacer { background: repeating-linear-gradient(45deg, #f8f9fa, #f8f9fa 10px, #fff 10px, #fff 20px); border-radius: 4px; }
.preview-divider { border-top: 1px solid #e0e0e0; margin: 8px 0; }
.preview-grid { display: grid; gap: 10px; }
.preview-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
.preview-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
.preview-carousel { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 6px; }
.preview-carousel::-webkit-scrollbar { height: 3px; }
.preview-carousel::-webkit-scrollbar-thumb { background: #ddd; border-radius: 3px; }
.preview-carousel > * { flex-shrink: 0; }
/* Product card */
.preview-product-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.06); }
.preview-product-card .card-img { height: 100px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.preview-product-card .card-img img { width: 100%; height: 100%; object-fit: cover; }
.preview-product-card .card-body { padding: 8px 10px; }
.preview-product-card .card-title { font-size: 12px; font-weight: 600; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.preview-product-card .card-subtitle { font-size: 10px; color: #888; margin-bottom: 4px; }
.preview-product-card .card-price { font-size: 14px; font-weight: 700; color: #FC6A57; }
.preview-product-card .card-rating { font-size: 10px; color: #FFB800; }
.preview-product-card .card-desc { font-size: 10px; color: #888; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
/* Restaurant card */
.preview-restaurant-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.06); }
.preview-restaurant-card .card-img { height: 80px; background: #f5f5f5; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.preview-restaurant-card .card-img img { width: 100%; height: 100%; object-fit: cover; }
.preview-restaurant-card .card-body { padding: 8px 10px; }
.preview-restaurant-card .card-title { font-size: 12px; font-weight: 600; }
.preview-restaurant-card .card-meta { font-size: 10px; color: #888; }
/* Restaurant+Foods combo */
.preview-resto-foods { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
.preview-resto-foods .resto-header { display: flex; align-items: center; gap: 10px; padding: 12px; }
.preview-resto-foods .resto-logo { width: 48px; height: 48px; border-radius: 10px; background: #f5f5f5; overflow: hidden; flex-shrink: 0; }
.preview-resto-foods .resto-logo img { width: 100%; height: 100%; object-fit: cover; }
.preview-resto-foods .resto-info { flex: 1; }
.preview-resto-foods .resto-name { font-size: 14px; font-weight: 700; }
.preview-resto-foods .resto-meta { font-size: 11px; color: #888; }
.preview-resto-foods .food-scroll { display: flex; gap: 10px; padding: 0 12px 12px; overflow-x: auto; }
.preview-resto-foods .food-scroll::-webkit-scrollbar { height: 3px; }
.preview-resto-foods .food-mini { width: 110px; flex-shrink: 0; background: #f8f9fa; border-radius: 8px; overflow: hidden; }
.preview-resto-foods .food-mini-img { height: 70px; background: #eee; overflow: hidden; }
.preview-resto-foods .food-mini-img img { width: 100%; height: 100%; object-fit: cover; }
.preview-resto-foods .food-mini-body { padding: 6px 8px; }
.preview-resto-foods .food-mini-name { font-size: 11px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.preview-resto-foods .food-mini-price { font-size: 12px; font-weight: 700; color: #FC6A57; }
/* Tabs preview */
.preview-tabs .tab-nav { display: flex; gap: 0; border-bottom: 2px solid #eee; }
.preview-tabs .tab-btn { padding: 8px 16px; font-size: 12px; font-weight: 600; color: #888; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; }
.preview-tabs .tab-btn.active { color: #FC6A57; border-bottom-color: #FC6A57; }
.preview-tabs .tab-content { padding: 12px; min-height: 60px; color: #aaa; font-size: 12px; }

/* === DATA PICKER MODAL === */
.data-picker-modal .modal-body { max-height: 60vh; overflow-y: auto; }
.picker-filters { display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
.picker-filters select, .picker-filters input { font-size: 12px; height: 32px; }
.picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 8px; }
.picker-item { border: 2px solid #e7eaf3; border-radius: 8px; padding: 8px; cursor: pointer; text-align: center; transition: .15s; }
.picker-item:hover { border-color: #FC6A57; }
.picker-item.selected { border-color: #FC6A57; background: #fff5f4; }
.picker-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; margin-bottom: 4px; }
.picker-item .name { font-size: 11px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.picker-item .meta { font-size: 10px; color: #8c98a4; }
.picker-selected-bar { background: #f8f9fa; border-radius: 6px; padding: 6px 10px; margin-bottom: 10px; font-size: 11px; display: flex; align-items: center; justify-content: space-between; }
</style>
@endpush

@section('content')
<div class="builder-container">
    <!-- LEFT SIDEBAR -->
    <div class="builder-sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.page-builder.index') }}" class="btn btn-sm btn-outline-secondary"><i class="tio-arrow-backward"></i></a>
            <span class="ml-2 font-weight-bold" style="font-size:13px">{{ Str::limit($page->title, 18) }}</span>
        </div>
        <div class="sidebar-tabs">
            <div class="sidebar-tab active" data-tab="sections">Sections</div>
            <div class="sidebar-tab" data-tab="components">Components</div>
        </div>
        <div class="sidebar-content" id="sidebar-sections">
            @foreach($sectionTypes as $type => $info)
            <div class="builder-item" draggable="true" data-type="section" data-section-type="{{ $type }}">
                <i class="{{ $info['icon'] }}"></i>
                <div class="builder-item-info">
                    <div class="builder-item-name">{{ $info['name'] }}</div>
                    <div class="builder-item-desc">{{ $info['description'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="sidebar-content d-none" id="sidebar-components">
            @foreach($componentTypes as $type => $info)
            <div class="builder-item" draggable="true" data-type="component" data-component-type="{{ $type }}">
                <i class="{{ $info['icon'] }}"></i>
                <div class="builder-item-info">
                    <div class="builder-item-name">{{ $info['name'] }}</div>
                    <div class="builder-item-desc">{{ $info['description'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- MAIN CANVAS -->
    <div class="builder-main">
        <div class="builder-toolbar">
            <div class="toolbar-title">
                <input type="text" id="page-title-input" value="{{ $page->title }}" class="form-control form-control-sm" style="max-width: 260px; display: inline-block; font-size: 13px;">
            </div>
            <div class="device-switcher">
                <button class="device-btn active" data-width="375" title="Mobile"><i class="tio-android-phone"></i></button>
                <button class="device-btn" data-width="768" title="Tablet"><i class="tio-tablet"></i></button>
                <button class="device-btn" data-width="1024" title="Desktop"><i class="tio-monitor"></i></button>
            </div>
            <div class="d-flex gap-2" style="gap:6px">
                <a href="{{ route('admin.page-builder.preview', $page->id) }}" target="_blank" class="btn btn-sm btn-outline-info"><i class="tio-visible mr-1"></i>Preview</a>
                <button type="button" class="btn btn-sm btn-primary" id="save-page-btn"><i class="tio-save mr-1"></i>Save</button>
                <button type="button" class="btn btn-sm {{ $page->is_published ? 'btn-success' : 'btn-warning' }}" id="publish-btn">
                    <i class="tio-{{ $page->is_published ? 'checkmark-circle' : 'cloud-upload' }} mr-1"></i>{{ $page->is_published ? 'Published' : 'Publish' }}
                </button>
            </div>
        </div>
        <div class="builder-canvas-wrap">
            <div class="builder-canvas" id="builder-canvas" style="max-width: 375px;">
                <div class="canvas-header">
                    <span class="title">{{ $page->title }}</span>
                    <i class="tio-more-vertical"></i>
                </div>
                <div class="canvas-body" id="canvas-body">
                    @if($page->sections->isEmpty())
                    <div class="canvas-empty" id="canvas-empty"><i class="tio-drag-and-drop"></i><p>Drag sections here to start building</p></div>
                    @endif
                    <div id="sections-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: PROPERTIES PANEL -->
    <div class="builder-properties" id="properties-panel">
        <div class="properties-header">
            <h5>Page Settings</h5>
        </div>
        <div class="properties-body" id="properties-body">
            <!-- Default: page-level settings -->
            <div id="page-settings-default">
                <div class="prop-group">
                    <div class="prop-group-title">Background</div>
                    <div class="prop-group-content">
                        <div class="prop-row">
                            <div class="prop-label">Color</div>
                            <div class="color-input-wrap">
                                <input type="color" id="page-bg-color" value="{{ $page->settings['background_color'] ?? '#ffffff' }}" onchange="updatePageSetting('background_color', this.value)">
                                <input type="text" class="form-control prop-input" value="{{ $page->settings['background_color'] ?? '#ffffff' }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="prop-group">
                    <div class="prop-group-title">Theme Colors</div>
                    <div class="prop-group-content">
                        <div class="prop-row">
                            <div class="prop-label">Primary</div>
                            <div class="color-input-wrap">
                                <input type="color" id="page-primary-color" value="{{ $page->settings['primary_color'] ?? '#FC6A57' }}" onchange="updatePageSetting('primary_color', this.value)">
                                <input type="text" class="form-control prop-input" value="{{ $page->settings['primary_color'] ?? '#FC6A57' }}">
                            </div>
                        </div>
                        <div class="prop-row">
                            <div class="prop-label">Secondary</div>
                            <div class="color-input-wrap">
                                <input type="color" id="page-secondary-color" value="{{ $page->settings['secondary_color'] ?? '#8DC63F' }}" onchange="updatePageSetting('secondary_color', this.value)">
                                <input type="text" class="form-control prop-input" value="{{ $page->settings['secondary_color'] ?? '#8DC63F' }}">
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-muted text-center" style="font-size: 11px;"><i class="tio-info-outlined mr-1"></i>Click a section or component to edit</p>
            </div>
        </div>
    </div>
</div>

<!-- DATA PICKER MODAL (Products/Restaurants/Categories) -->
<div class="modal fade data-picker-modal" id="dataPickerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title" id="dataPickerTitle">Select Items</h6>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="picker-filters">
                    <input type="text" class="form-control flex-grow-1" id="dataPickerSearch" placeholder="Search by name...">
                    <select class="form-control" id="pickerFilterRestaurant" style="max-width:180px"><option value="">All Restaurants</option></select>
                    <select class="form-control" id="pickerFilterCategory" style="max-width:160px"><option value="">All Categories</option></select>
                </div>
                <div class="picker-selected-bar d-none" id="pickerSelectedBar">
                    <span><strong id="pickerSelectedCount">0</strong> selected</span>
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="clearPickerSelection()">Clear all</button>
                </div>
                <div class="picker-grid" id="dataPickerGrid"></div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" id="dataPickerConfirm">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
"use strict";

// ══════════════════════════════════════════════════════════════════════
// GLOBAL VARIABLES (accessible from onclick handlers)
// ══════════════════════════════════════════════════════════════════════
var pageData = @json($page->toBuilderJson());
var selectedSection = null;
var selectedComponent = null;
var sortableInstances = []; // Track Sortable instances to destroy on re-render
var SECTION_TYPES = {!! json_encode($sectionTypes) !!};
var COMPONENT_TYPES = {!! json_encode($componentTypes) !!};
var DEFAULT_IMG = '{{ dynamicAsset("public/assets/admin/img/100x100/food-default-image.png") }}';
var URLS = {
    save: '{{ route("admin.page-builder.save-structure", $page->id) }}',
    publish: '{{ route("admin.page-builder.publish", $page->id) }}',
    upload: '{{ route("admin.page-builder.upload-media") }}',
    searchProducts: '{{ route("admin.page-builder.search-products") }}',
    searchRestaurants: '{{ route("admin.page-builder.search-restaurants") }}',
    searchCategories: '{{ route("admin.page-builder.search-categories") }}',
};
var CSRF = '{{ csrf_token() }}';

// ══════════════════════════════════════════════════════════════════════
// GLOBAL FUNCTIONS (accessible from onclick handlers in HTML)
// ══════════════════════════════════════════════════════════════════════
function moveSection(i, dir) {
    var n = i + dir;
    if (n < 0 || n >= pageData.sections.length) return;
    var item = pageData.sections.splice(i, 1)[0];
    pageData.sections.splice(n, 0, item);
    pageData.sections.forEach(function(s, x) { s.order = x; });
    renderCanvas();
}

function duplicateSection(i) {
    var copy = JSON.parse(JSON.stringify(pageData.sections[i]));
    copy.id = 'new_' + Date.now();
    copy.name += ' (Copy)';
    copy.components.forEach(function(c) { c.id = 'new_' + Date.now() + '_' + Math.random(); });
    pageData.sections.splice(i + 1, 0, copy);
    pageData.sections.forEach(function(s, x) { s.order = x; });
    renderCanvas();
    toastr.success('Section duplicated');
}

function deleteSection(i) {
    if (!confirm('Delete this section and all its components?')) return;
    pageData.sections.splice(i, 1);
    pageData.sections.forEach(function(s, x) { s.order = x; });
    renderCanvas();
    showPageSettings();
    toastr.success('Section deleted');
}

function editComponentById(id) {
    for (var i = 0; i < pageData.sections.length; i++) {
        var ci = pageData.sections[i].components.findIndex(function(c) { return c.id == id; });
        if (ci !== -1) {
            showComponentProperties(pageData.sections[i].components[ci], i, ci);
            return;
        }
    }
}

function deleteComponentById(id) {
    for (var i = 0; i < pageData.sections.length; i++) {
        var ci = pageData.sections[i].components.findIndex(function(c) { return c.id == id; });
        if (ci !== -1) {
            pageData.sections[i].components.splice(ci, 1);
            pageData.sections[i].components.forEach(function(c, x) { c.order = x; });
            renderCanvas();
            toastr.success('Component deleted');
            return;
        }
    }
}

function updatePageSetting(key, val) {
    if (!pageData.settings) pageData.settings = {};
    pageData.settings[key] = val;
}

function clearPickerSelection() {
    selectedPickerItems = [];
    jQuery('#dataPickerGrid .picker-item').removeClass('selected');
    updatePickerSelectedBar();
}

// Wait for jQuery to be ready
jQuery(document).ready(function($) {
    // ══════════════════════════════════════════════════════════════════════
    // INIT
    // ══════════════════════════════════════════════════════════════════════
    renderCanvas();
    initSidebarTabs();
    initDragDrop();
    initDeviceSwitcher();
    initGlobalEvents();
    initSavePublish();
    loadPickerFilters();

// ══════════════════════════════════════════════════════════════════════
// CANVAS RENDERING (live preview)
// ══════════════════════════════════════════════════════════════════════
function renderCanvas() {
    var c = jQuery('#sections-container').empty();
    
    // Destroy existing Sortable instances to prevent duplicates
    sortableInstances.forEach(function(instance) {
        if (instance && instance.destroy) instance.destroy();
    });
    sortableInstances = [];
    
    if (!pageData.sections || !pageData.sections.length) { 
        jQuery('#canvas-empty').show(); 
        return; 
    }
    jQuery('#canvas-empty').hide();
    pageData.sections.forEach(function(s, i) { c.append(buildSectionHtml(s, i)); });
    
    // Re-initialize sortables after DOM update
    setTimeout(function() {
        initSortables();
    }, 50);
}

function buildSectionHtml(section, idx) {
    const info = SECTION_TYPES[section.section_type] || {name:'Section'};
    const st = section.settings || {};
    const sty = section.style || {};
    let inlineStyle = buildInlineStyle(st, sty);
    let compsHtml = '';
    if (section.components && section.components.length) {
        section.components.forEach((c, ci) => { compsHtml += buildComponentHtml(c, ci); });
    } else {
        compsHtml = '<div class="text-center text-muted py-3" style="font-size:11px">Drop components here</div>';
    }
    return `<div class="canvas-section" data-section-id="${section.id}" data-index="${idx}" style="${inlineStyle}">
        <div class="section-toolbar">
            <button onclick="moveSection(${idx},-1)" title="Up"><i class="tio-arrow-up"></i></button>
            <button onclick="moveSection(${idx},1)" title="Down"><i class="tio-arrow-down"></i></button>
            <button onclick="duplicateSection(${idx})" title="Copy"><i class="tio-copy"></i></button>
            <button onclick="deleteSection(${idx})" title="Delete"><i class="tio-delete"></i></button>
        </div>
        <span class="section-label"><i class="tio-drag drag-icon"></i>${info.name}</span>
        <div class="section-components" data-section-index="${idx}">${compsHtml}</div>
    </div>`;
}

function buildComponentHtml(comp, idx) {
    const st = comp.settings || {};
    const sty = comp.style || {};
    let inlineStyle = buildInlineStyle(st, sty);
    return `<div class="canvas-component" data-component-id="${comp.id}" data-index="${idx}" style="${inlineStyle}">
        <div class="component-toolbar">
            <button onclick="editComponentById('${comp.id}')" title="Edit"><i class="tio-edit"></i></button>
            <button onclick="deleteComponentById('${comp.id}')" title="Delete"><i class="tio-delete"></i></button>
        </div>
        ${getPreview(comp)}
    </div>`;
}

function buildInlineStyle(settings, style) {
    let s = '';
    if (settings.background_color && settings.background_color !== 'transparent') s += `background-color:${settings.background_color};`;
    if (settings.background_image) s += `background-image:url('${settings.background_image}');background-size:cover;background-position:center;`;
    if (settings.padding_top != null) s += `padding-top:${settings.padding_top}px;`;
    if (settings.padding_bottom != null) s += `padding-bottom:${settings.padding_bottom}px;`;
    if (settings.padding_left != null) s += `padding-left:${settings.padding_left}px;`;
    if (settings.padding_right != null) s += `padding-right:${settings.padding_right}px;`;
    if (settings.margin_top) s += `margin-top:${settings.margin_top}px;`;
    if (settings.margin_bottom) s += `margin-bottom:${settings.margin_bottom}px;`;
    if (settings.border_radius) s += `border-radius:${settings.border_radius}px;`;
    if (settings.opacity != null && settings.opacity !== 1) s += `opacity:${settings.opacity};`;
    if (style) {
        if (style.position && style.position !== 'static') s += `position:${style.position};`;
        if (style.top) s += `top:${style.top};`;
        if (style.left) s += `left:${style.left};`;
        if (style.right) s += `right:${style.right};`;
        if (style.bottom) s += `bottom:${style.bottom};`;
        if (style.z_index) s += `z-index:${style.z_index};`;
        if (style.text_align) s += `text-align:${style.text_align};`;
        if (style.display) s += `display:${style.display};`;
        if (style.flex_direction) s += `flex-direction:${style.flex_direction};`;
        if (style.justify_content) s += `justify-content:${style.justify_content};`;
        if (style.align_items) s += `align-items:${style.align_items};`;
        if (style.overflow) s += `overflow:${style.overflow};`;
        if (style.border) s += `border:${style.border};`;
        if (style.box_shadow) s += `box-shadow:${style.box_shadow};`;
        if (style.min_height) s += `min-height:${style.min_height};`;
        if (style.max_width) s += `max-width:${style.max_width};`;
        if (style.width) s += `width:${style.width};`;
        if (style.height) s += `height:${style.height};`;
    }
    if (settings.animation) s += `animation:${settings.animation} ${settings.animation_duration || '0.5s'} ${settings.animation_timing || 'ease'};`;
    return s;
}

// ══════════════════════════════════════════════════════════════════════
// COMPONENT PREVIEWS (live)
// ══════════════════════════════════════════════════════════════════════
function getPreview(comp) {
    const c = comp.content || {};
    const s = comp.settings || {};
    switch (comp.component_type) {
        case 'text':
            return `<div class="preview-text" style="color:${s.color||'#333'};font-size:${s.font_size||14}px;text-align:${s.text_align||'left'};line-height:${s.line_height||1.5}">${c.text||'Text content...'}</div>`;
        case 'heading':
            return `<div class="preview-heading ${c.level||'h2'}" style="color:${s.color||'#1a1a1a'};text-align:${s.text_align||'left'}">${c.text||'Heading'}</div>`;
        case 'image':
            if (c.url) return `<div class="preview-image" style="border-radius:${s.border_radius||8}px"><img src="${c.url}" style="height:${s.height?s.height+'px':'auto'};object-fit:${s.object_fit||'cover'}"></div>`;
            return `<div class="preview-image" style="height:${s.height||120}px;border-radius:${s.border_radius||8}px"><i class="tio-image" style="font-size:28px"></i></div>`;
        case 'button':
            return `<div style="text-align:${s.text_align||'left'}"><span class="preview-button" style="background:${s.background_color||'#FC6A57'};color:${s.text_color||'#fff'};border-radius:${s.border_radius||8}px;padding:${s.padding_y||10}px ${s.padding_x||20}px;font-size:${s.font_size||13}px;${s.full_width?'display:block;':''}">${c.text||'Button'}</span></div>`;
        case 'product_card':
        case 'product_list':
            return buildProductCardsPreview(comp);
        case 'restaurant_card':
        case 'restaurant_list':
            return buildRestaurantCardsPreview(comp);
        case 'spacer':
            return `<div class="preview-spacer" style="height:${s.height||24}px"></div>`;
        case 'divider':
            return `<div class="preview-divider" style="border-color:${s.color||'#e0e0e0'};border-width:${s.thickness||1}px;border-style:${s.style||'solid'};margin:${s.margin_y||8}px 0"></div>`;
        case 'restaurant_foods':
            return buildRestoFoodsPreview(comp);
        case 'tabs':
            return buildTabsPreview(comp);
        default:
            return `<div class="text-muted text-center py-2" style="font-size:11px">[${comp.component_type}]</div>`;
    }
}

function buildProductCardsPreview(comp) {
    const c = comp.content || {};
    const s = comp.settings || {};
    const cols = s.columns || 2;
    const ids = c.product_ids || [];
    const cached = c._cached_products || [];
    let title = c.title ? `<div style="font-size:14px;font-weight:700;margin-bottom:8px">${c.title}</div>` : '';
    let cards = '';
    if (cached.length) {
        cached.forEach(p => {
            cards += `<div class="preview-product-card" style="background:${s.card_bg_color||'#fff'}">
                ${s.show_image!==false?`<div class="card-img"><img src="${p.image_full_url||''}" onerror="this.src='${DEFAULT_IMG}'"></div>`:''}
                <div class="card-body">
                    ${s.show_name!==false?`<div class="card-title" style="color:${s.name_color||'#1a1a1a'}">${p.name}</div>`:''}
                    ${s.show_restaurant!==false?`<div class="card-subtitle">${p.restaurant_name||''}</div>`:''}
                    ${s.show_description?`<div class="card-desc">${p.description||''}</div>`:''}
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        ${s.show_price!==false?`<div class="card-price" style="color:${s.price_color||'#FC6A57'}">ETB ${Number(p.price||0).toFixed(2)}</div>`:''}
                        ${s.show_rating?`<div class="card-rating">⭐ ${p.avg_rating||'0'}</div>`:''}
                    </div>
                </div>
            </div>`;
        });
    } else {
        for (let i = 0; i < Math.min(ids.length || 2, 4); i++) {
            cards += `<div class="preview-product-card"><div class="card-img"><i class="tio-restaurant" style="font-size:20px;color:#ddd"></i></div><div class="card-body"><div class="card-title">Product</div><div class="card-price">ETB 0.00</div></div></div>`;
        }
    }
    return `${title}<div class="preview-grid cols-${cols}">${cards}</div>`;
}

function buildRestaurantCardsPreview(comp) {
    const c = comp.content || {};
    const s = comp.settings || {};
    const cached = c._cached_restaurants || [];
    let title = c.title ? `<div style="font-size:14px;font-weight:700;margin-bottom:8px">${c.title}</div>` : '';
    let cards = '';
    if (cached.length) {
        cached.forEach(r => {
            cards += `<div class="preview-restaurant-card" style="background:${s.card_bg_color||'#fff'}">
                ${s.show_image!==false?`<div class="card-img"><img src="${r.logo_full_url||''}" onerror="this.src='${DEFAULT_IMG}'"></div>`:''}
                <div class="card-body">
                    ${s.show_name!==false?`<div class="card-title" style="color:${s.name_color||'#1a1a1a'}">${r.name}</div>`:''}
                    <div class="card-meta">
                        ${s.show_rating!==false?`<span style="color:#FFB800">⭐ ${r.rating||'0'}</span>`:''} 
                        ${s.show_address?`<span>${r.address||''}</span>`:''}
                    </div>
                </div>
            </div>`;
        });
    } else {
        cards = `<div class="preview-restaurant-card"><div class="card-img"><i class="tio-shop" style="font-size:20px;color:#ddd"></i></div><div class="card-body"><div class="card-title">Restaurant</div><div class="card-meta">⭐ 4.5</div></div></div>`;
    }
    return `${title}<div class="preview-grid cols-${s.columns||2}">${cards}</div>`;
}

function buildRestoFoodsPreview(comp) {
    const c = comp.content || {};
    const s = comp.settings || {};
    const rName = c.restaurant_name || 'Select a Restaurant';
    const rLogo = c.restaurant_logo || '';
    let foods = '';
    const cached = c._cached_foods || [];
    if (cached.length) {
        cached.forEach(f => {
            foods += `<div class="food-mini"><div class="food-mini-img"><img src="${f.image_full_url||''}" onerror="this.src='${DEFAULT_IMG}'"></div><div class="food-mini-body"><div class="food-mini-name">${f.name}</div>${s.show_food_price!==false?`<div class="food-mini-price">ETB ${Number(f.price||0).toFixed(2)}</div>`:''}</div></div>`;
        });
    } else {
        for (let i = 0; i < 3; i++) foods += `<div class="food-mini"><div class="food-mini-img"></div><div class="food-mini-body"><div class="food-mini-name">Food item</div><div class="food-mini-price">ETB 0.00</div></div></div>`;
    }
    return `<div class="preview-resto-foods" style="background:${s.card_bg_color||'#fff'}">
        <div class="resto-header">
            <div class="resto-logo">${rLogo?`<img src="${rLogo}" onerror="this.src='${DEFAULT_IMG}'">`:'<i class="tio-shop" style="font-size:20px;color:#ddd;margin:12px"></i>'}</div>
            <div class="resto-info"><div class="resto-name" style="color:${s.name_color||'#1a1a1a'}">${rName}</div><div class="resto-meta">${s.show_restaurant_rating!==false?'⭐ '+(c.restaurant_rating||'4.5'):''}</div></div>
        </div>
        <div class="food-scroll">${foods}</div>
    </div>`;
}

function buildTabsPreview(comp) {
    const s = comp.settings || {};
    const labels = s.tab_labels || ['Tab 1', 'Tab 2'];
    let tabs = '';
    labels.forEach((l, i) => { tabs += `<div class="tab-btn ${i===0?'active':''}" style="${i===0?'color:'+( s.tab_active_text_color||'#FC6A57')+';border-bottom-color:'+(s.tab_active_color||'#FC6A57'):'color:'+(s.tab_text_color||'#888')}">${l}</div>`; });
    return `<div class="preview-tabs"><div class="tab-nav" style="background:${s.tab_bg_color||'#fff'}">${tabs}</div><div class="tab-content">Tab content goes here. Add components inside.</div></div>`;
}

// ══════════════════════════════════════════════════════════════════════
// PROPERTIES PANEL - Advanced WordPress-like editing
// ══════════════════════════════════════════════════════════════════════
function showPageSettings() {
    $('.properties-header h5').text('Page Settings');
    $('#properties-body').html($('#page-settings-default').prop('outerHTML'));
}

function showSectionProperties(sectionIdx) {
    const section = pageData.sections[sectionIdx];
    if (!section) return;
    const s = section.settings || {};
    const sty = section.style || {};
    $('.properties-header h5').text('Section: ' + (section.name || section.section_type));
    
    let html = buildSpacingGroup('Section Spacing', s) + buildBackgroundGroup(s) + buildPositioningGroup(sty) + buildAnimationGroup(s) + buildBorderGroup(s, sty) + buildVisibilityGroup(section);
    
    // Section-type specific settings
    if (['products_grid','products_carousel'].includes(section.section_type)) {
        html += buildCardDisplayGroup(s, 'Product');
    }
    if (['restaurants_grid','restaurants_carousel'].includes(section.section_type)) {
        html += buildCardDisplayGroup(s, 'Restaurant');
    }
    if (section.section_type === 'tabs') {
        html += buildTabsSettingsGroup(s);
    }
    if (section.section_type === 'restaurant_foods') {
        html += buildRestoFoodsSettingsGroup(s, section);
    }

    html += `<button class="btn btn-primary btn-sm btn-block mt-2" onclick="applySectionChanges(${sectionIdx})"><i class="tio-checkmark-circle mr-1"></i>Apply</button>`;
    $('#properties-body').html(html);
}

function showComponentProperties(comp, sectionIdx, compIdx) {
    const c = comp.content || {};
    const s = comp.settings || {};
    const sty = comp.style || {};
    
    $('.properties-header h5').text(comp.component_type.replace(/_/g,' ').replace(/\b\w/g,l=>l.toUpperCase()));
    
    let html = buildContentGroup(comp, sectionIdx, compIdx);
    html += buildStyleGroup(s);
    html += buildSpacingGroup('Spacing', s);
    html += buildBackgroundGroup(s);
    html += buildPositioningGroup(sty);
    html += buildBorderGroup(s, sty);
    html += buildAnimationGroup(s);
    
    if (['product_card','product_list'].includes(comp.component_type)) {
        html += buildProductCardOptionsGroup(s);
    }
    if (['restaurant_card','restaurant_list'].includes(comp.component_type)) {
        html += buildRestaurantCardOptionsGroup(s);
    }
    
    html += buildActionGroup(comp);
    html += `<button class="btn btn-primary btn-sm btn-block mt-2" onclick="applyComponentChanges(${sectionIdx},${compIdx})"><i class="tio-checkmark-circle mr-1"></i>Apply</button>`;
    $('#properties-body').html(html);
}

// --- Property Group Builders ---
function pg(title, content, collapsed) {
    return `<div class="prop-group"><div class="prop-group-title ${collapsed?'collapsed':''}" onclick="this.classList.toggle('collapsed')">${title} <i class="tio-chevron-down"></i></div><div class="prop-group-content">${content}</div></div>`;
}
function pr(label, input) { return `<div class="prop-row"><div class="prop-label">${label}</div>${input}</div>`; }
function colorPicker(id, val) { return `<div class="color-input-wrap"><input type="color" id="${id}" value="${val||'#ffffff'}"><input type="text" class="form-control prop-input" value="${val||'#ffffff'}" onchange="document.getElementById('${id}').value=this.value" id="${id}_hex"></div>`; }
function toggle(id, checked, label) { return `<div class="prop-toggle"><span class="prop-label mb-0">${label}</span><label class="switch"><input type="checkbox" id="${id}" ${checked?'checked':''}><span class="slider"></span></label></div>`; }
function numInput(id, val, min, max, step) { return `<input type="number" class="form-control prop-input" id="${id}" value="${val}" min="${min||0}" max="${max||999}" step="${step||1}">`; }

function buildSpacingGroup(title, s) {
    return pg(title, `
        <div class="prop-label" style="font-size:10px;margin-bottom:6px">Padding (px)</div>
        <div class="prop-inline">
            <div class="prop-inline-item"><label>T</label><input class="form-control prop-input" id="sp_pt" type="number" value="${s.padding_top??16}" min="0"></div>
            <div class="prop-inline-item"><label>R</label><input class="form-control prop-input" id="sp_pr" type="number" value="${s.padding_right??16}" min="0"></div>
            <div class="prop-inline-item"><label>B</label><input class="form-control prop-input" id="sp_pb" type="number" value="${s.padding_bottom??16}" min="0"></div>
            <div class="prop-inline-item"><label>L</label><input class="form-control prop-input" id="sp_pl" type="number" value="${s.padding_left??16}" min="0"></div>
        </div>
        <div class="prop-label mt-2" style="font-size:10px;margin-bottom:6px">Margin (px)</div>
        <div class="prop-inline">
            <div class="prop-inline-item"><label>T</label><input class="form-control prop-input" id="sp_mt" type="number" value="${s.margin_top??0}"></div>
            <div class="prop-inline-item"><label>B</label><input class="form-control prop-input" id="sp_mb" type="number" value="${s.margin_bottom??0}"></div>
        </div>
    `, true);
}

function buildBackgroundGroup(s) {
    const imgPreview = s.background_image ? `<img src="${s.background_image}" style="max-height:60px;border-radius:4px;margin-bottom:6px">` : '';
    return pg('Background', `
        ${pr('Color', colorPicker('bg_color', s.background_color || 'transparent'))}
        ${pr('Image', `<div class="image-upload-area" onclick="document.getElementById('bg_img_file').click()">
            ${imgPreview || '<div class="upload-placeholder"><i class="tio-cloud-upload"></i><div style="font-size:10px">Click to upload</div></div>'}
            <input type="file" id="bg_img_file" accept="image/*" onchange="uploadImage(this, 'bg_img_url')">
        </div>
        <input type="text" class="form-control prop-input mt-1" id="bg_img_url" value="${s.background_image||''}" placeholder="Or paste URL">`)}
        ${pr('Opacity', numInput('bg_opacity', s.opacity ?? 1, 0, 1, 0.1))}
    `, true);
}

function buildPositioningGroup(sty) {
    return pg('Position & Layout', `
        ${pr('Position', `<select class="form-control prop-input" id="sty_position">
            <option value="static" ${(sty.position||'static')==='static'?'selected':''}>Static</option>
            <option value="relative" ${sty.position==='relative'?'selected':''}>Relative</option>
            <option value="absolute" ${sty.position==='absolute'?'selected':''}>Absolute</option>
            <option value="fixed" ${sty.position==='fixed'?'selected':''}>Fixed</option>
            <option value="sticky" ${sty.position==='sticky'?'selected':''}>Sticky</option>
        </select>`)}
        <div class="prop-inline">
            <div class="prop-inline-item"><label>Top</label><input class="form-control prop-input" id="sty_top" value="${sty.top||''}"></div>
            <div class="prop-inline-item"><label>Right</label><input class="form-control prop-input" id="sty_right" value="${sty.right||''}"></div>
            <div class="prop-inline-item"><label>Bottom</label><input class="form-control prop-input" id="sty_bottom" value="${sty.bottom||''}"></div>
            <div class="prop-inline-item"><label>Left</label><input class="form-control prop-input" id="sty_left" value="${sty.left||''}"></div>
        </div>
        ${pr('Z-Index', `<input class="form-control prop-input" id="sty_zindex" type="number" value="${sty.z_index||''}">`)}
        ${pr('Text Align', `<select class="form-control prop-input" id="sty_text_align">
            <option value="">Default</option>
            <option value="left" ${sty.text_align==='left'?'selected':''}>Left</option>
            <option value="center" ${sty.text_align==='center'?'selected':''}>Center</option>
            <option value="right" ${sty.text_align==='right'?'selected':''}>Right</option>
        </select>`)}
        ${pr('Display', `<select class="form-control prop-input" id="sty_display">
            <option value="">Default</option>
            <option value="block" ${sty.display==='block'?'selected':''}>Block</option>
            <option value="flex" ${sty.display==='flex'?'selected':''}>Flex</option>
            <option value="inline-block" ${sty.display==='inline-block'?'selected':''}>Inline Block</option>
            <option value="none" ${sty.display==='none'?'selected':''}>None</option>
        </select>`)}
        ${pr('Width', `<input class="form-control prop-input" id="sty_width" value="${sty.width||''}" placeholder="e.g. 100% or 200px">`)}
        ${pr('Height', `<input class="form-control prop-input" id="sty_height" value="${sty.height||''}" placeholder="e.g. auto or 200px">`)}
    `, true);
}

function buildAnimationGroup(s) {
    return pg('Animation', `
        ${pr('Type', `<select class="form-control prop-input" id="anim_type">
            <option value="" ${!s.animation?'selected':''}>None</option>
            <option value="fadeIn" ${s.animation==='fadeIn'?'selected':''}>Fade In</option>
            <option value="slideInUp" ${s.animation==='slideInUp'?'selected':''}>Slide Up</option>
            <option value="slideInLeft" ${s.animation==='slideInLeft'?'selected':''}>Slide Left</option>
            <option value="slideInRight" ${s.animation==='slideInRight'?'selected':''}>Slide Right</option>
            <option value="zoomIn" ${s.animation==='zoomIn'?'selected':''}>Zoom In</option>
            <option value="bounceIn" ${s.animation==='bounceIn'?'selected':''}>Bounce In</option>
            <option value="pulse" ${s.animation==='pulse'?'selected':''}>Pulse</option>
        </select>`)}
        ${pr('Duration', `<input class="form-control prop-input" id="anim_duration" value="${s.animation_duration||'0.5s'}">`)}
    `, true);
}

function buildBorderGroup(s, sty) {
    return pg('Border & Shadow', `
        ${pr('Border Radius', numInput('brd_radius', s.border_radius||0, 0, 100))}
        ${pr('Border', `<input class="form-control prop-input" id="sty_border" value="${sty.border||''}" placeholder="e.g. 1px solid #ddd">`)}
        ${pr('Box Shadow', `<input class="form-control prop-input" id="sty_shadow" value="${sty.box_shadow||''}" placeholder="e.g. 0 2px 8px rgba(0,0,0,0.1)">`)}
    `, true);
}

function buildVisibilityGroup(item) {
    return pg('Visibility', `<div class="prop-row">${toggle('vis_toggle', item.is_visible !== false, 'Visible')}</div>`, true);
}

function buildStyleGroup(s) {
    return pg('Typography', `
        ${pr('Font Size', numInput('font_size', s.font_size||14, 8, 72))}
        ${pr('Color', colorPicker('text_color', s.color || '#333333'))}
        ${pr('Line Height', `<input class="form-control prop-input" id="line_height" type="number" value="${s.line_height||1.5}" step="0.1" min="0.5" max="4">`)}
        ${pr('Font Weight', `<select class="form-control prop-input" id="font_weight">
            <option value="">Default</option>
            <option value="400" ${s.font_weight==='400'?'selected':''}>Normal (400)</option>
            <option value="500" ${s.font_weight==='500'?'selected':''}>Medium (500)</option>
            <option value="600" ${s.font_weight==='600'?'selected':''}>Semi Bold (600)</option>
            <option value="700" ${s.font_weight==='700'?'selected':''}>Bold (700)</option>
            <option value="800" ${s.font_weight==='800'?'selected':''}>Extra Bold (800)</option>
        </select>`)}
    `, true);
}

function buildContentGroup(comp, si, ci) {
    const c = comp.content || {};
    const s = comp.settings || {};
    let inner = '';
    switch (comp.component_type) {
        case 'text':
            inner = pr('Text', `<textarea class="form-control prop-input" id="ct_text" rows="3">${c.text||''}</textarea>`);
            break;
        case 'heading':
            inner = pr('Text', `<input class="form-control prop-input" id="ct_text" value="${c.text||''}">`) +
                pr('Level', `<select class="form-control prop-input" id="ct_level"><option value="h1" ${c.level==='h1'?'selected':''}>H1</option><option value="h2" ${c.level==='h2'?'selected':''}>H2</option><option value="h3" ${c.level==='h3'?'selected':''}>H3</option></select>`);
            break;
        case 'image':
            inner = pr('Image', `<div class="image-upload-area" onclick="document.getElementById('ct_img_file').click()">
                ${c.url?`<img src="${c.url}">`:'<div class="upload-placeholder"><i class="tio-cloud-upload"></i><div style="font-size:10px">Upload image</div></div>'}
                <input type="file" id="ct_img_file" accept="image/*" onchange="uploadImage(this, 'ct_img_url')">
            </div><input type="text" class="form-control prop-input mt-1" id="ct_img_url" value="${c.url||''}" placeholder="Or paste URL">`) +
                pr('Alt Text', `<input class="form-control prop-input" id="ct_alt" value="${c.alt||''}">`) +
                pr('Height', numInput('ct_img_h', s.height||'', 0, 1000)) +
                pr('Object Fit', `<select class="form-control prop-input" id="ct_obj_fit"><option value="cover" ${(s.object_fit||'cover')==='cover'?'selected':''}>Cover</option><option value="contain" ${s.object_fit==='contain'?'selected':''}>Contain</option><option value="fill" ${s.object_fit==='fill'?'selected':''}>Fill</option></select>`);
            break;
        case 'button':
            inner = pr('Text', `<input class="form-control prop-input" id="ct_text" value="${c.text||'Button'}">`) +
                pr('Background', colorPicker('btn_bg', s.background_color||'#FC6A57')) +
                pr('Text Color', colorPicker('btn_color', s.text_color||'#ffffff')) +
                pr('Border Radius', numInput('btn_radius', s.border_radius||8, 0, 50)) +
                pr('Font Size', numInput('btn_fsize', s.font_size||13, 8, 36)) +
                `<div class="prop-row">${toggle('btn_full', s.full_width||false, 'Full Width')}</div>`;
            break;
        case 'product_card': case 'product_list':
            inner = pr('Title', `<input class="form-control prop-input" id="ct_title" value="${c.title||''}">`) +
                pr('Products', `<button class="btn btn-sm btn-outline-primary btn-block" onclick="openDataPicker('products',${si},${ci})"><i class="tio-add mr-1"></i>Select Products (${(c.product_ids||[]).length})</button>`) +
                pr('Columns', `<select class="form-control prop-input" id="ct_cols"><option value="1" ${(s.columns||2)==1?'selected':''}>1</option><option value="2" ${(s.columns||2)==2?'selected':''}>2</option><option value="3" ${s.columns==3?'selected':''}>3</option></select>`);
            break;
        case 'restaurant_card': case 'restaurant_list':
            inner = pr('Title', `<input class="form-control prop-input" id="ct_title" value="${c.title||''}">`) +
                pr('Restaurants', `<button class="btn btn-sm btn-outline-primary btn-block" onclick="openDataPicker('restaurants',${si},${ci})"><i class="tio-add mr-1"></i>Select Restaurants (${(c.restaurant_ids||[]).length})</button>`) +
                pr('Columns', `<select class="form-control prop-input" id="ct_cols"><option value="1" ${(s.columns||2)==1?'selected':''}>1</option><option value="2" ${(s.columns||2)==2?'selected':''}>2</option><option value="3" ${s.columns==3?'selected':''}>3</option></select>`);
            break;
        case 'spacer':
            inner = pr('Height (px)', numInput('ct_height', s.height||24, 4, 400));
            break;
        case 'divider':
            inner = pr('Color', colorPicker('div_color', s.color||'#e0e0e0')) +
                pr('Thickness', numInput('div_thick', s.thickness||1, 1, 10)) +
                pr('Style', `<select class="form-control prop-input" id="div_style"><option value="solid" ${(s.style||'solid')==='solid'?'selected':''}>Solid</option><option value="dashed" ${s.style==='dashed'?'selected':''}>Dashed</option><option value="dotted" ${s.style==='dotted'?'selected':''}>Dotted</option></select>`);
            break;
    }
    return pg('Content', inner, false);
}

function buildProductCardOptionsGroup(s) {
    return pg('Card Display Options', `
        <div class="prop-row">${toggle('pc_show_image', s.show_image!==false, 'Show Image')}</div>
        <div class="prop-row">${toggle('pc_show_name', s.show_name!==false, 'Show Name')}</div>
        <div class="prop-row">${toggle('pc_show_price', s.show_price!==false, 'Show Price')}</div>
        <div class="prop-row">${toggle('pc_show_restaurant', s.show_restaurant!==false, 'Show Restaurant')}</div>
        <div class="prop-row">${toggle('pc_show_rating', s.show_rating||false, 'Show Rating')}</div>
        <div class="prop-row">${toggle('pc_show_desc', s.show_description||false, 'Show Description')}</div>
        ${pr('Card Background', colorPicker('pc_card_bg', s.card_bg_color||'#ffffff'))}
        ${pr('Name Color', colorPicker('pc_name_color', s.name_color||'#1a1a1a'))}
        ${pr('Price Color', colorPicker('pc_price_color', s.price_color||'#FC6A57'))}
    `, false);
}

function buildRestaurantCardOptionsGroup(s) {
    return pg('Card Display Options', `
        <div class="prop-row">${toggle('rc_show_image', s.show_image!==false, 'Show Image')}</div>
        <div class="prop-row">${toggle('rc_show_name', s.show_name!==false, 'Show Name')}</div>
        <div class="prop-row">${toggle('rc_show_rating', s.show_rating!==false, 'Show Rating')}</div>
        <div class="prop-row">${toggle('rc_show_address', s.show_address||false, 'Show Address')}</div>
        <div class="prop-row">${toggle('rc_show_delivery', s.show_delivery_time||false, 'Show Delivery Time')}</div>
        ${pr('Card Background', colorPicker('rc_card_bg', s.card_bg_color||'#ffffff'))}
        ${pr('Name Color', colorPicker('rc_name_color', s.name_color||'#1a1a1a'))}
    `, false);
}

function buildTabsSettingsGroup(s) {
    const labels = (s.tab_labels||['Tab 1','Tab 2']).join('\n');
    return pg('Tab Settings', `
        ${pr('Tab Labels (one per line)', `<textarea class="form-control prop-input" id="tab_labels" rows="3">${labels}</textarea>`)}
        ${pr('Active Tab Color', colorPicker('tab_active_color', s.tab_active_color||'#FC6A57'))}
        ${pr('Tab Text Color', colorPicker('tab_text_color', s.tab_text_color||'#333333'))}
        ${pr('Tab Background', colorPicker('tab_bg_color', s.tab_bg_color||'#ffffff'))}
        ${pr('Border Radius', numInput('tab_radius', s.tab_border_radius||8, 0, 30))}
    `, false);
}

function buildRestoFoodsSettingsGroup(s, section) {
    return pg('Restaurant + Foods Settings', `
        ${pr('Restaurant', `<button class="btn btn-sm btn-outline-primary btn-block" onclick="openRestoFoodsPicker(${pageData.sections.indexOf(section)})"><i class="tio-add mr-1"></i>Select Restaurant${s.restaurant_id?' ('+s.restaurant_id+')':''}</button>`)}
        <div class="prop-row">${toggle('rf_show_logo', s.show_restaurant_logo!==false, 'Show Logo')}</div>
        <div class="prop-row">${toggle('rf_show_name', s.show_restaurant_name!==false, 'Show Name')}</div>
        <div class="prop-row">${toggle('rf_show_rating', s.show_restaurant_rating!==false, 'Show Rating')}</div>
        ${pr('Food Display', `<select class="form-control prop-input" id="rf_mode"><option value="auto" ${(s.food_selection||'auto')==='auto'?'selected':''}>Auto (all from restaurant)</option><option value="selected" ${s.food_selection==='selected'?'selected':''}>Selected products only</option></select>`)}
        ${pr('Max Foods', numInput('rf_count', s.food_count||10, 1, 50))}
        <div class="prop-row">${toggle('rf_show_food_price', s.show_food_price!==false, 'Show Food Price')}</div>
        <div class="prop-row">${toggle('rf_show_food_name', s.show_food_name!==false, 'Show Food Name')}</div>
        ${pr('Card Background', colorPicker('rf_card_bg', s.card_bg_color||'#ffffff'))}
        ${pr('Name Color', colorPicker('rf_name_color', s.name_color||'#1a1a1a'))}
    `, false);
}

function buildActionGroup(comp) {
    const a = comp.action || {};
    return pg('Click Action', `
        ${pr('Action Type', `<select class="form-control prop-input" id="act_type">
            <option value="" ${!a.type?'selected':''}>None</option>
            <option value="navigate_product" ${a.type==='navigate_product'?'selected':''}>Go to Product</option>
            <option value="navigate_restaurant" ${a.type==='navigate_restaurant'?'selected':''}>Go to Restaurant</option>
            <option value="navigate_category" ${a.type==='navigate_category'?'selected':''}>Go to Category</option>
            <option value="open_url" ${a.type==='open_url'?'selected':''}>Open URL</option>
            <option value="open_search" ${a.type==='open_search'?'selected':''}>Open Search</option>
        </select>`)}
        ${pr('URL / ID', `<input class="form-control prop-input" id="act_value" value="${a.url||a.product_id||a.restaurant_id||a.category_id||''}">`)}
    `, true);
}

// ══════════════════════════════════════════════════════════════════════
// APPLY CHANGES (reads from property inputs, updates pageData, re-renders)
// ══════════════════════════════════════════════════════════════════════
function readSpacing() {
    return { padding_top: +$('#sp_pt').val()||0, padding_right: +$('#sp_pr').val()||0, padding_bottom: +$('#sp_pb').val()||0, padding_left: +$('#sp_pl').val()||0, margin_top: +$('#sp_mt').val()||0, margin_bottom: +$('#sp_mb').val()||0 };
}
function readBackground() {
    return { background_color: $('#bg_color').val()||'transparent', background_image: $('#bg_img_url').val()||null, opacity: +$('#bg_opacity').val()||1 };
}
function readPositioning() {
    return { position: $('#sty_position').val()||'static', top: $('#sty_top').val()||'', right: $('#sty_right').val()||'', bottom: $('#sty_bottom').val()||'', left: $('#sty_left').val()||'', z_index: $('#sty_zindex').val()||'', text_align: $('#sty_text_align').val()||'', display: $('#sty_display').val()||'', width: $('#sty_width').val()||'', height: $('#sty_height').val()||'' };
}
function readBorder() {
    return { border_radius: +$('#brd_radius').val()||0, border: $('#sty_border').val()||'', box_shadow: $('#sty_shadow').val()||'' };
}
function readAnimation() {
    return { animation: $('#anim_type').val()||'', animation_duration: $('#anim_duration').val()||'0.5s' };
}

function applySectionChanges(idx) {
    const section = pageData.sections[idx];
    section.settings = Object.assign(section.settings||{}, readSpacing(), readBackground(), readAnimation(), {border_radius: +$('#brd_radius').val()||0});
    section.style = Object.assign(section.style||{}, readPositioning(), {border:$('#sty_border').val()||'', box_shadow:$('#sty_shadow').val()||''});
    section.is_visible = $('#vis_toggle').is(':checked');
    
    if (section.section_type === 'tabs' && $('#tab_labels').length) {
        section.settings.tab_labels = $('#tab_labels').val().split('\n').filter(l=>l.trim());
        section.settings.tab_active_color = $('#tab_active_color').val();
        section.settings.tab_text_color = $('#tab_text_color').val();
        section.settings.tab_bg_color = $('#tab_bg_color').val();
        section.settings.tab_border_radius = +$('#tab_radius').val()||8;
    }
    if (section.section_type === 'restaurant_foods') {
        section.settings.show_restaurant_logo = $('#rf_show_logo').is(':checked');
        section.settings.show_restaurant_name = $('#rf_show_name').is(':checked');
        section.settings.show_restaurant_rating = $('#rf_show_rating').is(':checked');
        section.settings.food_selection = $('#rf_mode').val();
        section.settings.food_count = +$('#rf_count').val()||10;
        section.settings.show_food_price = $('#rf_show_food_price').is(':checked');
        section.settings.show_food_name = $('#rf_show_food_name').is(':checked');
        section.settings.card_bg_color = $('#rf_card_bg').val();
        section.settings.name_color = $('#rf_name_color').val();
    }
    renderCanvas();
    toastr.success('Section updated');
}

function applyComponentChanges(si, ci) {
    const comp = pageData.sections[si].components[ci];
    if (!comp) return;
    const c = comp.content = comp.content || {};
    const s = comp.settings = comp.settings || {};
    
    // Spacing, background, position, border, animation
    Object.assign(s, readSpacing(), readBackground(), readAnimation(), {border_radius: +$('#brd_radius').val()||0});
    comp.style = Object.assign(comp.style||{}, readPositioning(), {border:$('#sty_border').val()||'', box_shadow:$('#sty_shadow').val()||''});
    
    // Typography
    if ($('#font_size').length) s.font_size = +$('#font_size').val()||14;
    if ($('#text_color').length) s.color = $('#text_color').val();
    if ($('#line_height').length) s.line_height = +$('#line_height').val()||1.5;
    if ($('#font_weight').length) s.font_weight = $('#font_weight').val();
    if ($('#sty_text_align').length) s.text_align = $('#sty_text_align').val();
    
    // Content per type
    switch (comp.component_type) {
        case 'text': case 'heading':
            c.text = $('#ct_text').val();
            if ($('#ct_level').length) c.level = $('#ct_level').val();
            break;
        case 'image':
            c.url = $('#ct_img_url').val();
            c.alt = $('#ct_alt').val();
            s.height = +$('#ct_img_h').val()||'';
            s.object_fit = $('#ct_obj_fit').val();
            break;
        case 'button':
            c.text = $('#ct_text').val();
            s.background_color = $('#btn_bg').val();
            s.text_color = $('#btn_color').val();
            s.border_radius = +$('#btn_radius').val()||8;
            s.font_size = +$('#btn_fsize').val()||13;
            s.full_width = $('#btn_full').is(':checked');
            break;
        case 'product_card': case 'product_list':
            c.title = $('#ct_title').val();
            s.columns = +$('#ct_cols').val()||2;
            s.show_image = $('#pc_show_image').is(':checked');
            s.show_name = $('#pc_show_name').is(':checked');
            s.show_price = $('#pc_show_price').is(':checked');
            s.show_restaurant = $('#pc_show_restaurant').is(':checked');
            s.show_rating = $('#pc_show_rating').is(':checked');
            s.show_description = $('#pc_show_desc').is(':checked');
            s.card_bg_color = $('#pc_card_bg').val();
            s.name_color = $('#pc_name_color').val();
            s.price_color = $('#pc_price_color').val();
            break;
        case 'restaurant_card': case 'restaurant_list':
            c.title = $('#ct_title').val();
            s.columns = +$('#ct_cols').val()||2;
            s.show_image = $('#rc_show_image').is(':checked');
            s.show_name = $('#rc_show_name').is(':checked');
            s.show_rating = $('#rc_show_rating').is(':checked');
            s.show_address = $('#rc_show_address').is(':checked');
            s.show_delivery_time = $('#rc_show_delivery').is(':checked');
            s.card_bg_color = $('#rc_card_bg').val();
            s.name_color = $('#rc_name_color').val();
            break;
        case 'spacer': s.height = +$('#ct_height').val()||24; break;
        case 'divider': s.color = $('#div_color').val(); s.thickness = +$('#div_thick').val()||1; s.style = $('#div_style').val(); break;
    }
    
    // Action
    if ($('#act_type').length) {
        const aType = $('#act_type').val();
        const aVal = $('#act_value').val();
        if (aType) {
            comp.action = {type: aType};
            if (aType === 'open_url') comp.action.url = aVal;
            else if (aType === 'navigate_product') comp.action.product_id = +aVal;
            else if (aType === 'navigate_restaurant') comp.action.restaurant_id = +aVal;
            else if (aType === 'navigate_category') comp.action.category_id = +aVal;
        } else { comp.action = null; }
    }
    
    renderCanvas();
    toastr.success('Component updated');
}

// ══════════════════════════════════════════════════════════════════════
// IMAGE UPLOAD
// ══════════════════════════════════════════════════════════════════════
function uploadImage(input, targetId) {
    if (!input.files || !input.files[0]) return;
    const fd = new FormData();
    fd.append('file', input.files[0]);
    fd.append('_token', CSRF);
    const area = $(input).closest('.image-upload-area');
    area.html('<div style="padding:20px;text-align:center"><i class="tio-loading tio-spin" style="font-size:20px"></i></div>');
    $.ajax({
        url: URLS.upload, type: 'POST', data: fd, processData: false, contentType: false,
        success: function(r) {
            if (r.success) {
                $('#' + targetId).val(r.url);
                area.html(`<img src="${r.url}"><input type="file" accept="image/*" onchange="uploadImage(this,'${targetId}')">`);
                toastr.success('Image uploaded');
            }
        },
        error: function() { area.html('<div class="upload-placeholder"><i class="tio-cloud-upload"></i><div style="font-size:10px">Upload failed</div></div>'); toastr.error('Upload failed'); }
    });
}

// ══════════════════════════════════════════════════════════════════════
// DATA PICKER (with filters)
// ══════════════════════════════════════════════════════════════════════
let currentPickerType = null, currentPickerSI = null, currentPickerCI = null, selectedPickerItems = [];

function loadPickerFilters() {
    $.get(URLS.searchRestaurants, {limit:100}, function(data) {
        let html = '<option value="">All Restaurants</option>';
        data.forEach(r => html += `<option value="${r.id}">${r.name}</option>`);
        $('#pickerFilterRestaurant').html(html);
    });
    $.get(URLS.searchCategories, {limit:100}, function(data) {
        let html = '<option value="">All Categories</option>';
        data.forEach(c => html += `<option value="${c.id}">${c.name}</option>`);
        $('#pickerFilterCategory').html(html);
    });
}

function openDataPicker(type, si, ci) {
    currentPickerType = type; currentPickerSI = si; currentPickerCI = ci;
    const comp = pageData.sections[si].components[ci];
    selectedPickerItems = type === 'products' ? [...(comp.content.product_ids||[])] : [...(comp.content.restaurant_ids||[])];
    $('#dataPickerTitle').text(type === 'products' ? 'Select Products' : 'Select Restaurants');
    $('#pickerFilterRestaurant').toggle(type === 'products');
    $('#pickerFilterCategory').toggle(type === 'products');
    updatePickerSelectedBar();
    $('#dataPickerModal').modal('show');
    loadPickerData();
}

function openRestoFoodsPicker(sectionIdx) {
    currentPickerType = 'restaurants_single'; currentPickerSI = sectionIdx; currentPickerCI = -1;
    selectedPickerItems = [];
    const s = pageData.sections[sectionIdx].settings||{};
    if (s.restaurant_id) selectedPickerItems = [s.restaurant_id];
    $('#dataPickerTitle').text('Select Restaurant');
    $('#pickerFilterRestaurant, #pickerFilterCategory').hide();
    updatePickerSelectedBar();
    $('#dataPickerModal').modal('show');
    loadPickerData();
}

function loadPickerData() {
    let url, params = {search: $('#dataPickerSearch').val()||'', limit: 50};
    if (currentPickerType === 'products') {
        url = URLS.searchProducts;
        params.restaurant_id = $('#pickerFilterRestaurant').val();
        params.category_id = $('#pickerFilterCategory').val();
    } else {
        url = URLS.searchRestaurants;
    }
    $.get(url, params, function(data) {
        let html = '';
        data.forEach(item => {
            const sel = selectedPickerItems.includes(item.id);
            const img = item.image_full_url || item.logo_full_url || '';
            html += `<div class="picker-item ${sel?'selected':''}" data-id="${item.id}" data-name="${item.name}" data-img="${img}" data-rating="${item.rating||item.avg_rating||0}" data-price="${item.price||0}" data-restaurant="${item.restaurant_name||''}" data-logo="${item.logo_full_url||''}">
                <img src="${img}" onerror="this.src='${DEFAULT_IMG}'"><div class="name">${item.name}</div><div class="meta">${item.restaurant_name||item.address||'ETB '+(item.price||'')}</div></div>`;
        });
        $('#dataPickerGrid').html(html || '<p class="text-center text-muted py-3">No items found</p>');
    });
}

function updatePickerSelectedBar() {
    const n = selectedPickerItems.length;
    if (n > 0) { $('#pickerSelectedBar').removeClass('d-none'); $('#pickerSelectedCount').text(n); }
    else { $('#pickerSelectedBar').addClass('d-none'); }
}
function clearPickerSelection() { selectedPickerItems = []; $('#dataPickerGrid .picker-item').removeClass('selected'); updatePickerSelectedBar(); }

$('#dataPickerSearch, #pickerFilterRestaurant, #pickerFilterCategory').on('input change', function() { loadPickerData(); });

$(document).on('click', '.picker-item', function() {
    const id = +$(this).data('id');
    if (currentPickerType === 'restaurants_single') {
        selectedPickerItems = [id];
        $('.picker-item').removeClass('selected');
        $(this).addClass('selected');
    } else {
        $(this).toggleClass('selected');
        if ($(this).hasClass('selected')) { if (!selectedPickerItems.includes(id)) selectedPickerItems.push(id); }
        else { selectedPickerItems = selectedPickerItems.filter(i=>i!==id); }
    }
    updatePickerSelectedBar();
});

$('#dataPickerConfirm').on('click', function() {
    if (currentPickerType === 'restaurants_single') {
        const section = pageData.sections[currentPickerSI];
        const item = $(`#dataPickerGrid .picker-item.selected`).first();
        section.settings.restaurant_id = selectedPickerItems[0];
        section.content = section.content || {};
        section.content.restaurant_name = item.data('name')||'';
        section.content.restaurant_logo = item.data('logo') || item.data('img')||'';
        section.content.restaurant_rating = item.data('rating')||0;
        // Load foods for this restaurant
        $.get(URLS.searchProducts, {restaurant_id: selectedPickerItems[0], limit: 20}, function(foods) {
            section.content._cached_foods = foods;
            renderCanvas();
        });
    } else {
        const comp = pageData.sections[currentPickerSI].components[currentPickerCI];
        if (currentPickerType === 'products') {
            comp.content.product_ids = [...selectedPickerItems];
            // Cache product data for live preview
            $.get(URLS.searchProducts, {limit:50}, function(all) {
                comp.content._cached_products = all.filter(p => selectedPickerItems.includes(p.id));
                renderCanvas();
            });
        } else {
            comp.content.restaurant_ids = [...selectedPickerItems];
            $.get(URLS.searchRestaurants, {limit:50}, function(all) {
                comp.content._cached_restaurants = all.filter(r => selectedPickerItems.includes(r.id));
                renderCanvas();
            });
        }
    }
    $('#dataPickerModal').modal('hide');
    toastr.success('Selection saved');
});

// ══════════════════════════════════════════════════════════════════════
// SORTABLE, DRAG-DROP, SECTION/COMPONENT CRUD
// ══════════════════════════════════════════════════════════════════════
function initSortables() {
    var c = document.getElementById('sections-container');
    if (!c) return;
    
    // Create sortable for sections with proper drag handle
    var sectionSortable = new Sortable(c, {
        animation: 150,
        handle: '.section-label', // Use section label as drag handle
        draggable: '.canvas-section',
        ghostClass: 'sortable-ghost',
        onEnd: function(e) {
            var item = pageData.sections.splice(e.oldIndex, 1)[0];
            pageData.sections.splice(e.newIndex, 0, item);
            pageData.sections.forEach(function(s, x) { s.order = x; });
        }
    });
    sortableInstances.push(sectionSortable);
    
    // Create sortable for components within each section
    document.querySelectorAll('.section-components').forEach(function(el) {
        var compSortable = new Sortable(el, {
            group: 'components',
            animation: 150,
            draggable: '.canvas-component',
            ghostClass: 'sortable-ghost',
            onEnd: function(e) {
                var si = parseInt(e.to.dataset.sectionIndex);
                var sec = pageData.sections[si];
                if (!sec) return;
                var item = sec.components.splice(e.oldIndex, 1)[0];
                sec.components.splice(e.newIndex, 0, item);
                sec.components.forEach(function(c, x) { c.order = x; });
            }
        });
        sortableInstances.push(compSortable);
    });
}

function initSidebarTabs() {
    $('.sidebar-tab').on('click', function() { const t=$(this).data('tab'); $('.sidebar-tab').removeClass('active'); $(this).addClass('active');
        if(t==='sections'){$('#sidebar-sections').removeClass('d-none');$('#sidebar-components').addClass('d-none');}else{$('#sidebar-sections').addClass('d-none');$('#sidebar-components').removeClass('d-none');}
    });
}

function initDragDrop() {
    $('.builder-item').on('dragstart', function(e) { e.originalEvent.dataTransfer.setData('type', $(this).data('type')); e.originalEvent.dataTransfer.setData('subType', $(this).data('section-type')||$(this).data('component-type')); });
    document.getElementById('canvas-body').addEventListener('dragover', e=>{e.preventDefault();e.dataTransfer.dropEffect='copy';});
    document.getElementById('canvas-body').addEventListener('drop', e=>{e.preventDefault();if(e.dataTransfer.getData('type')==='section') addSection(e.dataTransfer.getData('subType'));});
    $(document).on('dragover','.section-components',function(e){e.preventDefault();});
    $(document).on('drop','.section-components',function(e){e.preventDefault();if(e.originalEvent.dataTransfer.getData('type')==='component') addComponent($(this).data('section-index'), e.originalEvent.dataTransfer.getData('subType'));});
}

function addSection(type) {
    pageData.sections.push({id:'new_'+Date.now(), section_type:type, name:SECTION_TYPES[type]?.name||'Section', order:pageData.sections.length, settings:{}, style:{}, components:[], is_visible:true});
    renderCanvas(); toastr.success('Section added');
}

function addComponent(si, type) {
    const sec = pageData.sections[si]; if(!sec) return;
    sec.components.push({id:'new_'+Date.now(), component_type:type, order:sec.components.length, column_span:12, content:{}, settings:{}, style:{}, is_visible:true});
    renderCanvas(); toastr.success('Component added');
}

// Note: moveSection, duplicateSection, deleteSection, editComponentById, deleteComponentById
// are defined globally above the jQuery ready block so they can be called from onclick handlers

// ══════════════════════════════════════════════════════════════════════
// EVENTS (click selection, device switch, save/publish)
// ══════════════════════════════════════════════════════════════════════
function initGlobalEvents() {
    $(document).on('input', 'input[type="color"]', function() { $(this).siblings('input[type="text"]').val($(this).val()); });
    
    $(document).on('click', '.canvas-section', function(e) {
        if($(e.target).closest('.section-toolbar,.canvas-component').length) return;
        $('.canvas-section,.canvas-component').removeClass('selected'); $(this).addClass('selected');
        const idx = $(this).data('index');
        showSectionProperties(idx);
    });
    $(document).on('click', '.canvas-component', function(e) {
        e.stopPropagation();
        if($(e.target).closest('.component-toolbar').length) return;
        $('.canvas-section,.canvas-component').removeClass('selected'); $(this).addClass('selected');
        const id = $(this).data('component-id');
        editComponentById(id);
    });
}

function initDeviceSwitcher() {
    $('.device-btn').on('click', function() { $('.device-btn').removeClass('active'); $(this).addClass('active'); $('#builder-canvas').css('max-width', $(this).data('width')+'px'); });
}

// Note: updatePageSetting is defined globally above

function initSavePublish() {
    $('#save-page-btn').on('click', function() {
        const btn = $(this); btn.prop('disabled',true).html('<i class="tio-loading tio-spin mr-1"></i>Saving...');
        pageData.title = $('#page-title-input').val();
        // Strip cached data before saving
        const cleanData = JSON.parse(JSON.stringify(pageData));
        cleanData.sections.forEach(s => {
            if(s.content) { delete s.content._cached_foods; delete s.content._cached_products; delete s.content._cached_restaurants; }
            s.components.forEach(c => { if(c.content) { delete c.content._cached_products; delete c.content._cached_restaurants; delete c.content._cached_foods; } });
        });
        $.ajax({
            url: URLS.save, type:'POST', data:JSON.stringify(cleanData), contentType:'application/json', headers:{'X-CSRF-TOKEN':CSRF},
            success(r) { btn.prop('disabled',false).html('<i class="tio-save mr-1"></i>Save'); if(r.success){pageData=r.page;renderCanvas();toastr.success(r.message||'Saved!');} },
            error() { btn.prop('disabled',false).html('<i class="tio-save mr-1"></i>Save'); toastr.error('Save failed'); }
        });
    });
    
    $('#publish-btn').on('click', function() {
        const btn=$(this);
        $.post(URLS.publish, {_token:CSRF}, function(r) {
            if(r.is_published) btn.removeClass('btn-warning').addClass('btn-success').html('<i class="tio-checkmark-circle mr-1"></i>Published');
            else btn.removeClass('btn-success').addClass('btn-warning').html('<i class="tio-cloud-upload mr-1"></i>Publish');
            toastr.success(r.message);
        });
    });
});
</script>

<style>
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
@keyframes slideInUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
@keyframes slideInLeft { from{transform:translateX(-20px);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideInRight { from{transform:translateX(20px);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes zoomIn { from{transform:scale(.9);opacity:0} to{transform:scale(1);opacity:1} }
@keyframes bounceIn { 0%{transform:scale(.3);opacity:0} 50%{transform:scale(1.05)} 70%{transform:scale(.9)} 100%{transform:scale(1);opacity:1} }
@keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.05)} }
</style>
@endpush
