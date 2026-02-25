@extends('layouts.admin.app')

@section('title', translate('Manage Prizes'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{dynamicAsset('public/assets/admin/img/gift.png')}}" class="w--20" alt="">
                </span>
                <span>{{ translate('Prizes for') }}: {{ $game->name }}</span>
            </h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.gamification.edit', $game->id) }}" class="btn btn-secondary">
                    <i class="tio-back-ui"></i> {{ translate('Back to Game') }}
                </a>
                <a href="{{ route('admin.gamification.prizes.create', $game->id) }}" class="btn btn-primary">
                    <i class="tio-add"></i> {{ translate('Add Prize') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        {{ translate('Prize List') }} ({{ $game->prizes->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($game->prizes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('Position') }}</th>
                                    <th>{{ translate('Prize') }}</th>
                                    <th>{{ translate('Type') }}</th>
                                    <th>{{ translate('Value') }}</th>
                                    <th>{{ translate('Probability') }}</th>
                                    <th>{{ translate('Quantity') }}</th>
                                    <th>{{ translate('Status') }}</th>
                                    <th class="text-center">{{ translate('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="prize-list">
                                @foreach($game->prizes as $prize)
                                <tr data-id="{{ $prize->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="tio-drag-outlined cursor-move mr-2"></i>
                                            <span class="badge badge-soft-secondary">{{ $prize->position }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($prize->image_full_url)
                                            <img src="{{ $prize->image_full_url }}" class="avatar avatar-sm mr-2" alt="">
                                            @else
                                            <div class="avatar avatar-sm mr-2" style="background-color: {{ $prize->color }}"></div>
                                            @endif
                                            <div>
                                                <strong>{{ $prize->name }}</strong>
                                                @if($prize->description)
                                                <div class="text-muted small">{{ Str::limit($prize->description, 40) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-info">{{ $prize->type_name }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $prize->display_value }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 8px; max-width: 80px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $prize->probability }}%" aria-valuenow="{{ $prize->probability }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="ml-2">{{ $prize->probability }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($prize->total_quantity)
                                        <span class="badge badge-soft-{{ $prize->remaining_quantity > 0 ? 'success' : 'danger' }}">
                                            {{ $prize->remaining_quantity }} / {{ $prize->total_quantity }}
                                        </span>
                                        @else
                                        <span class="badge badge-soft-primary">{{ translate('Unlimited') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <label class="toggle-switch">
                                            <input type="checkbox" class="toggle-switch-input prize-status-toggle" 
                                                data-id="{{ $prize->id }}" 
                                                {{ $prize->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.gamification.prizes.edit', [$game->id, $prize->id]) }}" 
                                               class="btn btn-sm btn-white" 
                                               title="{{ translate('Edit') }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-white delete-prize" 
                                                    data-id="{{ $prize->id }}"
                                                    title="{{ translate('Delete') }}">
                                                <i class="tio-delete"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <img src="{{dynamicAsset('public/assets/admin/img/empty.png')}}" class="w-100px mb-3" alt="">
                        <p class="text-muted">{{ translate('No prizes added yet') }}</p>
                        <a href="{{ route('admin.gamification.prizes.create', $game->id) }}" class="btn btn-primary">
                            <i class="tio-add"></i> {{ translate('Add Your First Prize') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($game->prizes->count() > 0)
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ translate('Prize Distribution') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="prizeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ translate('Quick Stats') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>{{ translate('Total Prizes') }}:</strong> {{ $game->prizes->count() }}
                        </li>
                        <li class="mb-2">
                            <strong>{{ translate('Active Prizes') }}:</strong> {{ $game->prizes->where('status', 1)->count() }}
                        </li>
                        <li class="mb-2">
                            <strong>{{ translate('Total Probability') }}:</strong> {{ $game->prizes->sum('probability') }}%
                        </li>
                        <li class="mb-2">
                            <strong>{{ translate('Limited Quantity Prizes') }}:</strong> {{ $game->prizes->whereNotNull('total_quantity')->count() }}
                        </li>
                    </ul>
                    @if($game->prizes->sum('probability') > 100)
                    <div class="alert alert-warning mt-3">
                        <i class="tio-info"></i> {{ translate('Total probability exceeds 100%. This is OK - probabilities are weighted.') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Prize status toggle
    $('.prize-status-toggle').on('change', function() {
        let id = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: '{{ route("admin.gamification.prizes.status", $game->id) }}',
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

    // Delete prize
    $('.delete-prize').on('click', function() {
        let id = $(this).data('id');
        
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: '{{ translate("This will delete the prize permanently") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FC6A57',
            cancelButtonColor: '#363636',
            confirmButtonText: '{{ translate("Yes, delete it!") }}',
            cancelButtonText: '{{ translate("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.gamification.prizes.delete", [$game->id, ""]) }}/' + id,
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
                        toastr.error('{{ translate("Failed to delete prize") }}');
                    }
                });
            }
        });
    });

    // Sortable prizes
    @if($game->prizes->count() > 0)
    var el = document.getElementById('prize-list');
    var sortable = Sortable.create(el, {
        handle: '.cursor-move',
        animation: 150,
        onEnd: function(evt) {
            var positions = {};
            $('#prize-list tr').each(function(index) {
                var id = $(this).data('id');
                positions[id] = index;
            });
            
            $.ajax({
                url: '{{ route("admin.gamification.prizes.update-position", $game->id) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    positions: positions
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function() {
                    toastr.error('{{ translate("Failed to update positions") }}');
                }
            });
        }
    });

    // Prize distribution chart
    var ctx = document.getElementById('prizeChart').getContext('2d');
    var prizeChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($game->prizes as $prize)
                '{{ $prize->name }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($game->prizes as $prize)
                    {{ $prize->probability }},
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($game->prizes as $prize)
                    '{{ $prize->color }}',
                    @endforeach
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    @endif
});
</script>
<style>
.cursor-move {
    cursor: move;
}
</style>
@endpush
