@extends('layouts.landing.app')
@section('home','active')
@section('title', $landing_data['business_name'])
@section('content')

    {{-- ═══ HERO SECTION ═══ --}}
    <section class="bl-hero">
        <div class="bl-container">
            <div class="bl-hero-inner">
                <div class="bl-hero-content">
                    <div class="bl-hero-tag">🍔 {{ $landing_data['business_name'] }}</div>
                    <h1 class="bl-hero-title">
                        <span class="hl-green">{{ $landing_data['header_title'] }}</span>
                    </h1>
                    <p class="bl-hero-subtitle">{{ $landing_data['header_sub_title'] }}</p>
                    <p class="bl-hero-tagline">{{ $landing_data['header_tag_line'] }}</p>
                    <div class="bl-hero-actions">
                        @if($landing_data['header_app_button_status'])
                        <a href="{{ $landing_data['header_button_redirect_link'] ?? '#' }}" class="bl-btn bl-btn-primary">
                            {{ $landing_data['header_app_button_name'] }} →
                        </a>
                        @endif
                        <a href="{{ route('about-us') }}" class="bl-btn bl-btn-outline">{{ translate('Learn More') }}</a>
                    </div>
                    @if($landing_data['header_floating_total_order'] || $landing_data['header_floating_total_user'] || $landing_data['header_floating_total_reviews'])
                    <div class="bl-hero-stats">
                        @if($landing_data['header_floating_total_order'])
                        <div class="bl-hero-stat">
                            <span class="bl-hero-stat-num">{{ $landing_data['header_floating_total_order'] }}+</span>
                            <span class="bl-hero-stat-label">{{ translate('Orders') }}</span>
                        </div>
                        @endif
                        @if($landing_data['header_floating_total_user'])
                        <div class="bl-hero-stat">
                            <span class="bl-hero-stat-num">{{ $landing_data['header_floating_total_user'] }}+</span>
                            <span class="bl-hero-stat-label">{{ translate('Users') }}</span>
                        </div>
                        @endif
                        @if($landing_data['header_floating_total_reviews'])
                        <div class="bl-hero-stat">
                            <span class="bl-hero-stat-num">{{ $landing_data['header_floating_total_reviews'] }}+</span>
                            <span class="bl-hero-stat-label">{{ translate('Reviews') }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="bl-hero-img-wrap">
                    @if($landing_data['header_floating_total_reviews'])
                    <div class="bl-hero-float bl-hero-float-1">
                        <div class="icon">⭐</div>
                        <div>{{ $landing_data['header_floating_total_reviews'] }}+ {{ translate('Reviews') }}</div>
                    </div>
                    @endif
                    @if($landing_data['header_floating_total_order'])
                    <div class="bl-hero-float bl-hero-float-2">
                        <div class="icon">🛵</div>
                        <div>{{ $landing_data['header_floating_total_order'] }}+ {{ translate('Orders') }}</div>
                    </div>
                    @endif
                    @if($landing_data['header_floating_total_user'])
                    <div class="bl-hero-float bl-hero-float-3">
                        <div class="icon">👥</div>
                        <div>{{ $landing_data['header_floating_total_user'] }}+ {{ translate('Users') }}</div>
                    </div>
                    @endif
                    <img src="{{ $landing_data['header_content_image_full_url'] }}" alt="{{ $landing_data['business_name'] }}">
                </div>
            </div>
        </div>
    </section>
    {{-- ═══ ABOUT SECTION ═══ --}}
    @if(isset($landing_data['about_us_title']))
    <section class="bl-about bl-section">
        <div class="bl-container">
            <div class="bl-about-inner">
                <div class="bl-about-img-wrap">
                    <img src="{{ $landing_data['about_us_image_content_full_url'] }}" alt="{{ $landing_data['about_us_title'] }}">
                </div>
                <div class="bl-about-text">
                    <div class="bl-badge">{{ translate('About Us') }}</div>
                    <h2 class="bl-section-title">{{ $landing_data['about_us_title'] }}</h2>
                    <p class="bl-section-sub">{{ $landing_data['about_us_sub_title'] }}</p>
                    <p style="color:#6B7280;line-height:1.7;margin-bottom:28px">{{ $landing_data['about_us_text'] }}</p>
                    @if($landing_data['about_us_app_button_status'] && $landing_data['about_us_app_button_name'])
                    <a href="{{ $landing_data['about_us_redirect_link'] ?? '#' }}" class="bl-btn bl-btn-primary">
                        {{ $landing_data['about_us_app_button_name'] }} →
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══ ZONES SECTION ═══ --}}
    @if($landing_data['available_zone_status'] && $landing_data['available_zone_list'] && count($landing_data['available_zone_list']) > 0)
    <section class="bl-zones bl-section">
        <div class="bl-container">
            <div class="bl-zones-inner">
                <div>
                    <div class="bl-badge">{{ translate('Available Zones') }}</div>
                    <h2 class="bl-section-title">{{ $landing_data['available_zone_title'] }}</h2>
                    <p style="color:#6B7280;line-height:1.7;margin-bottom:8px">{{ $landing_data['available_zone_short_description'] }}</p>
                    <div class="bl-zone-tags">
                        @foreach($landing_data['available_zone_list'] as $zone)
                        <span class="bl-zone-tag">{{ $zone['display_name'] }}</span>
                        @endforeach
                    </div>
                </div>
                <div style="text-align:center">
                    <img src="{{ $landing_data['available_zone_image_full_url'] }}" alt="{{ $landing_data['available_zone_title'] }}" style="max-height:360px;object-fit:contain">
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══ FEATURES SECTION ═══ --}}
    @if(isset($landing_data['features']) && count($landing_data['features']) > 0)
    <section class="bl-features bl-section">
        <div class="bl-container">
            <div class="bl-features-header">
                <div class="bl-badge">{{ translate('Features') }}</div>
                <h2 class="bl-section-title">{{ $landing_data['feature_title'] }}</h2>
                <p class="bl-section-sub">{{ $landing_data['feature_sub_title'] }}</p>
            </div>
            <div class="bl-feature-grid">
                @foreach($landing_data['features'] as $feature_data)
                <div class="bl-feature-card">
                    <div class="bl-feature-icon">
                        <img src="{{ $feature_data['image_full_url'] }}" alt="{{ $feature_data['title'] }}">
                    </div>
                    <div class="bl-feature-title">{{ $feature_data['title'] }}</div>
                    <div class="bl-feature-desc">{{ $feature_data['description'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    {{-- ═══ SERVICES / PLATFORM SECTION ═══ --}}
    @if(isset($landing_data['services_title']))


    <section class="bl-services bl-section">
        <div class="bl-container">
            <div class="bl-services-header">
                <div class="bl-badge">{{ translate('Our Platform') }}</div>
                <h2 class="bl-section-title">{{ $landing_data['services_title'] }}</h2>
                <p class="bl-section-sub">{{ $landing_data['services_sub_title'] ?? '' }}</p>
            </div>
            <div class="bl-services-tabs">
                @if(isset($landing_data['services_order_title_1']) || isset($landing_data['services_order_title_2']))
                <button class="bl-services-tab" data-target="bl-tab-order">
                    <img src="{{ dynamicAsset('/public/assets/landing/assets_new/img/platform/1.svg') }}" alt="">
                    {{ translate('Order_your_food') }}
                </button>
                @endif
                @if(isset($landing_data['services_manage_restaurant_title_1']) || isset($landing_data['services_manage_restaurant_title_2']))
                <button class="bl-services-tab" data-target="bl-tab-restaurant">
                    <img src="{{ dynamicAsset('/public/assets/landing/assets_new/img/platform/2.svg') }}" alt="">
                    {{ translate('manage_your_restaurant') }}
                </button>
                @endif
                @if(isset($landing_data['services_manage_delivery_title_1']) || isset($landing_data['services_manage_delivery_title_2']))
                <button class="bl-services-tab" data-target="bl-tab-delivery">
                    <img src="{{ dynamicAsset('/public/assets/landing/assets_new/img/platform/3.svg') }}" alt="">
                    {{ translate('earn_by_delivery') }}
                </button>
                @endif
            </div>
            @if(isset($landing_data['services_order_title_1']) || isset($landing_data['services_order_title_2']))
            <div class="bl-services-pane" id="bl-tab-order">
                <div class="bl-services-content">
                    @if(isset($landing_data['services_order_title_1']))<h4>{{ $landing_data['services_order_title_1'] }}</h4><p>{{ $landing_data['services_order_description_1'] ?? '' }}</p>@endif
                    @if(isset($landing_data['services_order_title_2']))<h4>{{ $landing_data['services_order_title_2'] }}</h4><p>{{ $landing_data['services_order_description_2'] ?? '' }}</p>@endif
                    @if(isset($landing_data['services_order_button_status']) && $landing_data['services_order_button_status']==1)
                    <a href="{{ $landing_data['services_order_button_link'] ?? '#' }}" class="bl-btn bl-btn-primary" style="margin-top:16px">{{ $landing_data['services_order_button_name'] }} →</a>
                    @endif
                </div>
                <div class="bl-services-img">
                    <img src="{{ dynamicAsset('/public/assets/landing/assets_new/img/platform/order.svg') }}" alt="">
                </div>
            </div>
            @endif
            @if(isset($landing_data['services_manage_restaurant_title_1']) || isset($landing_data['services_manage_restaurant_title_2']))
            <div class="bl-services-pane" id="bl-tab-restaurant">
                <div class="bl-services-content">
                    @if(isset($landing_data['services_manage_restaurant_title_1']))<h4>{{ $landing_data['services_manage_restaurant_title_1'] }}</h4><p>{{ $landing_data['services_manage_restaurant_description_1'] ?? '' }}</p>@endif
                    @if(isset($landing_data['services_manage_restaurant_title_2']))<h4>{{ $landing_data['services_manage_restaurant_title_2'] }}</h4><p>{{ $landing_data['services_manage_restaurant_description_2'] ?? '' }}</p>@endif
                    @if(isset($landing_data['services_manage_restaurant_button_status']) && $landing_data['services_manage_restaurant_button_status']==1)
                    <a href="{{ $landing_data['services_manage_restaurant_button_link'] ?? '#' }}" class="bl-btn bl-btn-primary" style="margin-top:16px">{{ $landing_data['services_manage_restaurant_button_name'] }} →</a>
                    @endif
                </div>
                <div class="bl-services-img">
                    <img src="{{ dynamicAsset('/public/assets/landing/assets_new/img/platform/restaurant.svg') }}" alt="">
                </div>
            </div>
            @endif
            @if(isset($landing_data['services_manage_delivery_title_1']) || isset($landing_data['services_manage_delivery_title_2']))
            <div class="bl-services-pane" id="bl-tab-delivery">
                <div class="bl-services-content">
                    @if(isset($landing_data['services_manage_delivery_title_1']))<h4>{{ $landing_data['services_manage_delivery_title_1'] }}</h4><p>{{ $landing_data['services_manage_delivery_description_1'] ?? '' }}</p>@endif
                    @if(isset($landing_data['services_manage_delivery_title_2']))<h4>{{ $landing_data['services_manage_delivery_title_2'] }}</h4><p>{{ $landing_data['services_manage_delivery_description_2'] ?? '' }}</p>@endif
                    @if(isset($landing_data['services_manage_delivery_button_status']) && $landing_data['services_manage_delivery_button_status']==1)
                    <a href="{{ $landing_data['services_manage_delivery_button_link'] ?? '#' }}" class="bl-btn bl-btn-primary" style="margin-top:16px">{{ $landing_data['services_manage_delivery_button_name'] }} →</a>
                    @endif
                </div>
                <div class="bl-services-img">
                    <img src="{{ dynamicAsset('/public/assets/landing/assets_new/img/platform/delivery.svg') }}" alt="">
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif
    {{-- ═══ WHY CHOOSE US ═══ --}}
    @if(isset($landing_data['why_choose_us_title']))
    <section class="bl-choose bl-section">
        <div class="bl-container">
            <div class="bl-choose-header">
                <div class="bl-badge">{{ translate('Why Choose Us') }}</div>
                <h2 class="bl-section-title">{{ $landing_data['why_choose_us_title'] }}</h2>
                <p class="bl-section-sub">{{ $landing_data['why_choose_us_sub_title'] ?? '' }}</p>
            </div>
            <div class="bl-choose-grid">
                @foreach([1,2,3,4] as $i)
                @if(isset($landing_data['why_choose_us_image_'.$i]))
                <div class="bl-choose-card">
                    <img src="{{ $landing_data['why_choose_us_image_'.$i.'_full_url'] }}" alt="{{ $landing_data['why_choose_us_title_'.$i] ?? '' }}">
                    <div class="bl-choose-overlay">
                        <h4>{{ $landing_data['why_choose_us_title_'.$i] ?? '' }}</h4>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif
    {{-- ═══ EARN MONEY SECTION ═══ --}}
    @if(isset($landing_data['earn_money_title']))
    <section class="bl-earn bl-section">
        <div class="bl-container">
            <div class="bl-earn-inner">
                <div class="bl-earn-text">
                    <div class="bl-badge">{{ translate('Earn Money') }}</div>
                    <h2 class="bl-section-title">{{ $landing_data['earn_money_title'] }}</h2>
                    <p class="bl-section-sub">{{ $landing_data['earn_money_sub_title'] ?? '' }}</p>
                    @if(isset($landing_data['earn_money_reg_title']))
                    <h3 style="font-size:1.3rem;font-weight:700;color:#1A1A1A;margin:24px 0 16px">{{ $landing_data['earn_money_reg_title'] }}</h3>
                    @endif
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        @if(isset($landing_data['earn_money_restaurant_req_button_status']) && $landing_data['earn_money_restaurant_req_button_status'])
                        <a href="{{ $landing_data['earn_money_restaurant_req_button_link'] ?? '#' }}" class="bl-btn bl-btn-primary">
                            {{ $landing_data['earn_money_restaurant_req_button_name'] }} →
                        </a>
                        @endif
                        @if(isset($landing_data['earn_money_delivery_man_req_button_status']) && $landing_data['earn_money_delivery_man_req_button_status'])
                        <a href="{{ $landing_data['earn_money_delivery_req_button_link'] ?? '#' }}" class="bl-btn bl-btn-outline">
                            {{ $landing_data['earn_money_delivety_man_req_button_name'] }} →
                        </a>
                        @endif
                    </div>
                </div>
                <div class="bl-earn-img">
                    @if(isset($landing_data['earn_money_reg_image_full_url']))
                    <img src="{{ $landing_data['earn_money_reg_image_full_url'] }}" alt="{{ $landing_data['earn_money_title'] }}">
                    @else
                    <img src="{{ dynamicAsset('/public/assets/landing/assets_new/img/business.svg') }}" alt="">
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══ TESTIMONIALS SECTION ═══ --}}
    @if(isset($landing_data['testimonials']) && count($landing_data['testimonials']) > 0)
    <section class="bl-testimonials bl-section">
        <div class="bl-container">
            <div class="bl-testimonials-header">
                <div class="bl-badge">{{ translate('Testimonials') }}</div>
                <h2 class="bl-section-title">{{ $landing_data['testimonial_title'] ?? translate('What Our Users Say') }}</h2>
            </div>
            <div class="bl-testimonials-grid">
                @foreach($landing_data['testimonials'] as $t)
                <div class="bl-testimonial-card">
                    <div class="bl-testimonial-quote">"</div>
                    <p class="bl-testimonial-text">{{ $t['review'] }}</p>
                    <div class="bl-testimonial-author">
                        <img src="{{ $t['reviewer_image_full_url'] }}" alt="{{ $t['name'] }}">
                        <div>
                            <div class="bl-testimonial-name">{{ $t['name'] }}</div>
                            <div class="bl-testimonial-role">{{ $t['designation'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══ WELCOME MODAL ═══ --}}
    @if(isset($new_user) && $new_user == true)
    <div class="bl-modal-overlay" id="bl-welcome-modal">
        <div class="bl-modal-box">
            <button class="bl-modal-close" onclick="document.getElementById('bl-welcome-modal').style.display='none'">&times;</button>
            <img src="{{ dynamicAsset('/public/assets/landing/img/welcome.svg') }}" alt="" style="max-width:160px;margin:0 auto 16px;display:block">
            <h5 style="font-size:1.2rem;font-weight:700;text-align:center;margin-bottom:12px">
                {{ translate('Welcome_to') }} {{ $landing_data['business_name'] }}!
            </h5>
            <p style="text-align:center;color:#6B7280;margin-bottom:24px">
                {{ translate('Thanks for joining us! Your registration is under review. Hang tight, we\'ll notify you once approved!') }}
            </p>
            <div style="text-align:center">
                <button class="bl-btn bl-btn-primary" onclick="document.getElementById('bl-welcome-modal').style.display='none'">
                    {{ translate('okay') }}
                </button>
            </div>
        </div>
    </div>
    @endif

@endsection
@push('script_2')
@if(isset($new_user) && $new_user == true)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var m = document.getElementById('bl-welcome-modal');
        if(m) m.style.display = 'flex';
    });
</script>
@endif
@endpush
