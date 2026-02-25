@extends('layouts.admin.app')

@section('title', translate('Edit Game'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{dynamicAsset('public/assets/admin/img/game.png')}}" class="w--20" alt="">
            </span>
            <span>{{ translate('Edit Game') }}: {{ $game->name }}</span>
        </h1>
    </div>

    <form action="{{ route('admin.gamification.update', $game->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-2">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('Game Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Game Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $game->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Game Type') }} <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="spin_wheel" {{ $game->type == 'spin_wheel' ? 'selected' : '' }}>{{ translate('Spin the Wheel') }}</option>
                                        <option value="scratch_card" {{ $game->type == 'scratch_card' ? 'selected' : '' }}>{{ translate('Scratch Card') }}</option>
                                        <option value="slot_machine" {{ $game->type == 'slot_machine' ? 'selected' : '' }}>{{ translate('Slot Machine') }}</option>
                                        <option value="mystery_box" {{ $game->type == 'mystery_box' ? 'selected' : '' }}>{{ translate('Mystery Box') }}</option>
                                        <option value="decision_roulette" {{ $game->type == 'decision_roulette' ? 'selected' : '' }}>{{ translate('Decision Roulette') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3">{{ $game->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Instructions') }}</label>
                            <textarea name="instructions" class="form-control" rows="2">{{ $game->instructions }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Plays Per Day') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="plays_per_day" class="form-control" value="{{ $game->plays_per_day }}" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Plays Per Week') }}</label>
                                    <input type="number" name="plays_per_week" class="form-control" value="{{ $game->plays_per_week }}" min="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Cooldown (Minutes)') }}</label>
                                    <input type="number" name="cooldown_minutes" class="form-control" value="{{ $game->cooldown_minutes }}" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Start Date') }}</label>
                                    <input type="datetime-local" name="start_date" class="form-control" value="{{ $game->start_date ? $game->start_date->format('Y-m-d\TH:i') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('End Date') }}</label>
                                    <input type="datetime-local" name="end_date" class="form-control" value="{{ $game->end_date ? $game->end_date->format('Y-m-d\TH:i') : '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Button Text') }}</label>
                            <input type="text" name="button_text" class="form-control" value="{{ $game->button_text }}">
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Priority Order') }}</label>
                            <input type="number" name="priority" class="form-control" value="{{ $game->priority }}" min="0">
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('Visual Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Primary Color') }}</label>
                                    <input type="color" name="primary_color" class="form-control" value="{{ $game->display_settings['primary_color'] ?? '#8DC63F' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Secondary Color') }}</label>
                                    <input type="color" name="secondary_color" class="form-control" value="{{ $game->display_settings['secondary_color'] ?? '#F5D800' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Text Color') }}</label>
                                    <input type="color" name="text_color" class="form-control" value="{{ $game->display_settings['text_color'] ?? '#1A1A1A' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Background Image') }}</label>
                            @if($game->background_image_full_url)
                            <div class="mb-2">
                                <img src="{{ $game->background_image_full_url }}" class="img-fluid rounded" style="max-height: 200px;" alt="">
                            </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" name="background_image" class="custom-file-input" accept="image/*" id="background_image">
                                <label class="custom-file-label" for="background_image">{{ translate('Choose new file') }}</label>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <img id="background_preview" src="" class="img-fluid rounded" style="max-height: 200px; display: none;" alt="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('Game Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="toggle-switch d-flex align-items-center mb-3">
                                <input type="checkbox" name="status" class="toggle-switch-input" {{ $game->status ? 'checked' : '' }}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                                <span class="toggle-switch-content ml-2">
                                    <span class="d-block">{{ translate('Active') }}</span>
                                </span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="toggle-switch d-flex align-items-center mb-3">
                                <input type="checkbox" name="first_play_always_wins" class="toggle-switch-input" {{ $game->first_play_always_wins ? 'checked' : '' }}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                                <span class="toggle-switch-content ml-2">
                                    <span class="d-block">{{ translate('First Play Always Wins') }}</span>
                                </span>
                            </label>
                        </div>

                        <hr>

                        <div class="alert alert-soft-info">
                            <i class="tio-info"></i>
                            <strong>{{ translate('Statistics') }}</strong>
                            <ul class="mb-0 small pl-3">
                                <li>{{ translate('Prizes') }}: {{ $game->prizes->count() }}</li>
                                <li>{{ translate('Total Plays') }}: {{ $game->gamePlays->count() }}</li>
                                <li>{{ translate('Eligibility Rules') }}: {{ $game->eligibilityRules->count() }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="tio-save"></i> {{ translate('Update Game') }}
                            </button>
                            <a href="{{ route('admin.gamification.preview', $game->id) }}" class="btn btn-warning" target="_blank">
                                <i class="tio-visible"></i> {{ translate('Preview Game') }}
                            </a>
                            <a href="{{ route('admin.gamification.prizes.index', $game->id) }}" class="btn btn-info">
                                <i class="tio-gift"></i> {{ translate('Manage Prizes') }} ({{ $game->prizes->count() }})
                            </a>
                            <a href="{{ route('admin.gamification.analytics', $game->id) }}" class="btn btn-success">
                                <i class="tio-chart-bar-4"></i> {{ translate('View Analytics') }}
                            </a>
                            <a href="{{ route('admin.gamification.index') }}" class="btn btn-secondary">
                                <i class="tio-back-ui"></i> {{ translate('Back to List') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script_2')
<script>
$(document).ready(function() {
    $('#background_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#background_preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
            $(this).next('.custom-file-label').text(file.name);
        }
    });
});
</script>
@endpush
