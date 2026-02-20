@extends('layouts.admin.app')

@section('title',\App\Models\BusinessSetting::where(['key'=>'business_name'])->first()->value??'Dashboard')
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    @if(auth('admin')->user()->role_id == 1)

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-header-title">
                    {{ translate('messages.welcome') }}, {{ auth('admin')->user()->f_name }}
                    <span style="color:#8DC63F;">👋</span>
                </h1>
                <p class="page-header-text">{{ translate('messages.Hello,_here_you_can_manage_your_orders_by_zone.') }}</p>
            </div>
            @php($zones = \App\Models\Zone::get(['id','name']))
            <div class="d-flex align-items-center gap-2">
                <span style="font-size:12px;color:#888;font-weight:600;white-space:nowrap;">{{ translate('Filter by zone') }}:</span>
                <select name="zone_id" class="bana-period-select fetch-data-zone-wise" style="min-width:160px;">
                    <option value="all">{{ translate('all_zones') }}</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone['id'] }}" {{ $params['zone_id'] == $zone['id'] ? 'selected' : '' }}>
                            {{ $zone['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Demo zone notice ── --}}
    @if($zones->first()?->name == 'Demo Zone' && $zones->count() <= 1)
    <div class="card mb-4" style="border-left:4px solid #F5D800 !important;">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
            <div>
                <div style="font-size:14px;font-weight:700;color:#1A1A1A;">{{ translate('All Data From demo Zone') }}</div>
                <div style="font-size:12px;color:#888;margin-top:3px;">{{ translate('In this page you see all the demo zone data. To show actual data setup your Zones, Business Setting & complete orders') }}</div>
            </div>
            <a href="{{ route('admin.zone.home') }}" class="btn btn--primary btn-sm" style="white-space:nowrap;">
                <i class="tio-add mr-1"></i>{{ translate('Create Zone') }}
            </a>
        </div>
    </div>
    @endif

    {{-- ── Order Statistics ── --}}
    <div class="card mb-4">
        <div class="card-body">
            <div id="order_stats_top">
                @include('admin-views.partials._order-statics', ['data' => $data])
            </div>
            <div class="row g-2 mt-3" id="order_stats">
                @include('admin-views.partials._dashboard-order-stats', ['data' => $data])
            </div>
        </div>
    </div>

    {{-- ── Monthly Earnings Chart ── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card h-100" id="monthly-earning-graph">
                @include('admin-views.partials._monthly-earning-graph', [
                    'total_sell' => $total_sell,
                    'total_subs' => $total_subs,
                    'commission' => $commission
                ])
            </div>
        </div>
    </div>

    {{-- ── Analytics Grid ── --}}
    <div class="row g-3">
        {{-- User Statistics --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="bana-section-title">
                        <span class="bana-section-title-dot"></span>
                        {{ translate('user_statistics') }}
                    </div>
                    <div id="stat_zone">
                        @include('admin-views.partials._zone-change', ['data' => $data])
                    </div>
                </div>
                <div id="user-statistic-donut-chart">
                    @include('admin-views.partials._user-overview-chart', ['data' => $data])
                </div>
            </div>
        </div>

        {{-- Popular Restaurants --}}
        <div class="col-lg-6">
            <div class="card h-100" id="popular-restaurants-view">
                @include('admin-views.partials._popular-restaurants', ['popular' => $data['popular']])
            </div>
        </div>

        {{-- Top Delivery Men --}}
        <div class="col-lg-6">
            <div class="card h-100" id="top-deliveryman-view">
                @include('admin-views.partials._top-deliveryman', ['top_deliveryman' => $data['top_deliveryman']])
            </div>
        </div>

        {{-- Top Restaurants --}}
        <div class="col-lg-6">
            <div class="card h-100" id="top-restaurants-view">
                @include('admin-views.partials._top-restaurants', ['top_restaurants' => $data['top_restaurants']])
            </div>
        </div>

        {{-- Top Rated Foods --}}
        <div class="col-lg-6">
            <div class="card h-100" id="top-rated-foods-view">
                @include('admin-views.partials._top-rated-foods', ['top_rated_foods' => $data['top_rated_foods']])
            </div>
        </div>

        {{-- Top Selling Foods --}}
        <div class="col-lg-6">
            <div class="card h-100" id="top-selling-foods-view">
                @include('admin-views.partials._top-selling-foods', ['top_sell' => $data['top_sell']])
            </div>
        </div>
    </div>

    @else
    {{-- Non-admin welcome --}}
    <div class="page-header">
        <h1 class="page-header-title">
            {{ translate('messages.welcome') }}, {{ auth('admin')->user()->f_name }}
            <span style="color:#8DC63F;">👋</span>
        </h1>
        <p class="page-header-text">{{ translate('messages.Hello,_here_you_can_manage_your_restaurants.') }}</p>
    </div>
    @endif
</div>
@endsection

@push('script')
<script src="{{dynamicAsset('public/assets/admin/apexcharts/apexcharts.min.js')}}"></script>
@endpush

@push('script_2')
<script src="{{dynamicAsset('public/assets/admin/js/view-pages/apex-charts.js')}}"></script>
<script>

    "use strict";

loadchart();

    function loadchart(){

        var commission = $('#updatingData').data('commission').split(',').map(Number);
        var subscription = $('#updatingData').data('subscription').split(',').map(Number);
        var total_sell = $('#updatingData').data('total_sell').split(',').map(Number);
        if($('#user-overview').data('id')){
            var id = $('#user-overview').data('id');
            var value = $('#user-overview').data('value').split(',').map(Number);
            var labels = $('#user-overview').data('labels').split(',');
            newdonutChart(id,value,labels)
        }

        var options = {
            series: [{
                name: '{{ translate('messages.admin_commission') }}',
                data: commission,
                },  {
                name: '{{ translate('messages.total_sell') }}',
                data: total_sell,
                }
                @if (\App\CentralLogics\Helpers::subscription_check())
                ,{
                name: '{{ translate('messages.Subscription') }}',
                data: subscription,
                }
                @endif

                ],
            chart: {
                    toolbar:{
                        show: false
                    },
                type: 'bar',
                height: 380
            },
            plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 0,
                borderRadiusApplication: 'around',
                borderRadiusWhenStacked: 'last',
                columnWidth: '70%',
                barHeight: '70%',
                distributed: false,
                rangeBarOverlap: true,
                rangeBarGroupRows: false,
                hideZeroBarsWhenGrouped: false,
                isDumbbell: false,
                dumbbellColors: undefined,
                isFunnel: false,
                isFunnel3d: true,
                    colors: {
                        ranges: [{
                            from: 0,
                            to: 0,
                            color: undefined
                        }],
                        backgroundBarColors: [],
                        backgroundBarOpacity: 1,
                        backgroundBarRadius: 0,
                    }
                }
            },
            dataLabels: {
            enabled: false,
                position: 'top',
                maxItems: 1,
                hideOverflowingLabels: true,
                    total: {
                    enabled: false,
                    formatter: undefined,
                    offsetX: 0,
                    offsetY: 0,
                        style: {
                            color: '#373d3f',
                            fontSize: '12px',
                            fontFamily: undefined,
                            fontWeight: 600
                        }
                    }
            },
            stroke: {
                show: true,
                curve: 'smooth',
                lineCap: 'butt',
                width: 2,
                dashArray: 0,
                colors: ['transparent']
            },
            xaxis: {
                categories: ["{{ translate('messages.Jan') }}","{{ translate('messages.Feb') }}","{{ translate('messages.Mar') }}","{{ translate('messages.April') }}","{{ translate('messages.May') }}","{{ translate('messages.Jun') }}","{{ translate('messages.Jul') }}","{{ translate('messages.Aug') }}","{{ translate('messages.Sep') }}","{{ translate('messages.Oct') }}","{{ translate('messages.Nov') }}","{{ translate('messages.Dec') }}"],
            },
            yaxis: {
                title: {
                    text: '{{ \App\CentralLogics\Helpers::currency_symbol() }}. ({{ \App\CentralLogics\Helpers::currency_code() }})'
                }
            },
            colors: ['#8DC63F', '#F5D800', '#04BB7B'],
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "{{ \App\CentralLogics\Helpers::currency_symbol() }} " + val + " {{ \App\CentralLogics\Helpers::currency_code() }}"
                    }
                }
            }
        };

        var commissionchart = new ApexCharts(document.querySelector("#updatingData"), options);
        commissionchart.render();
    }

    $(document).on('change', '.order-stats-update', function () {
        let type = $(this).val();
            order_stats_update(type);
        });


        function order_stats_update(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.dashboard-stats.order')}}',
                data: {
                    statistics_type: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    insert_param('statistics_type',type);
                    $('#order_stats').html(data.view)
                    $('#order_stats_top').html(data.order_stats_top)
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }



        $('.fetch-data-zone-wise').on('change',function (){
            let zone_id = $(this).val();
            fetch_data_zone_wise(zone_id)
        })

        function fetch_data_zone_wise(zone_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.dashboard-stats.zone')}}',
                data: {
                    zone_id: zone_id
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {

                    console.log(data.user_overview);
                    insert_param('zone_id', zone_id);
                    $('#order_stats_top').html(data.order_stats_top);
                    $('#order_stats').html(data.order_stats);
                    $('#stat_zone').html(data.stat_zone);
                    $('#user-statistic-donut-chart').html(data.view)
                    $('#monthly-earning-graph').html(data.monthly_graph);
                    $('#popular-restaurants-view').html(data.popular_restaurants);
                    $('#top-deliveryman-view').html(data.top_deliveryman);
                    $('#top-rated-foods-view').html(data.top_rated_foods);
                    $('#top-restaurants-view').html(data.top_restaurants);
                    $('#top-selling-foods-view').html(data.top_selling_foods);
                    loadchart();
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }


        $(document).on('change', '.user-overview-stats-update', function () {
            let type = $(this).val();
            user_overview_stats_update(type)
        });




        function user_overview_stats_update(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.dashboard-stats.user-overview')}}',
                data: {
                    user_overview: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    insert_param('user_overview',type);
                    $('#user-statistic-donut-chart').html(data.view)
                    loadchart();
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }

        function insert_param(key, value) {
            key = encodeURIComponent(key);
            value = encodeURIComponent(value);
            let kvp = document.location.search.substr(1).split('&');
            let i = 0;

            for (; i < kvp.length; i++) {
                if (kvp[i].startsWith(key + '=')) {
                    let pair = kvp[i].split('=');
                    pair[1] = value;
                    kvp[i] = pair.join('=');
                    break;
                }
            }
            if (i >= kvp.length) {
                kvp[kvp.length] = [key, value].join('=');
            }
            // can return this or...
            let params = kvp.join('&');
            // change url page with new params
            window.history.pushState('page2', 'Title', '{{url()->current()}}?' + params);
        }
    </script>
@endpush
