@php($params=session('dash_params'))
@if($params['zone_id']!='all')
    @php($zone_name=\App\Models\Zone::where('id',$params['zone_id'])->first()->name)
@else
    @php($zone_name=translate('All'))
@endif

{{-- Section header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div class="d-flex align-items-center gap-2">
        <div class="bana-section-title">
            <span class="bana-section-title-dot"></span>
            {{ translate('order_statistics') }}
        </div>
        <span class="bana-zone-badge">
            <i class="tio-map-outlined" style="font-size:11px;"></i>
            {{ $zone_name }}
        </span>
    </div>
    <select class="bana-period-select order-stats-update" name="statistics_type">
        <option value="overall"    {{ $params['statistics_type'] == 'overall'    ? 'selected' : '' }}>{{ translate('messages.Overall') }}</option>
        <option value="this_year"  {{ $params['statistics_type'] == 'this_year'  ? 'selected' : '' }}>{{ translate('messages.This_year') }}</option>
        <option value="this_month" {{ $params['statistics_type'] == 'this_month' ? 'selected' : '' }}>{{ translate('messages.This_Month') }}</option>
        <option value="this_week"  {{ $params['statistics_type'] == 'this_week'  ? 'selected' : '' }}>{{ translate('messages.This_Week') }}</option>
        <option value="today"      {{ $params['statistics_type'] == 'today'      ? 'selected' : '' }}>{{ translate('messages.Today') }}</option>
    </select>
</div>

{{-- KPI cards --}}
<div class="row g-3">
    <div class="col-xl-3 col-sm-6">
        <a class="bana-kpi-card" href="{{ route('admin.order.list', ['delivered']) }}">
            <div class="bana-kpi-icon green">
                <i class="tio-checkmark-circle-outlined"></i>
            </div>
            <div class="bana-kpi-body">
                <div class="bana-kpi-value">{{ $data['delivered'] }}</div>
                <div class="bana-kpi-label">{{ translate('messages.delivered_orders') }}</div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="bana-kpi-card" href="{{ route('admin.order.list', ['canceled']) }}">
            <div class="bana-kpi-icon red">
                <i class="tio-clear-circle-outlined"></i>
            </div>
            <div class="bana-kpi-body">
                <div class="bana-kpi-value">{{ $data['canceled'] }}</div>
                <div class="bana-kpi-label">{{ translate('messages.canceled_orders') }}</div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="bana-kpi-card" href="{{ route('admin.order.list', ['refunded']) }}">
            <div class="bana-kpi-icon yellow">
                <i class="tio-refresh"></i>
            </div>
            <div class="bana-kpi-body">
                <div class="bana-kpi-value">{{ $data['refunded'] }}</div>
                <div class="bana-kpi-label">{{ translate('messages.refunded_orders') }}</div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="bana-kpi-card" href="{{ route('admin.order.list', ['failed']) }}">
            <div class="bana-kpi-icon orange">
                <i class="tio-warning-outlined"></i>
            </div>
            <div class="bana-kpi-body">
                <div class="bana-kpi-value">{{ $data['refund_requested'] }}</div>
                <div class="bana-kpi-label">{{ translate('messages.payment_failed_orders') }}</div>
            </div>
        </a>
    </div>
</div>


