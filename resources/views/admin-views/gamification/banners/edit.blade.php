@extends('layouts.admin.app')

@section('title', translate('Edit Gamification Banner'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><img src="{{dynamicAsset('public/assets/admin/img/banner.png')}}" class="w--20" alt=""></span>
            <span>{{ translate('Edit Gamification Banner') }}</span>
        </h1>
    </div>

    <form action="{{ route('admin.gamification.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-2">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title">{{ translate('Banner Info') }}</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Title') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ $banner->title }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Subtitle') }}</label>
                                    <input type="text" name="subtitle" class="form-control" value="{{ $banner->subtitle }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Linked Game') }} <span class="text-danger">*</span></label>
                                    <select name="game_id" class="form-control" required>
                                        @foreach($games as $game)
                                        <option value="{{ $game->id }}" {{ $banner->game_id == $game->id ? 'selected' : '' }}>{{ $game->name }} ({{ ucwords(str_replace('_',' ',$game->type)) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Placement') }} <span class="text-danger">*</span></label>
                                    <select name="placement" class="form-control" required>
                                        <option value="home" {{ $banner->placement == 'home' ? 'selected' : '' }}>{{ translate('Home Screen') }}</option>
                                        <option value="restaurant" {{ $banner->placement == 'restaurant' ? 'selected' : '' }}>{{ translate('Restaurant Page') }}</option>
                                        <option value="checkout" {{ $banner->placement == 'checkout' ? 'selected' : '' }}>{{ translate('Checkout Page') }}</option>
                                        <option value="cart" {{ $banner->placement == 'cart' ? 'selected' : '' }}>{{ translate('Cart Page') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Button Text') }}</label>
                                    <input type="text" name="button_text" class="form-control" value="{{ $banner->button_text }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Priority') }}</label>
                                    <input type="number" name="priority" class="form-control" value="{{ $banner->priority }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Start Date') }}</label>
                                    <input type="datetime-local" name="start_date" class="form-control" value="{{ $banner->start_date ? $banner->start_date->format('Y-m-d\TH:i') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('End Date') }}</label>
                                    <input type="datetime-local" name="end_date" class="form-control" value="{{ $banner->end_date ? $banner->end_date->format('Y-m-d\TH:i') : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">{{ translate('Zones') }}</label>
                            <select name="zone_ids[]" class="form-control js-select2-custom" multiple>
                                @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ $banner->zone_ids && in_array($zone->id, $banner->zone_ids) ? 'selected' : '' }}>{{ $zone->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ translate('Leave empty for all zones') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title">{{ translate('Visual') }}</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Background') }}</label>
                                    <input type="color" name="background_color" class="form-control" value="{{ $banner->background_color }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Text') }}</label>
                                    <input type="color" name="text_color" class="form-control" value="{{ $banner->text_color }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">{{ translate('Button Color') }}</label>
                            <input type="color" name="button_color" class="form-control" value="{{ $banner->button_color }}">
                        </div>
                        <div class="form-group">
                            <label class="input-label">{{ translate('Banner Image') }}</label>
                            @if($banner->image_full_url)
                            <img src="{{ $banner->image_full_url }}" class="img-fluid rounded mb-2" style="max-height:100px" alt="">
                            @endif
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" accept="image/*" id="bannerImg">
                                <label class="custom-file-label" for="bannerImg">{{ translate('Choose file') }}</label>
                            </div>
                        </div>
                        <img id="imgPreview" src="" class="img-fluid rounded mt-2" style="max-height:120px;display:none" alt="">
                        <div class="form-group mt-3">
                            <label class="toggle-switch d-flex align-items-center">
                                <input type="checkbox" name="status" class="toggle-switch-input" {{ $banner->status ? 'checked' : '' }}>
                                <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                <span class="toggle-switch-content ml-2"><span class="d-block">{{ translate('Active') }}</span></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block"><i class="tio-save"></i> {{ translate('Update Banner') }}</button>
                        <a href="{{ route('admin.gamification.banners.index') }}" class="btn btn-secondary btn-block mt-2"><i class="tio-back-ui"></i> {{ translate('Cancel') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script_2')
<script>
$('#bannerImg').on('change', function(){
    const f = this.files[0];
    if(f){ const r = new FileReader(); r.onload = function(e){ $('#imgPreview').attr('src',e.target.result).show(); }; r.readAsDataURL(f); $(this).next('.custom-file-label').text(f.name); }
});
</script>
@endpush
