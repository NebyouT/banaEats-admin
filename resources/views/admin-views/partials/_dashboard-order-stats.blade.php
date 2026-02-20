<div class="col-12 mb-1">
    <div class="bana-section-title" style="font-size:12px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
        {{ translate('Live Order Status') }}
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <a class="bana-order-card c1" href="{{ route('admin.dispatch.list', ['searching_for_deliverymen']) }}">
        <div class="bana-order-card-left">
            <div class="bana-order-card-icon">
                <i class="tio-time"></i>
            </div>
            <span class="bana-order-card-label">{{ translate('unassigned_orders') }}</span>
        </div>
        <span class="bana-order-card-count">{{ $data['searching_for_dm'] }}</span>
    </a>
</div>

<div class="col-sm-6 col-lg-3">
    <a class="bana-order-card c2" href="{{ route('admin.order.list', ['accepted']) }}">
        <div class="bana-order-card-left">
            <div class="bana-order-card-icon">
                <i class="tio-user-outlined"></i>
            </div>
            <span class="bana-order-card-label">{{ translate('accepted_by_delivery_man') }}</span>
        </div>
        <span class="bana-order-card-count">{{ $data['accepted_by_dm'] }}</span>
    </a>
</div>

<div class="col-sm-6 col-lg-3">
    <a class="bana-order-card c3" href="{{ route('admin.order.list', ['processing']) }}">
        <div class="bana-order-card-left">
            <div class="bana-order-card-icon">
                <i class="tio-restaurant"></i>
            </div>
            <span class="bana-order-card-label">{{ translate('cooking_in_restaurant') }}</span>
        </div>
        <span class="bana-order-card-count">{{ $data['preparing_in_rs'] }}</span>
    </a>
</div>

<div class="col-sm-6 col-lg-3">
    <a class="bana-order-card c4" href="{{ route('admin.order.list', ['food_on_the_way']) }}">
        <div class="bana-order-card-left">
            <div class="bana-order-card-icon">
                <i class="tio-bike"></i>
            </div>
            <span class="bana-order-card-label">{{ translate('picked_up_by_delivery_man') }}</span>
        </div>
        <span class="bana-order-card-count">{{ $data['picked_up'] }}</span>
    </a>
</div>
