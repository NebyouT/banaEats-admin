@extends('layouts.admin.app')

@section('title', translate('Edit Prize'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{dynamicAsset('public/assets/admin/img/gift.png')}}" class="w--20" alt="">
            </span>
            <span>{{ translate('Edit Prize') }}: {{ $prize->name }}</span>
        </h1>
    </div>

    <form action="{{ route('admin.gamification.prizes.update', [$game->id, $prize->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-2">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('Prize Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Prize Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $prize->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Prize Type') }} <span class="text-danger">*</span></label>
                                    <select name="type" id="prize-type" class="form-control" required>
                                        <option value="discount_percentage" {{ $prize->type == 'discount_percentage' ? 'selected' : '' }}>{{ translate('Discount (%)') }}</option>
                                        <option value="discount_fixed" {{ $prize->type == 'discount_fixed' ? 'selected' : '' }}>{{ translate('Discount (Fixed Amount)') }}</option>
                                        <option value="free_delivery" {{ $prize->type == 'free_delivery' ? 'selected' : '' }}>{{ translate('Free Delivery') }}</option>
                                        <option value="loyalty_points" {{ $prize->type == 'loyalty_points' ? 'selected' : '' }}>{{ translate('Loyalty Points') }}</option>
                                        <option value="wallet_credit" {{ $prize->type == 'wallet_credit' ? 'selected' : '' }}>{{ translate('Wallet Credit') }}</option>
                                        <option value="free_item" {{ $prize->type == 'free_item' ? 'selected' : '' }}>{{ translate('Free Item') }}</option>
                                        <option value="mystery" {{ $prize->type == 'mystery' ? 'selected' : '' }}>{{ translate('Mystery Prize') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Value') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="value" class="form-control" value="{{ $prize->value }}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Probability (%)') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="probability" class="form-control" value="{{ $prize->probability }}" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Description') }}</label>
                            <textarea name="description" class="form-control" rows="2">{{ $prize->description }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Total Quantity') }}</label>
                                    <input type="number" name="total_quantity" class="form-control" value="{{ $prize->total_quantity }}" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Expiry (Days)') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="expiry_days" class="form-control" value="{{ $prize->expiry_days }}" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Minimum Order Amount') }}</label>
                            <input type="number" name="min_order_amount" class="form-control" value="{{ $prize->min_order_amount }}" step="0.01" min="0">
                        </div>

                        <div class="form-group">
                            <label class="toggle-switch d-flex align-items-center mb-3">
                                <input type="checkbox" name="allow_multiple_wins" class="toggle-switch-input" {{ $prize->allow_multiple_wins ? 'checked' : '' }}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                                <span class="toggle-switch-content ml-2">
                                    <span class="d-block">{{ translate('Allow Multiple Wins') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('Restrictions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="input-label">{{ translate('Specific Restaurants') }}</label>
                            <select name="restaurant_ids[]" class="form-control js-select2-custom" multiple>
                                @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" {{ in_array($restaurant->id, $prize->restaurant_ids ?? []) ? 'selected' : '' }}>{{ $restaurant->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Specific Zones') }}</label>
                            <select name="zone_ids[]" class="form-control js-select2-custom" multiple>
                                @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ in_array($zone->id, $prize->zone_ids ?? []) ? 'selected' : '' }}>{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('Visual Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="input-label">{{ translate('Prize Color') }}</label>
                            <input type="color" name="color" class="form-control" value="{{ $prize->color }}">
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Prize Image') }}</label>
                            @if($prize->image_full_url)
                            <div class="mb-2">
                                <img src="{{ $prize->image_full_url }}" class="img-fluid rounded" style="max-height: 150px;" alt="">
                            </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" accept="image/*" id="prize_image">
                                <label class="custom-file-label" for="prize_image">{{ translate('Choose new file') }}</label>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <img id="image_preview" src="" class="img-fluid rounded" style="max-height: 150px; display: none;" alt="">
                        </div>

                        <div class="form-group">
                            <label class="toggle-switch d-flex align-items-center">
                                <input type="checkbox" name="status" class="toggle-switch-input" {{ $prize->status ? 'checked' : '' }}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                                <span class="toggle-switch-content ml-2">
                                    <span class="d-block">{{ translate('Active') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="tio-save"></i> {{ translate('Update Prize') }}
                            </button>
                            <a href="{{ route('admin.gamification.prizes.index', $game->id) }}" class="btn btn-secondary">
                                <i class="tio-back-ui"></i> {{ translate('Back to Prizes') }}
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
    $('#prize_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
            $(this).next('.custom-file-label').text(file.name);
        }
    });
});
</script>
@endpush
