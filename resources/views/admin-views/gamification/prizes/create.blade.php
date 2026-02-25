@extends('layouts.admin.app')

@section('title', translate('Add Prize'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{dynamicAsset('public/assets/admin/img/gift.png')}}" class="w--20" alt="">
            </span>
            <span>{{ translate('Add Prize to') }}: {{ $game->name }}</span>
        </h1>
    </div>

    <form action="{{ route('admin.gamification.prizes.store', $game->id) }}" method="POST" enctype="multipart/form-data">
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
                                    <input type="text" name="name" class="form-control" placeholder="{{ translate('e.g., 20% OFF') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Prize Type') }} <span class="text-danger">*</span></label>
                                    <select name="type" id="prize-type" class="form-control" required>
                                        <option value="">{{ translate('Select Type') }}</option>
                                        <option value="discount_percentage">{{ translate('Discount (%)') }}</option>
                                        <option value="discount_fixed">{{ translate('Discount (Fixed Amount)') }}</option>
                                        <option value="free_delivery">{{ translate('Free Delivery') }}</option>
                                        <option value="loyalty_points">{{ translate('Loyalty Points') }}</option>
                                        <option value="wallet_credit">{{ translate('Wallet Credit') }}</option>
                                        <option value="free_item">{{ translate('Free Item') }}</option>
                                        <option value="mystery">{{ translate('Mystery Prize') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Value') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="value" id="prize-value" class="form-control" step="0.01" min="0" required>
                                    <small class="text-muted" id="value-hint">{{ translate('Enter the prize value') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Probability (%)') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="probability" class="form-control" step="0.01" min="0" max="100" value="10" required>
                                    <small class="text-muted">{{ translate('Chance of winning this prize (0-100)') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Description') }}</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="{{ translate('Prize description...') }}"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Total Quantity') }}</label>
                                    <input type="number" name="total_quantity" class="form-control" min="1" placeholder="{{ translate('Leave empty for unlimited') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Expiry (Days)') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="expiry_days" class="form-control" value="7" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Minimum Order Amount') }}</label>
                            <input type="number" name="min_order_amount" class="form-control" step="0.01" min="0" placeholder="{{ translate('Optional') }}">
                        </div>

                        <div class="form-group">
                            <label class="toggle-switch d-flex align-items-center mb-3">
                                <input type="checkbox" name="allow_multiple_wins" class="toggle-switch-input">
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                                <span class="toggle-switch-content ml-2">
                                    <span class="d-block">{{ translate('Allow Multiple Wins') }}</span>
                                    <small class="text-muted">{{ translate('Customer can win this prize multiple times') }}</small>
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
                                <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ translate('Leave empty for all restaurants') }}</small>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Specific Zones') }}</label>
                            <select name="zone_ids[]" class="form-control js-select2-custom" multiple>
                                @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ translate('Leave empty for all zones') }}</small>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('Advanced Controls') }} <small class="text-muted">({{ translate('Optional') }})</small></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Max Discount Amount') }}</label>
                                    <input type="number" name="max_discount_amount" class="form-control" step="0.01" min="0" placeholder="{{ translate('No cap') }}">
                                    <small class="text-muted">{{ translate('Caps percentage discount to this amount') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Max Delivery Distance (km)') }}</label>
                                    <input type="number" name="max_delivery_distance_km" class="form-control" min="0" placeholder="{{ translate('No limit') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Min Delivery Time (min)') }}</label>
                                    <input type="number" name="min_delivery_time_gap_minutes" class="form-control" min="0" placeholder="{{ translate('Optional') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Max Delivery Time (min)') }}</label>
                                    <input type="number" name="max_delivery_time_gap_minutes" class="form-control" min="0" placeholder="{{ translate('Optional') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Valid Order Types') }}</label>
                                    <select name="valid_order_types[]" class="form-control js-select2-custom" multiple>
                                        <option value="delivery">{{ translate('Delivery') }}</option>
                                        <option value="takeaway">{{ translate('Takeaway') }}</option>
                                        <option value="dine_in">{{ translate('Dine In') }}</option>
                                    </select>
                                    <small class="text-muted">{{ translate('Leave empty for all') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Valid Payment Methods') }}</label>
                                    <select name="valid_payment_methods[]" class="form-control js-select2-custom" multiple>
                                        <option value="cash_on_delivery">{{ translate('Cash on Delivery') }}</option>
                                        <option value="digital_payment">{{ translate('Digital Payment') }}</option>
                                        <option value="wallet">{{ translate('Wallet') }}</option>
                                    </select>
                                    <small class="text-muted">{{ translate('Leave empty for all') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Min Cart Items') }}</label>
                                    <input type="number" name="min_cart_items" class="form-control" min="0" placeholder="{{ translate('No minimum') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Min Order Count') }}</label>
                                    <input type="number" name="min_order_count" class="form-control" min="0" placeholder="{{ translate('No minimum') }}">
                                    <small class="text-muted">{{ translate('User must have this many past orders') }}</small>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3">{{ translate('Schedule') }}</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Schedule Type') }}</label>
                                    <select name="schedule_type" id="scheduleType" class="form-control">
                                        <option value="">{{ translate('All Day') }}</option>
                                        <option value="specific_time">{{ translate('Specific Time') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 schedule-time-fields" style="display:none">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Valid From') }}</label>
                                    <input type="time" name="valid_from_time" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 schedule-time-fields" style="display:none">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('Valid Until') }}</label>
                                    <input type="time" name="valid_until_time" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">{{ translate('Valid Days') }}</label>
                            <select name="valid_days[]" class="form-control js-select2-custom" multiple>
                                <option value="0">{{ translate('Sunday') }}</option>
                                <option value="1">{{ translate('Monday') }}</option>
                                <option value="2">{{ translate('Tuesday') }}</option>
                                <option value="3">{{ translate('Wednesday') }}</option>
                                <option value="4">{{ translate('Thursday') }}</option>
                                <option value="5">{{ translate('Friday') }}</option>
                                <option value="6">{{ translate('Saturday') }}</option>
                            </select>
                            <small class="text-muted">{{ translate('Leave empty for every day') }}</small>
                        </div>

                        <div class="form-group">
                            <label class="toggle-switch d-flex align-items-center">
                                <input type="checkbox" name="new_customer_only" class="toggle-switch-input">
                                <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                <span class="toggle-switch-content ml-2">
                                    <span class="d-block">{{ translate('New Customers Only') }}</span>
                                    <small class="text-muted">{{ translate('Only users with zero completed orders') }}</small>
                                </span>
                            </label>
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
                            <input type="color" name="color" class="form-control" value="#8DC63F">
                            <small class="text-muted">{{ translate('Color on the wheel/card') }}</small>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('Prize Image') }}</label>
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" accept="image/*" id="prize_image">
                                <label class="custom-file-label" for="prize_image">{{ translate('Choose file') }}</label>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <img id="image_preview" src="" class="img-fluid rounded" style="max-height: 150px; display: none;" alt="">
                        </div>

                        <div class="form-group">
                            <label class="toggle-switch d-flex align-items-center">
                                <input type="checkbox" name="status" class="toggle-switch-input" checked>
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
                                <i class="tio-save"></i> {{ translate('Add Prize') }}
                            </button>
                            <a href="{{ route('admin.gamification.prizes.index', $game->id) }}" class="btn btn-secondary">
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

    // Schedule type toggle
    $('#scheduleType').on('change', function(){
        $('.schedule-time-fields').toggle($(this).val() === 'specific_time');
    });

    // Prize type change
    $('#prize-type').on('change', function() {
        var type = $(this).val();
        var valueInput = $('#prize-value');
        var valueHint = $('#value-hint');
        
        switch(type) {
            case 'discount_percentage':
                valueInput.attr('max', 100);
                valueHint.text('{{ translate("Enter percentage (0-100)") }}');
                break;
            case 'discount_fixed':
                valueInput.removeAttr('max');
                valueHint.text('{{ translate("Enter fixed amount") }}');
                break;
            case 'free_delivery':
                valueInput.val(0).attr('readonly', true);
                valueHint.text('{{ translate("No value needed for free delivery") }}');
                break;
            case 'loyalty_points':
                valueInput.removeAttr('max readonly');
                valueHint.text('{{ translate("Enter number of points") }}');
                break;
            case 'wallet_credit':
                valueInput.removeAttr('max readonly');
                valueHint.text('{{ translate("Enter credit amount") }}');
                break;
            case 'free_item':
                valueInput.val(0).attr('readonly', true);
                valueHint.text('{{ translate("Specify item in description") }}');
                break;
            case 'mystery':
                valueInput.val(0).attr('readonly', true);
                valueHint.text('{{ translate("Mystery prize - value hidden") }}');
                break;
            default:
                valueInput.removeAttr('max readonly');
                valueHint.text('{{ translate("Enter the prize value") }}');
        }
    });
});
</script>
@endpush
