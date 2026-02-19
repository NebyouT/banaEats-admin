@extends('layouts.admin.app')

@section('title', 'Edit Custom Page Banner')

@push('css_or_js')
<style>
.type-card { border: 2px solid #e7eaf3; border-radius: 10px; cursor: pointer; padding: 20px 16px; text-align: center; transition: border-color .15s, background .15s; }
.type-card:hover { border-color: #FC6A57; }
.type-card.selected { border-color: #FC6A57; background: #fff5f4; }
.type-card .type-icon { font-size: 2rem; margin-bottom: 8px; }
.type-card .type-label { font-weight: 700; font-size: 15px; }
.type-card .type-ratio { font-size: 12px; color: #8c98a4; margin-top: 4px; }
.type-card .type-hint { font-size: 11px; color: #8c98a4; margin-top: 6px; }
.preview-box { border: 2px dashed #e7eaf3; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; overflow: hidden; transition: all .2s; }
.preview-box img, .preview-box video { width: 100%; height: 100%; object-fit: cover; }
.media-type-badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-image { background: #e8f4fd; color: #1a73e8; }
.badge-gif   { background: #fef3e2; color: #f57c00; }
.badge-video { background: #e8f5e9; color: #2e7d32; }
</style>
@endpush

@section('content')
@php
    $isSquare   = $custom_page_banner->type === 'square';
    $mediaType  = $custom_page_banner->media_type ?? 'image';
    $mediaUrl   = $custom_page_banner->image_full_url;
@endphp
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-edit text-primary"></i></span>
                    Edit Custom Page Banner
                </h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.custom-page-banner.index') }}" class="btn btn-outline-secondary">
                    <i class="tio-arrow-backward mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <form id="banner-form" action="{{ route('admin.custom-page-banner.update', $custom_page_banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- LEFT --}}
            <div class="col-lg-8">

                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0">Banner Details</h5></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="input-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ $custom_page_banner->title }}" required>
                        </div>

                        <div class="form-group mb-0">
                            <label class="input-label">Banner Type <span class="text-danger">*</span></label>
                            <p class="text-muted font-size-sm mb-3">Your Flutter app reads <code>type</code> to decide how to render this banner.</p>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="type-card {{ $isSquare ? 'selected' : '' }}" id="type-square-card" onclick="selectType('square')">
                                        <div class="type-icon">⬛</div>
                                        <div class="type-label">Square</div>
                                        <div class="type-ratio">1 : 1 ratio</div>
                                        <div class="type-hint">Min recommended: 600 × 600 px</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="type-card {{ !$isSquare ? 'selected' : '' }}" id="type-wide-card" onclick="selectType('wide')">
                                        <div class="type-icon">▬</div>
                                        <div class="type-label">Wide</div>
                                        <div class="type-ratio">5 : 1 ratio</div>
                                        <div class="type-hint">Min recommended: 1500 × 300 px</div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="type" id="type-input" value="{{ $custom_page_banner->type }}">
                        </div>
                    </div>
                </div>

                {{-- Linked Page (single) --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="tio-pages-outlined mr-1 text-primary"></i>
                            Link to a Custom Page
                            <small class="text-muted ml-1">(tapping the banner navigates here)</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($pages->isEmpty())
                            <p class="text-muted mb-0">No active custom pages found.</p>
                        @else
                            <select name="page_id" class="form-control select2" id="page-select">
                                <option value="">— No linked page —</option>
                                @foreach($pages as $page)
                                    <option value="{{ $page->id }}" {{ $custom_page_banner->page_id == $page->id ? 'selected' : '' }}>
                                        {{ $page->title }} ({{ $page->slug }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">When a user taps this banner in the app, they will be taken directly to the selected page.</small>
                        @endif
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Banner Media
                            <span class="media-type-badge badge-{{ $mediaType }} ml-2" id="media-badge">{{ $mediaType }}</span>
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div id="preview-box" class="preview-box mx-auto mb-3"
                             style="width:{{ $isSquare ? '160px' : '100%' }};height:{{ $isSquare ? '160px' : '80px' }};">
                            @if($mediaType === 'video')
                                <video id="preview-video" src="{{ $mediaUrl }}" autoplay muted loop playsinline
                                       onerror="this.style.display='none'"></video>
                                <img id="preview-img" style="display:none;" alt="">
                            @else
                                <img id="preview-img"
                                     src="{{ $mediaUrl }}"
                                     alt="{{ $custom_page_banner->title }}"
                                     onerror="this.src='{{ dynamicAsset('public/assets/admin/img/160x160/img2.jpg') }}'">
                                <video id="preview-video" style="display:none;" autoplay muted loop playsinline></video>
                            @endif
                        </div>
                        <label class="btn btn-outline-primary btn-block" for="media-input">
                            <i class="tio-upload mr-1"></i> Change Media
                        </label>
                        <input type="file" name="media" id="media-input" class="d-none"
                               accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime">
                        <div class="mt-2">
                            <small class="text-muted" id="size-hint">
                                {{ $isSquare ? 'Square (1:1): min 600 × 600 px' : 'Wide (5:1): min 1500 × 300 px' }}
                            </small>
                        </div>
                        <div class="mt-2 text-left">
                            <small class="text-muted">
                                <strong>Accepted:</strong> JPG, PNG, WebP, GIF, MP4, WebM, MOV<br>
                                <strong>Max size:</strong> 20 MB — leave empty to keep current media
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0">Status</h5></div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="font-size-sm">Currently
                                <strong class="{{ $custom_page_banner->status ? 'text-success' : 'text-danger' }}">
                                    {{ $custom_page_banner->status ? 'Active' : 'Inactive' }}
                                </strong>
                            </span>
                            <form action="{{ route('admin.custom-page-banner.status') }}" method="POST" class="mb-0">
                                @csrf
                                <input type="hidden" name="id" value="{{ $custom_page_banner->id }}">
                                <input type="hidden" name="status" value="{{ $custom_page_banner->status ? 0 : 1 }}">
                                <button type="submit" class="btn btn-sm {{ $custom_page_banner->status ? 'btn-danger' : 'btn-success' }}">
                                    {{ $custom_page_banner->status ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block" id="save-btn">
                            <i class="tio-save mr-1"></i> Update Banner
                        </button>
                        <a href="{{ route('admin.custom-page-banner.index') }}" class="btn btn-outline-secondary btn-block mt-2">Cancel</a>
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

function selectType(type) {
    document.getElementById('type-input').value = type;
    document.getElementById('type-square-card').classList.toggle('selected', type === 'square');
    document.getElementById('type-wide-card').classList.toggle('selected', type === 'wide');
    const box = document.getElementById('preview-box');
    if (type === 'square') {
        box.style.width  = '160px';
        box.style.height = '160px';
        document.getElementById('size-hint').textContent = 'Square (1:1): min 600 × 600 px';
    } else {
        box.style.width  = '100%';
        box.style.height = '80px';
        document.getElementById('size-hint').textContent = 'Wide (5:1): min 1500 × 300 px';
    }
}

document.getElementById('media-input').addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;
    const file  = this.files[0];
    const mime  = file.type;
    const url   = URL.createObjectURL(file);
    const img   = document.getElementById('preview-img');
    const vid   = document.getElementById('preview-video');
    const badge = document.getElementById('media-badge');

    img.style.display = 'none';
    vid.style.display = 'none';

    if (mime === 'image/gif') {
        img.src = url; img.style.display = 'block';
        badge.textContent = 'gif'; badge.className = 'media-type-badge badge-gif ml-2';
    } else if (mime.startsWith('video/')) {
        vid.src = url; vid.style.display = 'block';
        badge.textContent = 'video'; badge.className = 'media-type-badge badge-video ml-2';
    } else {
        img.src = url; img.style.display = 'block';
        badge.textContent = 'image'; badge.className = 'media-type-badge badge-image ml-2';
    }
});

$('#banner-form').on('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="tio-loading tio-spin mr-1"></i> Saving...';
    $.ajax({
        url: $(this).attr('action'), type: 'POST',
        data: new FormData(this), processData: false, contentType: false,
        success: function () {
            toastr.success('Banner updated successfully!');
            setTimeout(() => window.location.href = '{{ route("admin.custom-page-banner.index") }}', 800);
        },
        error: function (xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="tio-save mr-1"></i> Update Banner';
            const res = xhr.responseJSON;
            if (res && res.errors) res.errors.forEach(function(err) { toastr.error(err.message); });
            else toastr.error('Something went wrong.');
        }
    });
});
</script>
@endpush
