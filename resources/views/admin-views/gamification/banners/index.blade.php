@extends('layouts.admin.app')

@section('title', translate('Gamification Banners'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-header-title">
                <span class="page-header-icon"><img src="{{dynamicAsset('public/assets/admin/img/banner.png')}}" class="w--20" alt=""></span>
                <span>{{ translate('Gamification Banners') }} <span class="badge badge-soft-dark">{{ $banners->total() }}</span></span>
            </h1>
            <a href="{{ route('admin.gamification.banners.create') }}" class="btn btn-primary"><i class="tio-add"></i> {{ translate('Add Banner') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless table-thead-bordered table-align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('#') }}</th>
                            <th>{{ translate('Image') }}</th>
                            <th>{{ translate('Title') }}</th>
                            <th>{{ translate('Linked Game') }}</th>
                            <th>{{ translate('Placement') }}</th>
                            <th>{{ translate('Status') }}</th>
                            <th class="text-center">{{ translate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $key => $banner)
                        <tr>
                            <td>{{ $banners->firstItem() + $key }}</td>
                            <td>
                                @if($banner->image_full_url)
                                <img src="{{ $banner->image_full_url }}" class="rounded" width="80" height="40" style="object-fit:cover" alt="">
                                @else
                                <div style="width:80px;height:40px;background:{{ $banner->background_color }};border-radius:6px;display:flex;align-items:center;justify-content:center;color:{{ $banner->text_color }};font-size:10px;font-weight:700">{{ Str::limit($banner->title, 12) }}</div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight:600">{{ $banner->title }}</div>
                                @if($banner->subtitle)<small class="text-muted">{{ $banner->subtitle }}</small>@endif
                            </td>
                            <td>
                                @if($banner->game)
                                <span class="badge badge-soft-info">{{ $banner->game->name }}</span>
                                @else
                                <span class="badge badge-soft-danger">{{ translate('Deleted') }}</span>
                                @endif
                            </td>
                            <td><span class="badge badge-soft-primary">{{ ucfirst($banner->placement) }}</span></td>
                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" class="toggle-switch-input banner-status" data-id="{{ $banner->id }}" {{ $banner->status ? 'checked' : '' }}>
                                    <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                </label>
                            </td>
                            <td class="text-center">
                                <div class="btn--container justify-content-center">
                                    <a href="{{ route('admin.gamification.banners.edit', $banner->id) }}" class="action-btn" title="{{ translate('Edit') }}"><i class="tio-edit"></i></a>
                                    <a href="javascript:" class="action-btn btn--danger btn-outline-danger delete-banner" data-id="{{ $banner->id }}" title="{{ translate('Delete') }}"><i class="tio-delete-outlined"></i></a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">{{ translate('No banners found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($banners->hasPages())
        <div class="card-footer">{{ $banners->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('script_2')
<script>
$(document).ready(function(){
    $('.banner-status').on('change', function(){
        $.ajax({
            url: '{{ route("admin.gamification.banners.status") }}',
            type: 'POST',
            data: {_token:'{{ csrf_token() }}', id: $(this).data('id'), status: $(this).is(':checked') ? 1 : 0},
            success: function(r){ toastr.success(r.message); },
            error: function(){ toastr.error('{{ translate("Failed") }}'); }
        });
    });
    $('.delete-banner').on('click', function(){
        var id = $(this).data('id');
        Swal.fire({title:'{{ translate("Are you sure?") }}',text:'{{ translate("This banner will be deleted.") }}',showCancelButton:true,confirmButtonColor:'#d33',confirmButtonText:'{{ translate("Delete") }}'}).then(function(result){
            if(result.isConfirmed){
                $.ajax({
                    url: '{{ url("admin/gamification/banners/delete") }}/'+id,
                    type: 'DELETE',
                    data: {_token:'{{ csrf_token() }}'},
                    success: function(){ location.reload(); },
                    error: function(){ toastr.error('{{ translate("Failed") }}'); }
                });
            }
        });
    });
});
</script>
@endpush
