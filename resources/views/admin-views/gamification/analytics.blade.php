@extends('layouts.admin.app')

@section('title', translate('Game Analytics'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{dynamicAsset('public/assets/admin/img/analytics.png')}}" class="w--20" alt="">
                </span>
                <span>{{ translate('Analytics') }}: {{ $game->name }}</span>
            </h1>
            <a href="{{ route('admin.gamification.index') }}" class="btn btn-secondary">
                <i class="tio-back-ui"></i> {{ translate('Back to Games') }}
            </a>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-hover-shadow h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2">{{ translate('Total Plays') }}</h6>
                    <div class="row align-items-center gx-2 mb-1">
                        <div class="col-12">
                            <h2 class="card-title text-primary">{{ $totalPlays }}</h2>
                        </div>
                    </div>
                    <span class="badge badge-soft-secondary">
                        <i class="tio-game"></i> {{ translate('All time') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-hover-shadow h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2">{{ translate('Total Winners') }}</h6>
                    <div class="row align-items-center gx-2 mb-1">
                        <div class="col-12">
                            <h2 class="card-title text-success">{{ $totalWinners }}</h2>
                        </div>
                    </div>
                    <span class="badge badge-soft-success">
                        @if($totalPlays > 0)
                            {{ number_format(($totalWinners / $totalPlays) * 100, 1) }}% {{ translate('win rate') }}
                        @else
                            0% {{ translate('win rate') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-hover-shadow h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2">{{ translate('Prizes Claimed') }}</h6>
                    <div class="row align-items-center gx-2 mb-1">
                        <div class="col-12">
                            <h2 class="card-title text-info">{{ $totalClaimed }}</h2>
                        </div>
                    </div>
                    <span class="badge badge-soft-info">
                        @if($totalWinners > 0)
                            {{ number_format(($totalClaimed / $totalWinners) * 100, 1) }}% {{ translate('claimed') }}
                        @else
                            0% {{ translate('claimed') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-hover-shadow h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2">{{ translate('Unique Players') }}</h6>
                    <div class="row align-items-center gx-2 mb-1">
                        <div class="col-12">
                            <h2 class="card-title text-warning">{{ $uniquePlayers }}</h2>
                        </div>
                    </div>
                    <span class="badge badge-soft-warning">
                        <i class="tio-user"></i> {{ translate('Players') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">{{ translate('Prize Distribution') }}</h5>
                </div>
                <div class="card-body">
                    @if($prizeDistribution->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ translate('Prize') }}</th>
                                        <th>{{ translate('Type') }}</th>
                                        <th class="text-center">{{ translate('Won') }}</th>
                                        <th class="text-center">{{ translate('Percentage') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prizeDistribution as $dist)
                                        @if($dist->prize)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm mr-2" style="background-color: {{ $dist->prize->color ?? '#8DC63F' }}">
                                                        <span class="avatar-initials text-white">
                                                            <i class="tio-gift"></i>
                                                        </span>
                                                    </div>
                                                    <span class="font-weight-bold">{{ $dist->prize->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-primary">{{ $dist->prize->type_name }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="font-weight-bold">{{ $dist->count }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-muted">
                                                    {{ number_format(($dist->count / $totalWinners) * 100, 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{dynamicAsset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image" class="img-fluid mb-3" style="width: 7rem;">
                            <p class="text-muted">{{ translate('No prizes won yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">{{ translate('Recent Plays') }}</h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @if($recentPlays->count() > 0)
                        <ul class="list-unstyled list-unstyled-py-3">
                            @foreach($recentPlays as $play)
                            <li>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm avatar-circle mr-3">
                                        <img class="avatar-img" src="{{ $play->user?->image_full_url ?? dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}" alt="">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                {{ $play->user?->f_name ?? 'Guest' }} {{ $play->user?->l_name ?? '' }}
                                            </h6>
                                            <small class="text-muted">{{ $play->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($play->is_winner && $play->prize)
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="badge badge-soft-success mr-2">
                                                    <i class="tio-checkmark-circle"></i> {{ translate('Won') }}
                                                </span>
                                                <small class="text-dark font-weight-bold">{{ $play->prize->name }}</small>
                                                @if($play->is_claimed)
                                                    <span class="badge badge-soft-info ml-2">
                                                        <i class="tio-done"></i> {{ translate('Claimed') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge badge-soft-secondary mt-1">
                                                <i class="tio-clear"></i> {{ translate('No win') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <img src="{{dynamicAsset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image" class="img-fluid mb-3" style="width: 7rem;">
                            <p class="text-muted">{{ translate('No plays yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
