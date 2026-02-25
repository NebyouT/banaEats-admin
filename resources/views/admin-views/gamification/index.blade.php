@extends('layouts.admin.app')

@section('title', translate('Gamification Games'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{dynamicAsset('public/assets/admin/img/game.png')}}" class="w--20" alt="">
            </span>
            <span>{{ translate('Gamification Games') }}</span>
        </h1>
        <a href="{{ route('admin.gamification.create') }}" class="btn btn-primary">
            <i class="tio-add"></i> {{ translate('Add New Game') }}
        </a>
    </div>

    <div class="card">
        <div class="card-header border-0">
            <div class="search--button-wrapper justify-content-end">
                <form class="search-form">
                    <div class="input-group input--group">
                        <input type="search" name="search" class="form-control" placeholder="{{ translate('Search by name') }}" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i class="tio-search"></i></button>
                    </div>
                </form>
                <div class="hs-unfold">
                    <select name="type" class="form-control js-select2-custom" onchange="location.href='?type='+this.value+'&search={{ request('search') }}'">
                        <option value="">{{ translate('All Types') }}</option>
                        <option value="spin_wheel" {{ request('type') == 'spin_wheel' ? 'selected' : '' }}>{{ translate('Spin the Wheel') }}</option>
                        <option value="scratch_card" {{ request('type') == 'scratch_card' ? 'selected' : '' }}>{{ translate('Scratch Card') }}</option>
                        <option value="slot_machine" {{ request('type') == 'slot_machine' ? 'selected' : '' }}>{{ translate('Slot Machine') }}</option>
                        <option value="mystery_box" {{ request('type') == 'mystery_box' ? 'selected' : '' }}>{{ translate('Mystery Box') }}</option>
                        <option value="decision_roulette" {{ request('type') == 'decision_roulette' ? 'selected' : '' }}>{{ translate('Decision Roulette') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>{{ translate('ID') }}</th>
                        <th>{{ translate('Game Name') }}</th>
                        <th>{{ translate('Type') }}</th>
                        <th>{{ translate('Prizes') }}</th>
                        <th>{{ translate('Total Plays') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th>{{ translate('Schedule') }}</th>
                        <th class="text-center">{{ translate('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($games as $game)
                    <tr>
                        <td>{{ $game->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($game->background_image_full_url)
                                <img src="{{ $game->background_image_full_url }}" class="avatar avatar-sm mr-2" alt="">
                                @endif
                                <div>
                                    <strong>{{ $game->name }}</strong>
                                    <div class="text-muted small">{{ Str::limit($game->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-soft-info">{{ $game->type_name }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.gamification.prizes.index', $game->id) }}" class="btn btn-sm btn-outline-primary">
                                {{ $game->prizes_count }} {{ translate('Prizes') }}
                            </a>
                        </td>
                        <td>
                            <strong>{{ $game->game_plays_count }}</strong>
                        </td>
                        <td>
                            <label class="toggle-switch">
                                <input type="checkbox" class="toggle-switch-input status-toggle" 
                                    data-id="{{ $game->id }}" 
                                    {{ $game->status ? 'checked' : '' }}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </td>
                        <td>
                            @if($game->start_date || $game->end_date)
                            <div class="small">
                                @if($game->start_date)
                                <div>{{ translate('Start') }}: {{ $game->start_date->format('M d, Y') }}</div>
                                @endif
                                @if($game->end_date)
                                <div>{{ translate('End') }}: {{ $game->end_date->format('M d, Y') }}</div>
                                @endif
                            </div>
                            @else
                            <span class="text-muted">{{ translate('Always Active') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.gamification.analytics', $game->id) }}" 
                                   class="btn btn-sm btn-white" 
                                   title="{{ translate('Analytics') }}">
                                    <i class="tio-chart-bar-4"></i>
                                </a>
                                <a href="{{ route('admin.gamification.prizes.index', $game->id) }}" 
                                   class="btn btn-sm btn-white" 
                                   title="{{ translate('Manage Prizes') }}">
                                    <i class="tio-gift"></i>
                                </a>
                                <a href="{{ route('admin.gamification.edit', $game->id) }}" 
                                   class="btn btn-sm btn-white" 
                                   title="{{ translate('Edit') }}">
                                    <i class="tio-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-white delete-game" 
                                        data-id="{{ $game->id }}"
                                        title="{{ translate('Delete') }}">
                                    <i class="tio-delete"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <img src="{{dynamicAsset('public/assets/admin/img/empty.png')}}" class="w-100px mb-3" alt="">
                            <p class="text-muted">{{ translate('No games found') }}</p>
                            <a href="{{ route('admin.gamification.create') }}" class="btn btn-primary">
                                {{ translate('Create Your First Game') }}
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {!! $games->links() !!}
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
$(document).ready(function() {
    $('.status-toggle').on('change', function() {
        let id = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: '{{ route("admin.gamification.status") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                status: status
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function() {
                toastr.error('{{ translate("Failed to update status") }}');
            }
        });
    });

    $('.delete-game').on('click', function() {
        let id = $(this).data('id');
        
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: '{{ translate("This will delete the game and all its prizes and play history") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FC6A57',
            cancelButtonColor: '#363636',
            confirmButtonText: '{{ translate("Yes, delete it!") }}',
            cancelButtonText: '{{ translate("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.gamification.delete", "") }}/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.success) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('{{ translate("Failed to delete game") }}');
                    }
                });
            }
        });
    });
});
</script>
@endpush
