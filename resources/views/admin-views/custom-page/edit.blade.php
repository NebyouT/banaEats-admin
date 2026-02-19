@extends('layouts.admin.app')

@section('title', translate('messages.edit_custom_page'))

@push('css_or_js')
<style>
.cp-section-card { border: 1px solid #e7eaf3; border-radius: 10px; overflow: hidden; }
.cp-section-card .cp-section-header { background: #f8f9fa; padding: 12px 16px; border-bottom: 1px solid #e7eaf3; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
.cp-section-card .cp-section-header h5 { margin: 0; font-size: 14px; font-weight: 600; }
.cp-browse-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; max-height: 340px; overflow-y: auto; padding: 12px; background: #fff; }
.cp-browse-grid.restaurant-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
.cp-grid-item { border: 2px solid #e7eaf3; border-radius: 8px; cursor: pointer; transition: border-color .15s, box-shadow .15s; background: #fff; display: flex; flex-direction: column; align-items: center; padding: 10px 8px 8px; text-align: center; position: relative; }
.cp-grid-item:hover { border-color: #FC6A57; box-shadow: 0 2px 10px rgba(252,106,87,.15); }
.cp-grid-item.selected { border-color: #FC6A57; background: #fff5f4; }
.cp-grid-item.selected::after { content: '\2713'; position: absolute; top: 5px; right: 7px; background: #FC6A57; color: #fff; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; line-height: 18px; text-align: center; }
.cp-grid-item img { width: 72px; height: 72px; object-fit: cover; border-radius: 8px; margin-bottom: 7px; }
.cp-grid-item.restaurant-grid-item img { border-radius: 50%; }
.cp-grid-item .cp-item-name { font-size: 12px; font-weight: 600; color: #1e2022; line-height: 1.3; margin-bottom: 2px; }
.cp-grid-item .cp-item-sub { font-size: 11px; color: #8c98a4; }
.cp-grid-item .cp-item-price { font-size: 12px; font-weight: 700; color: #FC6A57; margin-top: 3px; }
.cp-selected-area { padding: 12px 16px; background: #f8f9fa; border-top: 1px solid #e7eaf3; min-height: 60px; }
.cp-selected-area .cp-selected-title { font-size: 12px; font-weight: 600; color: #677788; margin-bottom: 8px; text-transform: uppercase; letter-spacing: .5px; }
.cp-selected-list { display: flex; flex-wrap: wrap; gap: 8px; }
.cp-selected-item { display: flex; align-items: center; gap: 8px; background: #fff; border: 1px solid #e7eaf3; border-radius: 8px; padding: 5px 10px 5px 6px; font-size: 12px; color: #1e2022; }
.cp-selected-item img { width: 30px; height: 30px; object-fit: cover; border-radius: 5px; }
.cp-selected-item .cp-remove { cursor: pointer; color: #e74c3c; font-size: 16px; line-height: 1; margin-left: 4px; font-weight: 700; }
.cp-empty-msg { color: #8c98a4; font-size: 13px; font-style: italic; }
.cp-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
.cp-toolbar .form-control { font-size: 13px; }
.cp-toolbar select.form-control { max-width: 200px; }
.cp-no-results { padding: 30px; text-align: center; color: #8c98a4; font-size: 13px; }
.cp-loading { padding: 30px; text-align: center; color: #8c98a4; }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-edit text-primary"></i></span>
                    Edit Custom Page
                </h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.custom-page.index') }}" class="btn btn-outline-secondary">
                    <i class="tio-arrow-backward mr-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    <form id="custom-page-form" action="{{ route('admin.custom-page.update', $custom_page) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- LEFT --}}
            <div class="col-lg-8">

                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0">Page Information</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="input-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ $custom_page->title }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="input-label">Subtitle</label>
                                <input type="text" name="subtitle" class="form-control" value="{{ $custom_page->subtitle }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Promotional Text</label>
                            <textarea name="promotional_text" class="form-control" rows="2">{{ $custom_page->promotional_text }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-0">
                                <label class="input-label">Background Color</label>
                                <div class="d-flex align-items-center">
                                    <input type="color" name="background_color" id="bg_color_picker"
                                           value="{{ $custom_page->background_color ?? '#ffffff' }}"
                                           style="width:48px;height:38px;padding:2px;border-radius:6px;border:1px solid #e7eaf3;cursor:pointer;">
                                    <input type="text" id="bg_color_hex" value="{{ $custom_page->background_color ?? '#ffffff' }}"
                                           maxlength="7" class="form-control ml-2" style="width:110px;" placeholder="#ffffff">
                                </div>
                            </div>
                            <div class="col-md-6 form-group mb-0">
                                <label class="input-label">API Slug <small class="text-muted">(read-only)</small></label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" value="{{ $custom_page->slug }}" readonly>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-white" onclick="navigator.clipboard.writeText('{{ $custom_page->slug }}');toastr.info('Slug copied!')" title="Copy slug">
                                            <i class="tio-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">Use in: <code>GET /api/v1/custom-pages/{{ $custom_page->slug }}</code></small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PRODUCT PICKER --}}
                <div class="cp-section-card mb-3">
                    <div class="cp-section-header">
                        <h5><i class="tio-restaurant mr-1 text-primary"></i> Products
                            <span class="badge badge-soft-primary ml-1" id="product-count-badge">0 selected</span>
                        </h5>
                        <div class="cp-toolbar">
                            <select id="product-restaurant-filter" class="form-control form-control-sm" style="min-width:180px;">
                                <option value="">— All Restaurants —</option>
                            </select>
                            <input type="text" id="product-search" class="form-control form-control-sm"
                                   placeholder="Search products..." style="min-width:150px;" autocomplete="off">
                            <button type="button" class="btn btn-sm btn-white" id="product-search-clear" title="Clear">
                                <i class="tio-clear"></i>
                            </button>
                        </div>
                    </div>
                    <div class="cp-browse-grid" id="product-grid">
                        <div class="cp-loading">Loading products...</div>
                    </div>
                    <div class="cp-selected-area">
                        <div class="cp-selected-title">Added to page <span id="product-selected-count">(0)</span></div>
                        <div class="cp-selected-list" id="product-selected-list">
                            <span class="cp-empty-msg">No products added yet</span>
                        </div>
                        <div id="product-hidden-inputs"></div>
                    </div>
                </div>

                {{-- RESTAURANT PICKER --}}
                <div class="cp-section-card mb-3">
                    <div class="cp-section-header">
                        <h5><i class="tio-shop mr-1 text-warning"></i> Restaurants
                            <span class="badge badge-soft-warning ml-1" id="restaurant-count-badge">0 selected</span>
                        </h5>
                        <div class="cp-toolbar">
                            <input type="text" id="restaurant-search" class="form-control form-control-sm"
                                   placeholder="Search restaurants..." style="min-width:200px;" autocomplete="off">
                            <button type="button" class="btn btn-sm btn-white" id="restaurant-search-clear" title="Clear">
                                <i class="tio-clear"></i>
                            </button>
                        </div>
                    </div>
                    <div class="cp-browse-grid restaurant-grid" id="restaurant-grid">
                        <div class="cp-loading">Loading restaurants...</div>
                    </div>
                    <div class="cp-selected-area">
                        <div class="cp-selected-title">Added to page <span id="restaurant-selected-count">(0)</span></div>
                        <div class="cp-selected-list" id="restaurant-selected-list">
                            <span class="cp-empty-msg">No restaurants added yet</span>
                        </div>
                        <div id="restaurant-hidden-inputs"></div>
                    </div>
                </div>

            </div>{{-- /col-lg-8 --}}

            {{-- RIGHT --}}
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0">Background Image</h5></div>
                    <div class="card-body text-center">
                        <img id="bg-image-preview"
                             src="{{ $custom_page->background_image_full_url ?? dynamicAsset('public/assets/admin/img/900x400/img1.jpg') }}"
                             class="rounded mb-3" style="width:100%;height:160px;object-fit:cover;"
                             onerror="this.src='{{ dynamicAsset('public/assets/admin/img/900x400/img1.jpg') }}'">
                        <label class="btn btn-sm btn-outline-primary btn-block" for="background_image">
                            <i class="tio-upload mr-1"></i> Change Image
                        </label>
                        <input type="file" name="background_image" id="background_image" class="d-none" accept="image/*">
                        <p class="text-muted font-size-sm mt-2 mb-0">Recommended: 1200 × 400 px</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block" id="save-btn">
                            <i class="tio-save mr-1"></i> Update Page
                        </button>
                        <a href="{{ route('admin.custom-page.index') }}" class="btn btn-outline-secondary btn-block mt-2">Cancel</a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('script_2')
<script>
"use strict";

const SEARCH_PRODUCTS_URL    = '{{ route("admin.custom-page.search-products") }}';
const SEARCH_RESTAURANTS_URL = '{{ route("admin.custom-page.search-restaurants") }}';
const FALLBACK_FOOD_IMG      = '{{ dynamicAsset("public/assets/admin/img/100x100/food-default-image.png") }}';
const FALLBACK_REST_IMG      = '{{ dynamicAsset("public/assets/admin/img/160x160/img2.jpg") }}';

// Preloaded existing selections from PHP
const preloadedProducts    = @json($preloadedProductsJson);
const preloadedRestaurants = @json($preloadedRestaurantsJson);

// ── Colour picker ─────────────────────────────────────────────────────────────
const colorPicker = document.getElementById('bg_color_picker');
const colorHex    = document.getElementById('bg_color_hex');
colorPicker.addEventListener('input', () => { colorHex.value = colorPicker.value; });
colorHex.addEventListener('input', function () {
    if (/^#[0-9a-fA-F]{6}$/.test(this.value)) colorPicker.value = this.value;
});

// ── Image preview ─────────────────────────────────────────────────────────────
document.getElementById('background_image').addEventListener('change', function () {
    if (this.files && this.files[0]) {
        const r = new FileReader();
        r.onload = e => document.getElementById('bg-image-preview').src = e.target.result;
        r.readAsDataURL(this.files[0]);
    }
});

// ── GridPicker ────────────────────────────────────────────────────────────────
function GridPicker(cfg) {
    const grid         = document.getElementById(cfg.gridId);
    const selList      = document.getElementById(cfg.selectedListId);
    const hiddenInputs = document.getElementById(cfg.hiddenInputsId);
    const badge        = document.getElementById(cfg.badgeId);
    const countSpan    = document.getElementById(cfg.countSpanId);
    const selected     = {};

    function updateUI() {
        const n = Object.keys(selected).length;
        badge.textContent     = n + ' selected';
        countSpan.textContent = '(' + n + ')';
        hiddenInputs.innerHTML = '';
        Object.keys(selected).forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = cfg.inputName + '[]'; inp.value = id;
            hiddenInputs.appendChild(inp);
        });
        Array.from(selList.querySelectorAll('.cp-selected-item')).forEach(c => c.remove());
        const emptyMsg = selList.querySelector('.cp-empty-msg');
        if (!Object.keys(selected).length) {
            if (emptyMsg) emptyMsg.style.display = '';
            return;
        }
        if (emptyMsg) emptyMsg.style.display = 'none';
        Object.keys(selected).forEach(id => {
            const item = selected[id];
            const chip = document.createElement('div');
            chip.className = 'cp-selected-item';
            chip.innerHTML =
                '<img src="' + item.img + '" onerror="this.src=\'' + item.fallback + '\'" alt="">' +
                '<span>' + item.name + '</span>' +
                '<span class="cp-remove" title="Remove">&times;</span>';
            chip.querySelector('.cp-remove').addEventListener('click', () => deselect(String(id)));
            selList.appendChild(chip);
        });
    }

    function select(id, name, img, fallback) {
        id = String(id);
        if (selected[id]) { deselect(id); return; }
        selected[id] = { name, img: img || fallback, fallback };
        const el = grid.querySelector('[data-id="' + id + '"]');
        if (el) el.classList.add('selected');
        updateUI();
    }

    function deselect(id) {
        id = String(id);
        delete selected[id];
        const el = grid.querySelector('[data-id="' + id + '"]');
        if (el) el.classList.remove('selected');
        updateUI();
    }

    function renderGrid(items) {
        if (!items.length) {
            grid.innerHTML = '<div class="cp-no-results">No results found</div>';
            return;
        }
        grid.innerHTML = '';
        items.forEach(item => {
            const div      = document.createElement('div');
            div.className  = 'cp-grid-item' + (selected[String(item.id)] ? ' selected' : '');
            div.dataset.id = item.id;
            const imgSrc   = item[cfg.imgKey] || cfg.fallback;
            const subText  = item[cfg.subKey] || '';
            const price    = item.price !== undefined ? '<div class="cp-item-price">' + parseFloat(item.price).toFixed(2) + '</div>' : '';
            div.innerHTML  =
                '<img src="' + imgSrc + '" onerror="this.src=\'' + cfg.fallback + '\'" alt="">' +
                '<div class="cp-item-name">' + item[cfg.nameKey] + '</div>' +
                (subText ? '<div class="cp-item-sub">' + subText + '</div>' : '') + price;
            div.addEventListener('click', () => select(item.id, item[cfg.nameKey], imgSrc, cfg.fallback));
            grid.appendChild(div);
        });
    }

    function load(search, extra) {
        grid.innerHTML = '<div class="cp-loading">Loading...</div>';
        $.get(cfg.fetchUrl, Object.assign({ search: search || '' }, extra || {}), renderGrid)
         .fail(() => { grid.innerHTML = '<div class="cp-no-results">Failed to load.</div>'; });
    }

    // Pre-select items without waiting for grid
    function preSelect(id, name, img, fallback) {
        id = String(id);
        selected[id] = { name, img: img || fallback, fallback };
        updateUI();
    }

    this.load      = load;
    this.select    = select;
    this.deselect  = deselect;
    this.preSelect = preSelect;
}

// ── Restaurant dropdown ───────────────────────────────────────────────────────
const restFilterSel = document.getElementById('product-restaurant-filter');
function loadRestaurantDropdown(search) {
    $.get(SEARCH_RESTAURANTS_URL, { search: search || '' }, function (data) {
        const cur = restFilterSel.value;
        restFilterSel.innerHTML = '<option value="">— All Restaurants —</option>';
        data.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r.id; opt.textContent = r.name;
            if (String(r.id) === String(cur)) opt.selected = true;
            restFilterSel.appendChild(opt);
        });
    });
}
loadRestaurantDropdown('');

// ── Init pickers ──────────────────────────────────────────────────────────────
const productPicker = new GridPicker({
    gridId: 'product-grid', selectedListId: 'product-selected-list',
    hiddenInputsId: 'product-hidden-inputs', badgeId: 'product-count-badge',
    countSpanId: 'product-selected-count', inputName: 'product_ids',
    fetchUrl: SEARCH_PRODUCTS_URL, nameKey: 'name', subKey: 'restaurant_name',
    imgKey: 'image_full_url', fallback: FALLBACK_FOOD_IMG,
});

const restaurantPicker = new GridPicker({
    gridId: 'restaurant-grid', selectedListId: 'restaurant-selected-list',
    hiddenInputsId: 'restaurant-hidden-inputs', badgeId: 'restaurant-count-badge',
    countSpanId: 'restaurant-selected-count', inputName: 'restaurant_ids',
    fetchUrl: SEARCH_RESTAURANTS_URL, nameKey: 'name', subKey: 'address',
    imgKey: 'logo_full_url', fallback: FALLBACK_REST_IMG,
});

// ── Pre-populate existing selections ─────────────────────────────────────────
preloadedProducts.forEach(p => productPicker.preSelect(p.id, p.name, p.image_full_url, FALLBACK_FOOD_IMG));
preloadedRestaurants.forEach(r => restaurantPicker.preSelect(r.id, r.name, r.logo_full_url, FALLBACK_REST_IMG));

// Load grids (will mark pre-selected items as selected)
productPicker.load('', {});
restaurantPicker.load('', {});

// ── Search handlers ───────────────────────────────────────────────────────────
let productDebounce, restDebounce;

document.getElementById('product-search').addEventListener('input', function () {
    clearTimeout(productDebounce);
    const q = this.value.trim();
    productDebounce = setTimeout(() => {
        const rid = restFilterSel.value;
        productPicker.load(q, rid ? { restaurant_id: rid } : {});
    }, 350);
});
document.getElementById('product-search-clear').addEventListener('click', function () {
    document.getElementById('product-search').value = '';
    productPicker.load('', restFilterSel.value ? { restaurant_id: restFilterSel.value } : {});
});
restFilterSel.addEventListener('change', function () {
    const q = document.getElementById('product-search').value.trim();
    productPicker.load(q, this.value ? { restaurant_id: this.value } : {});
});

document.getElementById('restaurant-search').addEventListener('input', function () {
    clearTimeout(restDebounce);
    const q = this.value.trim();
    restDebounce = setTimeout(() => {
        restaurantPicker.load(q, {});
        loadRestaurantDropdown(q);
    }, 350);
});
document.getElementById('restaurant-search-clear').addEventListener('click', function () {
    document.getElementById('restaurant-search').value = '';
    restaurantPicker.load('', {});
    loadRestaurantDropdown('');
});

// ── Form submit ───────────────────────────────────────────────────────────────
$('#custom-page-form').on('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    btn.disabled = true; btn.innerHTML = '<i class="tio-loading tio-spin mr-1"></i> Saving...';
    $.ajax({
        url: $(this).attr('action'), type: 'POST',
        data: new FormData(this), processData: false, contentType: false,
        success: function () {
            toastr.success('Custom page updated successfully!');
            setTimeout(() => window.location.href = '{{ route("admin.custom-page.index") }}', 800);
        },
        error: function (xhr) {
            btn.disabled = false; btn.innerHTML = '<i class="tio-save mr-1"></i> Update Page';
            const res = xhr.responseJSON;
            if (res && res.errors) res.errors.forEach(e => toastr.error(e.message));
            else toastr.error('Something went wrong.');
        }
    });
});
</script>
@endpush
