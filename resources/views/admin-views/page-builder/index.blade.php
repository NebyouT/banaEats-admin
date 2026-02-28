@extends('layouts.admin.app')

@section('title', translate('Page Builder'))

@push('css_or_js')
<style>
.page-card {
    border: 1px solid #e7eaf3;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
    background: #fff;
}
.page-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.page-card-thumb {
    height: 140px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.page-card-thumb i {
    font-size: 48px;
    color: #dee2e6;
}
.page-card-body {
    padding: 16px;
}
.page-card-title {
    font-size: 15px;
    font-weight: 600;
    color: #1e2022;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.page-card-meta {
    font-size: 12px;
    color: #8c98a4;
}
.page-card-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    flex-wrap: wrap;
}
.page-card-actions .btn {
    padding: 6px 12px;
    font-size: 12px;
}
.status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.status-badge.published {
    background: #d4edda;
    color: #155724;
}
.status-badge.draft {
    background: #fff3cd;
    color: #856404;
}
.empty-state {
    text-align: center;
    padding: 60px 20px;
}
.empty-state i {
    font-size: 64px;
    color: #dee2e6;
    margin-bottom: 16px;
}
.empty-state h4 {
    color: #495057;
    margin-bottom: 8px;
}
.empty-state p {
    color: #8c98a4;
    margin-bottom: 20px;
}
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon">
                        <i class="tio-pages text-primary"></i>
                    </span>
                    {{ translate('Page Builder') }}
                    <span class="badge badge-soft-dark ml-2">{{ $pages->total() }}</span>
                </h1>
                <p class="page-header-description">{{ translate('Create custom pages with drag-and-drop editor') }}</p>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.page-builder.create') }}" class="btn btn-primary">
                    <i class="tio-add mr-1"></i> {{ translate('Create Page') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('admin.page-builder.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="input-label">{{ translate('Search') }}</label>
                        <input type="text" name="search" class="form-control" placeholder="{{ translate('Search by title...') }}" value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="input-label">{{ translate('Status') }}</label>
                        <select name="status" class="form-control">
                            <option value="all" {{ ($status ?? 'all') == 'all' ? 'selected' : '' }}>{{ translate('All') }}</option>
                            <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>{{ translate('Active') }}</option>
                            <option value="inactive" {{ ($status ?? '') == 'inactive' ? 'selected' : '' }}>{{ translate('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="tio-search mr-1"></i> {{ translate('Filter') }}
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.page-builder.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="tio-clear mr-1"></i> {{ translate('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pages Grid -->
    @if($pages->count() > 0)
    <div class="row g-3">
        @foreach($pages as $page)
        <div class="col-md-4 col-lg-3">
            <div class="page-card">
                <div class="page-card-thumb">
                    <i class="tio-document-text"></i>
                    <span class="status-badge {{ $page->is_published ? 'published' : 'draft' }}">
                        {{ $page->is_published ? translate('Published') : translate('Draft') }}
                    </span>
                </div>
                <div class="page-card-body">
                    <h5 class="page-card-title" title="{{ $page->title }}">{{ $page->title }}</h5>
                    <div class="page-card-meta">
                        <span><i class="tio-layers-outlined mr-1"></i>{{ $page->sections_count }} {{ translate('sections') }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $page->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="page-card-actions">
                        <a href="{{ route('admin.page-builder.edit', $page->id) }}" class="btn btn-sm btn-primary">
                            <i class="tio-edit"></i> {{ translate('Edit') }}
                        </a>
                        <a href="{{ route('admin.page-builder.preview', $page->id) }}" class="btn btn-sm btn-outline-info" target="_blank">
                            <i class="tio-visible"></i>
                        </a>
                        <a href="{{ route('admin.page-builder.duplicate', $page->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ translate('Duplicate') }}">
                            <i class="tio-copy"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePage({{ $page->id }})" title="{{ translate('Delete') }}">
                            <i class="tio-delete"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $pages->links() }}
    </div>
    @else
    <div class="card">
        <div class="empty-state">
            <i class="tio-document-text-outlined"></i>
            <h4>{{ translate('No pages yet') }}</h4>
            <p>{{ translate('Create your first custom page with our drag-and-drop builder') }}</p>
            <a href="{{ route('admin.page-builder.create') }}" class="btn btn-primary">
                <i class="tio-add mr-1"></i> {{ translate('Create Page') }}
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Delete Form -->
<form id="delete-form" action="" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('script_2')
<script>
function deletePage(id) {
    Swal.fire({
        title: '{{ translate("Are you sure?") }}',
        text: '{{ translate("This page will be permanently deleted.") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FC6A57',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ translate("Yes, delete it!") }}',
        cancelButtonText: '{{ translate("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = '{{ route("admin.page-builder.delete", "") }}/' + id;
            form.submit();
        }
    });
}
</script>
@endpush
