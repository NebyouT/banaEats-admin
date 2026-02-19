@extends('layouts.admin.app')

@section('title', 'Custom Page Banners')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-image text-primary"></i></span>
                    Custom Page Banners
                </h1>
                <p class="text-muted mb-0 font-size-sm">Manage banners linked to custom pages. Two types: <strong>Square (1:1)</strong> and <strong>Wide (5:1)</strong>.</p>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.custom-page-banner.create') }}" class="btn btn-primary">
                    <i class="tio-add mr-1"></i> Add Banner
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Banner</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Linked Pages</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ $banner->image_full_url }}"
                                     alt="{{ $banner->title }}"
                                     class="rounded"
                                     style="width:{{ $banner->type === 'square' ? '60px' : '120px' }};height:60px;object-fit:cover;"
                                     onerror="this.src='{{ dynamicAsset('public/assets/admin/img/160x160/img2.jpg') }}'">
                            </td>
                            <td>
                                <span class="d-block font-weight-bold">{{ $banner->title }}</span>
                            </td>
                            <td>
                                @if($banner->type === 'square')
                                    <span class="badge badge-soft-info">Square (1:1)</span>
                                @else
                                    <span class="badge badge-soft-warning">Wide (5:1)</span>
                                @endif
                            </td>
                            <td>
                                @php $count = count($banner->page_ids ?? []); @endphp
                                <span class="badge badge-soft-secondary">{{ $count }} {{ Str::plural('page', $count) }}</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.custom-page-banner.status') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $banner->id }}">
                                    <input type="hidden" name="status" value="{{ $banner->status ? 0 : 1 }}">
                                    <button type="submit" class="btn btn-sm {{ $banner->status ? 'btn-success' : 'btn-danger' }}">
                                        {{ $banner->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.custom-page-banner.edit', $banner) }}" class="btn btn-sm btn-white mr-1" title="Edit">
                                    <i class="tio-edit"></i>
                                </a>
                                <form action="{{ route('admin.custom-page-banner.delete', $banner) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this banner?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white text-danger" title="Delete">
                                        <i class="tio-delete"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="tio-image" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No banners yet. <a href="{{ route('admin.custom-page-banner.create') }}">Add one</a>.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($banners->hasPages())
        <div class="card-footer">
            {{ $banners->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
