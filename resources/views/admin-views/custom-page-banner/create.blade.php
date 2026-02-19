@extends('layouts.admin.app')

@section('title', 'Add Custom Page Banner')

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
.preview-box img, .preview-box video { width: 100%; height: 100%; object-fit: cover; display: none; }
.preview-placeholder { color: #8c98a4; font-size: 13px; text-align: center; padding: 20px; }
.media-type-badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-image { background: #e8f4fd; color: #1a73e8; }
.badge-gif   { background: #fef3e2; color: #f57c00; }
.badge-video { background: #e8f5e9; color: #2e7d32; }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-add text-primary"></i></span>
                    Add Custom Page Banner
                </h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.custom-page-banner.index') }}" class="btn btn-outline-secondary">
                    <i class="tio-arrow-backward mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <form id="banner-form" action="{{ route('admin.custom-page-banner.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- LEFT --}}
            <div class="col-lg-8">

                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0">Banner Details</h5></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="input-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Summer Deals Banner" required>
                        </div>

                        <div class="form-group mb-0">
                            <label class="input-label">Banner Type <span class="text-danger">*</span></label>
                            <p class="text-muted font-size-sm mb-3">Your Flutter app uses <code>type</code> to decide the aspect ratio for rendering.</p>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="type-card" id="type-square-card" onclick="selectType('square')">
                                        <div class="type-icon">⬛</div>
                                        <div class="type-label">Square</div>
                                        <div class="type-ratio">1 : 1 ratio</div>
                                        <div class="type-hint">Min recommended: 600 × 600 px</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="type-card selected" id="type-wide-card" onclick="selectType('wide')">
                                        <div class="type-icon">▬</div>
                                        <div class="type-label">Wide</div>
                                        <div class="type-ratio">5 : 1 ratio</div>
                                        <div class="type-hint">Min recommended: 1500 × 300 px</div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="type" id="type-input" value="wide">
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
                            <p class="text-muted mb-0">No active custom pages found. <a href="{{ route('admin.custom-page.create') }}">Create one first</a>.</p>
                        @else
                            <select name="page_id" class="form-control select2" id="page-select">
                                <option value="">— No linked page —</option>
                                @foreach($pages as $page)
                                    <option value="{{ $page->id }}">{{ $page->title }} <span style="color:#8c98a4">({{ $page->slug }})</span></option>
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
                            Banner Media <span class="text-danger">*</span>
                            <span class="media-type-badge badge-image ml-2" id="media-badge">image</span>
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div id="preview-box" class="preview-box mx-auto mb-3" style="width:100%;height:80px;">
                            <img id="preview-img" alt="preview">
                            <video id="preview-video" autoplay muted loop playsinline></video>
                            <div class="preview-placeholder" id="preview-placeholder">
                                <i class="tio-image" style="font-size:2rem;"></i>
                                <p class="mt-1 mb-0 font-size-sm">Upload to preview</p>
                            </div>
                        </div>
                        <label class="btn btn-outline-primary btn-block" for="media-input">
                            <i class="tio-upload mr-1"></i> Choose Image / GIF / Video
                        </label>
                        <input type="file" name="media" id="media-input" class="d-none"
                               accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime" required>
                        <div class="mt-2">
                            <small class="text-muted" id="size-hint">Wide (5:1): min 1500 × 300 px</small>
                        </div>
                        <div class="mt-2 text-left">
                            <small class="text-muted">
                                <strong>Accepted:</strong> JPG, PNG, WebP, GIF, MP4, WebM, MOV<br>
                                <strong>Max size:</strong> 20 MB
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block" id="save-btn">
                            <i class="tio-save mr-1"></i> Save Banner
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
    const file = this.files[0];
    const mime = file.type;
    const url  = URL.createObjectURL(file);
    const img  = document.getElementById('preview-img');
    const vid  = document.getElementById('preview-video');
    const ph   = document.getElementById('preview-placeholder');
    const badge = document.getElementById('media-badge');

    img.style.display = 'none';
    vid.style.display = 'none';
    ph.style.display  = 'none';

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
            toastr.success('Banner created successfully!');
            setTimeout(() => window.location.href = '{{ route("admin.custom-page-banner.index") }}', 800);
        },
        error: function (xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="tio-save mr-1"></i> Save Banner';
            const res = xhr.responseJSON;
            if (res && res.errors) res.errors.forEach(function(err) { toastr.error(err.message); });
            else toastr.error('Something went wrong.');
        }
    });
});
</script>
@endpush
