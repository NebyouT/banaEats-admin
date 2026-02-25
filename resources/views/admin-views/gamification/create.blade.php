@extends('layouts.admin.app')

@section('title', translate('Create Game'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{dynamicAsset('public/assets/admin/img/game.png')}}" class="w--20" alt="">
            </span>
            <span>{{ translate('Create New Game') }}</span>
        </h1>
    </div>

    <form action="{{ route('admin.gamification.store') }}" method="POST" enctype="multipart/form-data">
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
                                    <input type="text" name="name" class="form-control" placeholder="{{ translate('e.g., Weekend Spin') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Game Type') }} <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="">{{ translate('Select Type') }}</option>
                                        <option value="spin_wheel">{{ translate('Spin the Wheel') }}</option>
                                        <option value="scratch_card">{{ translate('Scratch Card') }}</option>
                                        <option value="slot_machine">{{ translate('Slot Machine') }}</option>
                                        <option value="mystery_box">{{ translate('Mystery Box') }}</option>
                                        <option value="decision_roulette">{{ translate('Decision Roulette') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="{{ translate('Describe your game...') }}"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Instructions') }}</label>
                            <textarea name="instructions" class="form-control" rows="2" placeholder="{{ translate('How to play...') }}"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Plays Per Day') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="plays_per_day" class="form-control" value="1" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Plays Per Week') }}</label>
                                    <input type="number" name="plays_per_week" class="form-control" min="1" placeholder="{{ translate('Optional') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Cooldown (Minutes)') }}</label>
                                    <input type="number" name="cooldown_minutes" class="form-control" value="0" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Start Date') }}</label>
                                    <input type="datetime-local" name="start_date" class="form-control">
                                    <small class="text-muted">{{ translate('Leave empty for immediate start') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('End Date') }}</label>
                                    <input type="datetime-local" name="end_date" class="form-control">
                                    <small class="text-muted">{{ translate('Leave empty for no expiry') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Button Text') }}</label>
                            <input type="text" name="button_text" class="form-control" value="Play Now" placeholder="{{ translate('Play Now') }}">
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Priority Order') }}</label>
                            <input type="number" name="priority" class="form-control" value="0" min="0">
                            <small class="text-muted">{{ translate('Higher priority games appear first') }}</small>
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
                                    <input type="color" name="primary_color" class="form-control" value="#8DC63F">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Secondary Color') }}</label>
                                    <input type="color" name="secondary_color" class="form-control" value="#F5D800">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Text Color') }}</label>
                                    <input type="color" name="text_color" class="form-control" value="#1A1A1A">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Background Image') }}</label>
                            <div class="custom-file">
                                <input type="file" name="background_image" class="custom-file-input" accept="image/*" id="background_image">
                                <label class="custom-file-label" for="background_image">{{ translate('Choose file') }}</label>
                            </div>
                            <small class="text-muted">{{ translate('Recommended: 1920x1080px, Max 2MB') }}</small>
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
                                <input type="checkbox" name="status" class="toggle-switch-input" checked>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                                <span class="toggle-switch-content ml-2">
                                    <span class="d-block">{{ translate('Active') }}</span>
                                    <small class="text-muted">{{ translate('Game is visible to eligible customers') }}</small>
                                </span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="toggle-switch d-flex align-items-center mb-3">
                                <input type="checkbox" name="first_play_always_wins" class="toggle-switch-input">
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                                <span class="toggle-switch-content ml-2">
                                    <span class="d-block">{{ translate('First Play Always Wins') }}</span>
                                    <small class="text-muted">{{ translate('Guarantee a prize on first play') }}</small>
                                </span>
                            </label>
                        </div>

                        <hr>

                        <div class="alert alert-soft-info">
                            <i class="tio-info"></i>
                            <strong>{{ translate('Next Steps') }}</strong>
                            <p class="mb-0 small">{{ translate('After creating the game, you will be able to add prizes and configure eligibility rules.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class="tio-save"></i> {{ translate('Create Game') }}
                            </button>
                            <a href="{{ route('admin.gamification.index') }}" class="btn btn-secondary">
                                <i class="tio-clear"></i> {{ translate('Cancel') }}
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
    // Image preview
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
