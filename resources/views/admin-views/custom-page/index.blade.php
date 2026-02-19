@extends('layouts.admin.app')

@section('title', translate('messages.custom_pages'))

@push('css_or_js')
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon">
                        <img src="{{ asset('public/assets/admin/img/custom-page.png') }}" class="w--26" onerror="this.src='{{ asset('public/assets/admin/img/banner.png') }}'">
                    </span>
                    {{ translate('messages.custom_pages') }}
                    <span class="badge badge-soft-dark ml-2">{{ $pages->total() }}</span>
                </h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.custom-page.create') }}" class="btn btn-primary">
                    <i class="tio-add mr-1"></i>{{ translate('messages.add_custom_page') }}
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
                            <th>{{ translate('messages.#') }}</th>
                            <th>{{ translate('messages.title') }}</th>
                            <th>{{ translate('messages.slug') }}</th>
                            <th class="text-center">{{ translate('messages.products') }}</th>
                            <th class="text-center">{{ translate('messages.restaurants') }}</th>
                            <th class="text-center">{{ translate('messages.status') }}</th>
                            <th class="text-center">{{ translate('messages.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $index => $page)
                        <tr>
                            <td>{{ $pages->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($page->background_image)
                                    <img src="{{ $page->background_image_full_url }}"
                                         class="avatar avatar-sm avatar-circle mr-2"
                                         onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'">
                                    @endif
                                    <div>
                                        <span class="d-block font-size-sm text-body">{{ $page->title }}</span>
                                        @if($page->subtitle)
                                        <small class="text-muted">{{ Str::limit($page->subtitle, 40) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $page->slug }}</code></td>
                            <td class="text-center">
                                <span class="badge badge-soft-info">{{ count($page->product_ids ?? []) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-soft-warning">{{ count($page->restaurant_ids ?? []) }}</span>
                            </td>
                            <td class="text-center">
                                <label class="toggle-switch toggle-switch-sm" for="status_{{ $page->id }}">
                                    <input type="checkbox" class="toggle-switch-input"
                                           id="status_{{ $page->id }}"
                                           {{ $page->status ? 'checked' : '' }}
                                           onchange="updateStatus({{ $page->id }}, this.checked ? 1 : 0)">
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-white" href="{{ route('admin.custom-page.edit', $page) }}"
                                   title="{{ translate('messages.edit') }}">
                                    <i class="tio-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-white text-danger"
                                        onclick="deleteConfirm('{{ route('admin.custom-page.delete', $page) }}')"
                                        title="{{ translate('messages.delete') }}">
                                    <i class="tio-delete-outlined"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <img src="{{ asset('public/assets/admin/img/empty.png') }}" class="mb-3 w--80" alt="">
                                <p class="mb-0 text-muted">{{ translate('messages.no_custom_pages_found') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pages->hasPages())
        <div class="card-footer">
            {{ $pages->links() }}
        </div>
        @endif
    </div>
</div>

<form id="delete-form" action="" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('script_2')
<script>
    function deleteConfirm(url) {
        Swal.fire({
            title: '{{ translate("messages.are_you_sure") }}',
            text: '{{ translate("messages.you_wont_be_able_to_revert_this") }}',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FC6A57',
            cancelButtonColor: '#363636',
            confirmButtonText: '{{ translate("messages.yes_delete_it") }}',
            cancelButtonText: '{{ translate("messages.cancel") }}',
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form').action = url;
                document.getElementById('delete-form').submit();
            }
        });
    }

    function updateStatus(id, status) {
        $.ajax({
            url: '{{ route("admin.custom-page.status") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', id: id, status: status },
            success: function () {
                toastr.success('{{ translate("messages.status_updated_successfully") }}');
            },
            error: function () {
                toastr.error('{{ translate("messages.something_went_wrong") }}');
            }
        });
    }
</script>
@endpush
