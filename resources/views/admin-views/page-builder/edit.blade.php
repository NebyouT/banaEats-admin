@extends('layouts.admin.app')

@section('title', translate('Edit Page') . ' - ' . $page->title)

@push('css_or_js')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">
<style>
/* Layout */
.builder-container { display: flex; height: calc(100vh - 70px); overflow: hidden; }
.builder-sidebar { width: 280px; background: #fff; border-right: 1px solid #e7eaf3; display: flex; flex-direction: column; flex-shrink: 0; }
.builder-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f0f2f5; }
.builder-canvas-wrap { flex: 1; overflow: auto; padding: 20px; display: flex; justify-content: center; }
.builder-properties { width: 320px; background: #fff; border-left: 1px solid #e7eaf3; overflow-y: auto; flex-shrink: 0; }

/* Sidebar */
.sidebar-header { padding: 16px; border-bottom: 1px solid #e7eaf3; }
.sidebar-tabs { display: flex; border-bottom: 1px solid #e7eaf3; }
.sidebar-tab { flex: 1; padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #8c98a4; cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.2s; }
.sidebar-tab:hover { color: #1e2022; }
.sidebar-tab.active { color: #FC6A57; border-bottom-color: #FC6A57; }
.sidebar-content { flex: 1; overflow-y: auto; padding: 12px; }

/* Section/Component Items */
.builder-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: #f8f9fa; border: 1px solid #e7eaf3; border-radius: 8px; margin-bottom: 8px; cursor: grab; transition: all 0.2s; }
.builder-item:hover { background: #fff; border-color: #FC6A57; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.builder-item i { font-size: 18px; color: #FC6A57; width: 24px; text-align: center; }
.builder-item-info { flex: 1; }
.builder-item-name { font-size: 13px; font-weight: 600; color: #1e2022; }
.builder-item-desc { font-size: 11px; color: #8c98a4; }

/* Canvas */
.builder-canvas { width: 100%; max-width: 420px; min-height: 600px; background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden; }
.canvas-header { padding: 12px 16px; background: #1a1a1a; color: #fff; display: flex; align-items: center; justify-content: space-between; }
.canvas-header .title { font-size: 14px; font-weight: 600; }
.canvas-body { min-height: 500px; position: relative; }
.canvas-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 400px; color: #8c98a4; text-align: center; padding: 40px; }
.canvas-empty i { font-size: 48px; margin-bottom: 16px; color: #dee2e6; }
.canvas-empty p { font-size: 14px; margin: 0; }

/* Sections in Canvas */
.canvas-section { position: relative; border: 2px dashed transparent; margin: 8px; border-radius: 8px; transition: all 0.2s; min-height: 60px; }
.canvas-section:hover { border-color: #dee2e6; }
.canvas-section.selected { border-color: #FC6A57; background: rgba(252,106,87,0.03); }
.canvas-section.sortable-ghost { opacity: 0.4; background: #FC6A57; }
.section-toolbar { position: absolute; top: -32px; right: 8px; display: none; gap: 4px; background: #1e2022; border-radius: 6px; padding: 4px; z-index: 10; }
.canvas-section:hover .section-toolbar, .canvas-section.selected .section-toolbar { display: flex; }
.section-toolbar button { width: 26px; height: 26px; border: none; background: transparent; color: #fff; border-radius: 4px; cursor: pointer; font-size: 12px; }
.section-toolbar button:hover { background: rgba(255,255,255,0.15); }
.section-label { position: absolute; top: 4px; left: 8px; font-size: 10px; font-weight: 600; color: #8c98a4; text-transform: uppercase; letter-spacing: 0.5px; }

/* Components in Section */
.section-components { padding: 24px 12px 12px; min-height: 50px; }
.canvas-component { position: relative; border: 1px dashed transparent; border-radius: 6px; padding: 8px; margin-bottom: 8px; transition: all 0.2s; }
.canvas-component:hover { border-color: #8DC63F; background: rgba(141,198,63,0.03); }
.canvas-component.selected { border-color: #8DC63F; background: rgba(141,198,63,0.05); }
.component-toolbar { position: absolute; top: -28px; right: 4px; display: none; gap: 2px; background: #8DC63F; border-radius: 4px; padding: 2px; z-index: 10; }
.canvas-component:hover .component-toolbar, .canvas-component.selected .component-toolbar { display: flex; }
.component-toolbar button { width: 22px; height: 22px; border: none; background: transparent; color: #fff; border-radius: 3px; cursor: pointer; font-size: 11px; }
.component-toolbar button:hover { background: rgba(255,255,255,0.2); }

/* Properties Panel */
.properties-header { padding: 16px; border-bottom: 1px solid #e7eaf3; display: flex; align-items: center; justify-content: space-between; }
.properties-header h5 { margin: 0; font-size: 14px; font-weight: 600; }
.properties-body { padding: 16px; }
.prop-group { margin-bottom: 20px; }
.prop-group-title { font-size: 11px; font-weight: 700; color: #8c98a4; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
.prop-row { margin-bottom: 12px; }
.prop-label { font-size: 12px; font-weight: 500; color: #1e2022; margin-bottom: 4px; }
.prop-input { font-size: 13px; }
.color-input-wrap { display: flex; gap: 8px; align-items: center; }
.color-input-wrap input[type="color"] { width: 36px; height: 36px; padding: 2px; border: 1px solid #e7eaf3; border-radius: 6px; cursor: pointer; }
.color-input-wrap input[type="text"] { flex: 1; }

/* Toolbar */
.builder-toolbar { padding: 12px 16px; background: #fff; border-bottom: 1px solid #e7eaf3; display: flex; align-items: center; gap: 12px; }
.toolbar-title { font-size: 16px; font-weight: 600; color: #1e2022; flex: 1; }
.toolbar-actions { display: flex; gap: 8px; }

/* Device Switcher */
.device-switcher { display: flex; gap: 4px; background: #f8f9fa; border-radius: 8px; padding: 4px; }
.device-btn { width: 32px; height: 32px; border: none; background: transparent; border-radius: 6px; cursor: pointer; color: #8c98a4; transition: all 0.2s; }
.device-btn:hover { color: #1e2022; }
.device-btn.active { background: #fff; color: #FC6A57; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

/* Component Previews */
.preview-text { font-size: 14px; color: #333; line-height: 1.5; }
.preview-heading { font-weight: 700; color: #1a1a1a; }
.preview-heading.h1 { font-size: 28px; }
.preview-heading.h2 { font-size: 24px; }
.preview-heading.h3 { font-size: 20px; }
.preview-image { width: 100%; border-radius: 8px; background: #f0f0f0; min-height: 100px; display: flex; align-items: center; justify-content: center; color: #aaa; }
.preview-image img { width: 100%; border-radius: 8px; }
.preview-button { display: inline-block; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; text-align: center; cursor: pointer; }
.preview-product-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.preview-product-card .card-img { height: 120px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; }
.preview-product-card .card-img img { width: 100%; height: 100%; object-fit: cover; }
.preview-product-card .card-body { padding: 12px; }
.preview-product-card .card-title { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
.preview-product-card .card-price { font-size: 16px; font-weight: 700; color: #FC6A57; }
.preview-restaurant-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.preview-restaurant-card .card-img { height: 100px; background: #f5f5f5; }
.preview-restaurant-card .card-body { padding: 12px; }
.preview-spacer { background: repeating-linear-gradient(45deg, #f8f9fa, #f8f9fa 10px, #fff 10px, #fff 20px); border-radius: 4px; }
.preview-divider { border-top: 1px solid #e0e0e0; margin: 8px 0; }

/* Products/Restaurants Grid */
.preview-grid { display: grid; gap: 12px; }
.preview-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
.preview-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
.preview-carousel { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 8px; }
.preview-carousel > * { flex-shrink: 0; }

/* Data Picker Modal */
.data-picker-modal .modal-body { max-height: 60vh; overflow-y: auto; }
.picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
.picker-item { border: 2px solid #e7eaf3; border-radius: 8px; padding: 10px; cursor: pointer; text-align: center; transition: all 0.2s; }
.picker-item:hover { border-color: #FC6A57; }
.picker-item.selected { border-color: #FC6A57; background: #fff5f4; }
.picker-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-bottom: 8px; }
.picker-item .name { font-size: 12px; font-weight: 600; }
.picker-item .meta { font-size: 11px; color: #8c98a4; }
</style>
@endpush

@section('content')
<div class="builder-container">
    <!-- Left Sidebar: Sections & Components -->
    <div class="builder-sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.page-builder.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="tio-arrow-backward"></i>
            </a>
            <span class="ml-2 font-weight-bold">{{ Str::limit($page->title, 20) }}</span>
        </div>
        <div class="sidebar-tabs">
            <div class="sidebar-tab active" data-tab="sections">{{ translate('Sections') }}</div>
            <div class="sidebar-tab" data-tab="components">{{ translate('Components') }}</div>
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

    <!-- Main Canvas Area -->
    <div class="builder-main">
        <div class="builder-toolbar">
            <div class="toolbar-title">
                <input type="text" id="page-title-input" value="{{ $page->title }}" class="form-control form-control-sm" style="max-width: 300px; display: inline-block;">
            </div>
            <div class="device-switcher">
                <button class="device-btn active" data-width="375" title="Mobile"><i class="tio-android-phone"></i></button>
                <button class="device-btn" data-width="768" title="Tablet"><i class="tio-tablet"></i></button>
                <button class="device-btn" data-width="1024" title="Desktop"><i class="tio-monitor"></i></button>
            </div>
            <div class="toolbar-actions">
                <a href="{{ route('admin.page-builder.preview', $page->id) }}" target="_blank" class="btn btn-sm btn-outline-info">
                    <i class="tio-visible mr-1"></i> {{ translate('Preview') }}
                </a>
                <button type="button" class="btn btn-sm btn-primary" id="save-page-btn">
                    <i class="tio-save mr-1"></i> {{ translate('Save') }}
                </button>
                <button type="button" class="btn btn-sm {{ $page->is_published ? 'btn-success' : 'btn-warning' }}" id="publish-btn">
                    <i class="tio-{{ $page->is_published ? 'checkmark-circle' : 'cloud-upload' }} mr-1"></i>
                    {{ $page->is_published ? translate('Published') : translate('Publish') }}
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
                    <div class="canvas-empty" id="canvas-empty">
                        <i class="tio-drag-and-drop"></i>
                        <p>{{ translate('Drag sections here to start building your page') }}</p>
                    </div>
                    @endif
                    <div id="sections-container">
                        <!-- Sections will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Properties -->
    <div class="builder-properties" id="properties-panel">
        <div class="properties-header">
            <h5>{{ translate('Page Settings') }}</h5>
        </div>
        <div class="properties-body" id="properties-body">
            <div class="prop-group">
                <div class="prop-group-title">{{ translate('Background') }}</div>
                <div class="prop-row">
                    <div class="prop-label">{{ translate('Color') }}</div>
                    <div class="color-input-wrap">
                        <input type="color" id="page-bg-color" value="{{ $page->settings['background_color'] ?? '#ffffff' }}">
                        <input type="text" class="form-control prop-input" id="page-bg-color-hex" value="{{ $page->settings['background_color'] ?? '#ffffff' }}">
                    </div>
                </div>
            </div>
            <div class="prop-group">
                <div class="prop-group-title">{{ translate('Theme Colors') }}</div>
                <div class="prop-row">
                    <div class="prop-label">{{ translate('Primary') }}</div>
                    <div class="color-input-wrap">
                        <input type="color" id="page-primary-color" value="{{ $page->settings['primary_color'] ?? '#FC6A57' }}">
                        <input type="text" class="form-control prop-input" value="{{ $page->settings['primary_color'] ?? '#FC6A57' }}">
                    </div>
                </div>
                <div class="prop-row">
                    <div class="prop-label">{{ translate('Secondary') }}</div>
                    <div class="color-input-wrap">
                        <input type="color" id="page-secondary-color" value="{{ $page->settings['secondary_color'] ?? '#8DC63F' }}">
                        <input type="text" class="form-control prop-input" value="{{ $page->settings['secondary_color'] ?? '#8DC63F' }}">
                    </div>
                </div>
            </div>
            <p class="text-muted text-center" style="font-size: 12px;">
                <i class="tio-info-outlined mr-1"></i>
                {{ translate('Select a section or component to edit its properties') }}
            </p>
        </div>
    </div>
</div>

<!-- Data Picker Modal -->
<div class="modal fade data-picker-modal" id="dataPickerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataPickerTitle">{{ translate('Select Items') }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="dataPickerSearch" placeholder="{{ translate('Search...') }}">
                </div>
                <div class="picker-grid" id="dataPickerGrid">
                    <!-- Items loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="dataPickerConfirm">{{ translate('Confirm Selection') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
"use strict";

// Page data
let pageData = @json($page->toBuilderJson());
let selectedSection = null;
let selectedComponent = null;

// URLs
const URLS = {
    save: '{{ route("admin.page-builder.save-structure", $page->id) }}',
    publish: '{{ route("admin.page-builder.publish", $page->id) }}',
    addSection: '{{ route("admin.page-builder.section.add", $page->id) }}',
    searchProducts: '{{ route("admin.page-builder.search-products") }}',
    searchRestaurants: '{{ route("admin.page-builder.search-restaurants") }}',
};

// Initialize
$(document).ready(function() {
    renderSections();
    initSortable();
    initSidebarTabs();
    initDragDrop();
    initDeviceSwitcher();
    initPropertySync();
    initSavePublish();
});

// Render all sections
function renderSections() {
    const container = $('#sections-container');
    container.empty();
    
    if (!pageData.sections || pageData.sections.length === 0) {
        $('#canvas-empty').show();
        return;
    }
    
    $('#canvas-empty').hide();
    
    pageData.sections.forEach((section, idx) => {
        container.append(renderSection(section, idx));
    });
    
    initSectionSortable();
}

// Render single section
function renderSection(section, index) {
    const typeInfo = {!! json_encode($sectionTypes) !!}[section.section_type] || { name: 'Section', icon: 'tio-layers' };
    
    let componentsHtml = '';
    if (section.components && section.components.length > 0) {
        section.components.forEach((comp, compIdx) => {
            componentsHtml += renderComponent(comp, compIdx);
        });
    }
    
    return `
        <div class="canvas-section" data-section-id="${section.id}" data-index="${index}">
            <div class="section-toolbar">
                <button type="button" onclick="moveSection(${index}, -1)" title="Move Up"><i class="tio-arrow-up"></i></button>
                <button type="button" onclick="moveSection(${index}, 1)" title="Move Down"><i class="tio-arrow-down"></i></button>
                <button type="button" onclick="duplicateSection(${index})" title="Duplicate"><i class="tio-copy"></i></button>
                <button type="button" onclick="deleteSection(${index})" title="Delete"><i class="tio-delete"></i></button>
            </div>
            <span class="section-label">${typeInfo.name}</span>
            <div class="section-components" data-section-index="${index}">
                ${componentsHtml}
                ${section.components && section.components.length === 0 ? '<div class="text-center text-muted py-3" style="font-size:12px">Drop components here</div>' : ''}
            </div>
        </div>
    `;
}

// Render single component
function renderComponent(comp, index) {
    const preview = getComponentPreview(comp);
    
    return `
        <div class="canvas-component" data-component-id="${comp.id}" data-index="${index}">
            <div class="component-toolbar">
                <button type="button" onclick="editComponent(event, ${comp.id})" title="Edit"><i class="tio-edit"></i></button>
                <button type="button" onclick="deleteComponent(event, ${comp.id})" title="Delete"><i class="tio-delete"></i></button>
            </div>
            ${preview}
        </div>
    `;
}

// Get component preview HTML
function getComponentPreview(comp) {
    const content = comp.content || {};
    const settings = comp.settings || {};
    
    switch (comp.component_type) {
        case 'text':
            return `<div class="preview-text" style="color:${settings.color || '#333'};font-size:${settings.font_size || 14}px">${content.text || 'Text content...'}</div>`;
        
        case 'heading':
            const level = content.level || 'h2';
            return `<div class="preview-heading ${level}" style="color:${settings.color || '#1a1a1a'}">${content.text || 'Heading'}</div>`;
        
        case 'image':
            if (content.url) {
                return `<div class="preview-image"><img src="${content.url}" alt="${content.alt || ''}"></div>`;
            }
            return `<div class="preview-image" style="height:${settings.height || 150}px"><i class="tio-image" style="font-size:32px"></i></div>`;
        
        case 'button':
            return `<div class="preview-button" style="background:${settings.background_color || '#FC6A57'};color:${settings.text_color || '#fff'};border-radius:${settings.border_radius || 8}px">${content.text || 'Button'}</div>`;
        
        case 'product_card':
        case 'product_list':
            return `
                <div class="preview-grid cols-2">
                    <div class="preview-product-card">
                        <div class="card-img"><i class="tio-restaurant" style="font-size:24px;color:#ddd"></i></div>
                        <div class="card-body">
                            <div class="card-title">Product Name</div>
                            <div class="card-price">ETB 99.00</div>
                        </div>
                    </div>
                    <div class="preview-product-card">
                        <div class="card-img"><i class="tio-restaurant" style="font-size:24px;color:#ddd"></i></div>
                        <div class="card-body">
                            <div class="card-title">Product Name</div>
                            <div class="card-price">ETB 99.00</div>
                        </div>
                    </div>
                </div>
            `;
        
        case 'restaurant_card':
        case 'restaurant_list':
            return `
                <div class="preview-grid cols-2">
                    <div class="preview-restaurant-card">
                        <div class="card-img" style="background:#f5f5f5;display:flex;align-items:center;justify-content:center"><i class="tio-shop" style="font-size:24px;color:#ddd"></i></div>
                        <div class="card-body">
                            <div class="card-title">Restaurant Name</div>
                            <div style="font-size:11px;color:#888">⭐ 4.5 • 20-30 min</div>
                        </div>
                    </div>
                </div>
            `;
        
        case 'spacer':
            return `<div class="preview-spacer" style="height:${settings.height || 24}px"></div>`;
        
        case 'divider':
            return `<div class="preview-divider" style="border-color:${settings.color || '#e0e0e0'};border-width:${settings.thickness || 1}px"></div>`;
        
        default:
            return `<div class="text-muted text-center py-2" style="font-size:12px">${comp.component_type}</div>`;
    }
}

// Initialize sortable for sections
function initSectionSortable() {
    const container = document.getElementById('sections-container');
    if (!container) return;
    
    new Sortable(container, {
        animation: 150,
        handle: '.canvas-section',
        ghostClass: 'sortable-ghost',
        onEnd: function(evt) {
            // Reorder sections in pageData
            const item = pageData.sections.splice(evt.oldIndex, 1)[0];
            pageData.sections.splice(evt.newIndex, 0, item);
            // Update order values
            pageData.sections.forEach((s, i) => s.order = i);
        }
    });
    
    // Initialize sortable for components within each section
    document.querySelectorAll('.section-components').forEach(el => {
        new Sortable(el, {
            group: 'components',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                const sectionIdx = parseInt(evt.to.dataset.sectionIndex);
                const section = pageData.sections[sectionIdx];
                if (!section) return;
                
                const item = section.components.splice(evt.oldIndex, 1)[0];
                section.components.splice(evt.newIndex, 0, item);
                section.components.forEach((c, i) => c.order = i);
            }
        });
    });
}

// Initialize sortable
function initSortable() {
    initSectionSortable();
}

// Sidebar tabs
function initSidebarTabs() {
    $('.sidebar-tab').on('click', function() {
        const tab = $(this).data('tab');
        $('.sidebar-tab').removeClass('active');
        $(this).addClass('active');
        
        if (tab === 'sections') {
            $('#sidebar-sections').removeClass('d-none');
            $('#sidebar-components').addClass('d-none');
        } else {
            $('#sidebar-sections').addClass('d-none');
            $('#sidebar-components').removeClass('d-none');
        }
    });
}

// Drag and drop from sidebar
function initDragDrop() {
    // Make sidebar items draggable
    $('.builder-item').on('dragstart', function(e) {
        const type = $(this).data('type');
        const subType = type === 'section' ? $(this).data('section-type') : $(this).data('component-type');
        e.originalEvent.dataTransfer.setData('type', type);
        e.originalEvent.dataTransfer.setData('subType', subType);
    });
    
    // Canvas drop zone
    const canvasBody = document.getElementById('canvas-body');
    
    canvasBody.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
    });
    
    canvasBody.addEventListener('drop', function(e) {
        e.preventDefault();
        const type = e.dataTransfer.getData('type');
        const subType = e.dataTransfer.getData('subType');
        
        if (type === 'section') {
            addSection(subType);
        }
    });
    
    // Section drop zones for components
    $(document).on('dragover', '.section-components', function(e) {
        e.preventDefault();
    });
    
    $(document).on('drop', '.section-components', function(e) {
        e.preventDefault();
        const type = e.originalEvent.dataTransfer.getData('type');
        const subType = e.originalEvent.dataTransfer.getData('subType');
        
        if (type === 'component') {
            const sectionIdx = $(this).data('section-index');
            addComponent(sectionIdx, subType);
        }
    });
}

// Add new section
function addSection(sectionType) {
    const newSection = {
        id: 'new_' + Date.now(),
        section_type: sectionType,
        name: {!! json_encode($sectionTypes) !!}[sectionType]?.name || 'Section',
        order: pageData.sections.length,
        settings: {},
        components: [],
        is_visible: true
    };
    
    pageData.sections.push(newSection);
    renderSections();
    toastr.success('Section added');
}

// Add new component
function addComponent(sectionIndex, componentType) {
    const section = pageData.sections[sectionIndex];
    if (!section) return;
    
    const newComponent = {
        id: 'new_' + Date.now(),
        component_type: componentType,
        order: section.components.length,
        column_span: 12,
        content: {},
        settings: {},
        is_visible: true
    };
    
    section.components.push(newComponent);
    renderSections();
    toastr.success('Component added');
}

// Move section
function moveSection(index, direction) {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= pageData.sections.length) return;
    
    const item = pageData.sections.splice(index, 1)[0];
    pageData.sections.splice(newIndex, 0, item);
    pageData.sections.forEach((s, i) => s.order = i);
    renderSections();
}

// Duplicate section
function duplicateSection(index) {
    const original = pageData.sections[index];
    const copy = JSON.parse(JSON.stringify(original));
    copy.id = 'new_' + Date.now();
    copy.name = original.name + ' (Copy)';
    copy.components.forEach(c => c.id = 'new_' + Date.now() + '_' + Math.random());
    
    pageData.sections.splice(index + 1, 0, copy);
    pageData.sections.forEach((s, i) => s.order = i);
    renderSections();
    toastr.success('Section duplicated');
}

// Delete section
function deleteSection(index) {
    Swal.fire({
        title: 'Delete Section?',
        text: 'This will also delete all components inside.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FC6A57',
        confirmButtonText: 'Yes, delete'
    }).then((result) => {
        if (result.isConfirmed) {
            pageData.sections.splice(index, 1);
            pageData.sections.forEach((s, i) => s.order = i);
            renderSections();
            toastr.success('Section deleted');
        }
    });
}

// Edit component
function editComponent(event, componentId) {
    event.stopPropagation();
    // Find component
    let comp = null;
    let sectionIdx = -1;
    let compIdx = -1;
    
    for (let i = 0; i < pageData.sections.length; i++) {
        const idx = pageData.sections[i].components.findIndex(c => c.id == componentId);
        if (idx !== -1) {
            comp = pageData.sections[i].components[idx];
            sectionIdx = i;
            compIdx = idx;
            break;
        }
    }
    
    if (!comp) return;
    
    // Show properties panel for this component
    showComponentProperties(comp, sectionIdx, compIdx);
}

// Delete component
function deleteComponent(event, componentId) {
    event.stopPropagation();
    
    for (let i = 0; i < pageData.sections.length; i++) {
        const idx = pageData.sections[i].components.findIndex(c => c.id == componentId);
        if (idx !== -1) {
            pageData.sections[i].components.splice(idx, 1);
            pageData.sections[i].components.forEach((c, j) => c.order = j);
            renderSections();
            toastr.success('Component deleted');
            return;
        }
    }
}

// Show component properties
function showComponentProperties(comp, sectionIdx, compIdx) {
    const content = comp.content || {};
    const settings = comp.settings || {};
    
    let html = `
        <div class="prop-group">
            <div class="prop-group-title">${comp.component_type.replace('_', ' ').toUpperCase()}</div>
    `;
    
    // Content fields based on type
    switch (comp.component_type) {
        case 'text':
        case 'heading':
            html += `
                <div class="prop-row">
                    <div class="prop-label">Text</div>
                    <textarea class="form-control prop-input" id="prop-text" rows="3">${content.text || ''}</textarea>
                </div>
            `;
            if (comp.component_type === 'heading') {
                html += `
                    <div class="prop-row">
                        <div class="prop-label">Level</div>
                        <select class="form-control prop-input" id="prop-level">
                            <option value="h1" ${content.level === 'h1' ? 'selected' : ''}>H1</option>
                            <option value="h2" ${content.level === 'h2' ? 'selected' : ''}>H2</option>
                            <option value="h3" ${content.level === 'h3' ? 'selected' : ''}>H3</option>
                        </select>
                    </div>
                `;
            }
            break;
        
        case 'button':
            html += `
                <div class="prop-row">
                    <div class="prop-label">Button Text</div>
                    <input type="text" class="form-control prop-input" id="prop-text" value="${content.text || 'Button'}">
                </div>
                <div class="prop-row">
                    <div class="prop-label">Background Color</div>
                    <div class="color-input-wrap">
                        <input type="color" id="prop-bg-color" value="${settings.background_color || '#FC6A57'}">
                        <input type="text" class="form-control prop-input" value="${settings.background_color || '#FC6A57'}">
                    </div>
                </div>
                <div class="prop-row">
                    <div class="prop-label">Text Color</div>
                    <div class="color-input-wrap">
                        <input type="color" id="prop-text-color" value="${settings.text_color || '#ffffff'}">
                        <input type="text" class="form-control prop-input" value="${settings.text_color || '#ffffff'}">
                    </div>
                </div>
            `;
            break;
        
        case 'product_list':
            html += `
                <div class="prop-row">
                    <div class="prop-label">Title</div>
                    <input type="text" class="form-control prop-input" id="prop-title" value="${content.title || 'Featured Products'}">
                </div>
                <div class="prop-row">
                    <div class="prop-label">Products</div>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="openDataPicker('products', ${sectionIdx}, ${compIdx})">
                        <i class="tio-add mr-1"></i> Select Products (${(content.product_ids || []).length})
                    </button>
                </div>
                <div class="prop-row">
                    <div class="prop-label">Columns</div>
                    <select class="form-control prop-input" id="prop-columns">
                        <option value="2" ${(settings.columns || 2) == 2 ? 'selected' : ''}>2 Columns</option>
                        <option value="3" ${settings.columns == 3 ? 'selected' : ''}>3 Columns</option>
                    </select>
                </div>
            `;
            break;
        
        case 'restaurant_list':
            html += `
                <div class="prop-row">
                    <div class="prop-label">Title</div>
                    <input type="text" class="form-control prop-input" id="prop-title" value="${content.title || 'Featured Restaurants'}">
                </div>
                <div class="prop-row">
                    <div class="prop-label">Restaurants</div>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="openDataPicker('restaurants', ${sectionIdx}, ${compIdx})">
                        <i class="tio-add mr-1"></i> Select Restaurants (${(content.restaurant_ids || []).length})
                    </button>
                </div>
            `;
            break;
        
        case 'spacer':
            html += `
                <div class="prop-row">
                    <div class="prop-label">Height (px)</div>
                    <input type="number" class="form-control prop-input" id="prop-height" value="${settings.height || 24}" min="8" max="200">
                </div>
            `;
            break;
    }
    
    html += `
        </div>
        <button type="button" class="btn btn-primary btn-block mt-3" onclick="applyComponentChanges(${sectionIdx}, ${compIdx})">
            Apply Changes
        </button>
    `;
    
    $('#properties-body').html(html);
    $('.properties-header h5').text('Edit ' + comp.component_type.replace('_', ' '));
}

// Apply component changes
function applyComponentChanges(sectionIdx, compIdx) {
    const comp = pageData.sections[sectionIdx].components[compIdx];
    if (!comp) return;
    
    // Update content based on type
    switch (comp.component_type) {
        case 'text':
        case 'heading':
            comp.content.text = $('#prop-text').val();
            if (comp.component_type === 'heading') {
                comp.content.level = $('#prop-level').val();
            }
            break;
        
        case 'button':
            comp.content.text = $('#prop-text').val();
            comp.settings.background_color = $('#prop-bg-color').val();
            comp.settings.text_color = $('#prop-text-color').val();
            break;
        
        case 'product_list':
            comp.content.title = $('#prop-title').val();
            comp.settings.columns = parseInt($('#prop-columns').val());
            break;
        
        case 'restaurant_list':
            comp.content.title = $('#prop-title').val();
            break;
        
        case 'spacer':
            comp.settings.height = parseInt($('#prop-height').val());
            break;
    }
    
    renderSections();
    toastr.success('Changes applied');
}

// Data picker
let currentPickerType = null;
let currentPickerSection = null;
let currentPickerComponent = null;
let selectedPickerItems = [];

function openDataPicker(type, sectionIdx, compIdx) {
    currentPickerType = type;
    currentPickerSection = sectionIdx;
    currentPickerComponent = compIdx;
    
    const comp = pageData.sections[sectionIdx].components[compIdx];
    selectedPickerItems = type === 'products' 
        ? (comp.content.product_ids || []) 
        : (comp.content.restaurant_ids || []);
    
    $('#dataPickerTitle').text(type === 'products' ? 'Select Products' : 'Select Restaurants');
    $('#dataPickerModal').modal('show');
    
    loadPickerData('');
}

function loadPickerData(search) {
    const url = currentPickerType === 'products' ? URLS.searchProducts : URLS.searchRestaurants;
    
    $.get(url, { search: search }, function(data) {
        let html = '';
        data.forEach(item => {
            const isSelected = selectedPickerItems.includes(item.id);
            const img = item.image_full_url || item.logo_full_url || '';
            html += `
                <div class="picker-item ${isSelected ? 'selected' : ''}" data-id="${item.id}">
                    <img src="${img}" onerror="this.src='{{ dynamicAsset("public/assets/admin/img/100x100/food-default-image.png") }}'">
                    <div class="name">${item.name}</div>
                    <div class="meta">${item.restaurant_name || item.address || ''}</div>
                </div>
            `;
        });
        $('#dataPickerGrid').html(html || '<p class="text-center text-muted">No items found</p>');
    });
}

$('#dataPickerSearch').on('input', function() {
    loadPickerData($(this).val());
});

$(document).on('click', '.picker-item', function() {
    const id = parseInt($(this).data('id'));
    $(this).toggleClass('selected');
    
    if ($(this).hasClass('selected')) {
        if (!selectedPickerItems.includes(id)) selectedPickerItems.push(id);
    } else {
        selectedPickerItems = selectedPickerItems.filter(i => i !== id);
    }
});

$('#dataPickerConfirm').on('click', function() {
    const comp = pageData.sections[currentPickerSection].components[currentPickerComponent];
    
    if (currentPickerType === 'products') {
        comp.content.product_ids = selectedPickerItems;
    } else {
        comp.content.restaurant_ids = selectedPickerItems;
    }
    
    $('#dataPickerModal').modal('hide');
    showComponentProperties(comp, currentPickerSection, currentPickerComponent);
    renderSections();
    toastr.success('Items selected');
});

// Device switcher
function initDeviceSwitcher() {
    $('.device-btn').on('click', function() {
        $('.device-btn').removeClass('active');
        $(this).addClass('active');
        const width = $(this).data('width');
        $('#builder-canvas').css('max-width', width + 'px');
    });
}

// Property sync
function initPropertySync() {
    // Color pickers
    $(document).on('input', 'input[type="color"]', function() {
        $(this).siblings('input[type="text"]').val($(this).val());
    });
}

// Save and publish
function initSavePublish() {
    $('#save-page-btn').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="tio-loading tio-spin mr-1"></i> Saving...');
        
        // Update title
        pageData.title = $('#page-title-input').val();
        
        $.ajax({
            url: URLS.save,
            type: 'POST',
            data: JSON.stringify(pageData),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(response) {
                btn.prop('disabled', false).html('<i class="tio-save mr-1"></i> Save');
                if (response.success) {
                    pageData = response.page;
                    renderSections();
                    toastr.success(response.message || 'Page saved!');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="tio-save mr-1"></i> Save');
                toastr.error('Failed to save');
            }
        });
    });
    
    $('#publish-btn').on('click', function() {
        const btn = $(this);
        
        $.post(URLS.publish, { _token: '{{ csrf_token() }}' }, function(response) {
            if (response.is_published) {
                btn.removeClass('btn-warning').addClass('btn-success')
                   .html('<i class="tio-checkmark-circle mr-1"></i> Published');
            } else {
                btn.removeClass('btn-success').addClass('btn-warning')
                   .html('<i class="tio-cloud-upload mr-1"></i> Publish');
            }
            toastr.success(response.message);
        });
    });
}

// Section click selection
$(document).on('click', '.canvas-section', function(e) {
    if ($(e.target).closest('.section-toolbar').length) return;
    
    $('.canvas-section').removeClass('selected');
    $(this).addClass('selected');
    selectedSection = $(this).data('section-id');
});

// Component click selection
$(document).on('click', '.canvas-component', function(e) {
    e.stopPropagation();
    if ($(e.target).closest('.component-toolbar').length) return;
    
    $('.canvas-component').removeClass('selected');
    $(this).addClass('selected');
    
    const compId = $(this).data('component-id');
    // Find and show properties
    for (let i = 0; i < pageData.sections.length; i++) {
        const idx = pageData.sections[i].components.findIndex(c => c.id == compId);
        if (idx !== -1) {
            showComponentProperties(pageData.sections[i].components[idx], i, idx);
            break;
        }
    }
});
</script>
@endpush
