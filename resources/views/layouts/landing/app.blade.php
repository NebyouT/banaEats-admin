<!DOCTYPE html>

<?php
    if(session()->get('landing_site_direction') ){
        $site_direction = session()->get('landing_site_direction');
    } else{
        $site_direction = session()->get('site_direction');
    }
    $country= \App\CentralLogics\Helpers::get_business_settings('country');
    $countryCode= strtolower($country??'auto');
?>
@php( $direction= ( $site_direction === 'rtl') ? "true" : "false")

<html dir="{{ $site_direction }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}" >

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title','Landing Page | ')</title>

    <link rel="stylesheet" href="{{dynamicAsset('/public/assets/landing/assets_new/css/all.min.css')}}" />
    <link rel="stylesheet" href="{{dynamicAsset('/public/assets/landing/assets_new/css/owl.min.css')}}" />
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin/intltelinput/css/intlTelInput.css')}}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/landing/css/bana-landing.css') }}">
    @php($icon = \App\CentralLogics\Helpers::get_business_settings('icon'))
    <link rel="shortcut icon" type="image/x-icon" href="{{ dynamicStorage('storage/app/public/business/'. $icon ?? '') }}">
    @php($landing_page_links = \App\CentralLogics\Helpers::get_business_settings('landing_page_links'))
    @php($backgroundChange =\App\CentralLogics\Helpers::get_business_settings('backgroundChange') )
    @stack('css_or_js')
</head>

<body>
    <!-- Preloader -->
    <div id="bl-preloader"><div class="bl-spinner"></div></div>

    @php($logo = \App\CentralLogics\Helpers::get_business_settings('logo'))
    @php($logo_storage = \App\CentralLogics\Helpers::get_settings_storage('logo'))
    @php($local = session()->has('landing_local') ? session('landing_local') : null)
    @php($lang = \App\CentralLogics\Helpers::get_business_settings('system_language'))

    <header class="bl-header">
        <div class="bl-container">
            <div class="bl-header-inner">
                <a href="{{ route('home') }}" class="bl-logo">
                    <img src="{{ \App\CentralLogics\Helpers::get_full_url('business',$logo,$logo_storage) }}" alt="Logo">
                </a>

                <ul class="bl-nav" id="bl-nav">
                    <li><a href="{{ route('home') }}" class="@yield('home')">{{ translate('messages.home') }}</a></li>
                    @if(isset($landing_page_links['web_app_url_status']) && $landing_page_links['web_app_url_status'])
                    <li><a href="{{ $landing_page_links['web_app_url'] }}">{{ translate('messages.browse_web') }}</a></li>
                    @endif
                    <li><a href="{{ route('about-us') }}" class="@yield('about')">{{ translate('messages.about') }}</a></li>
                    <li><a href="{{ route('privacy-policy') }}" class="@yield('privacy-policy')">{{ translate('messages.privacy_policy') }}</a></li>
                    <li><a href="{{ route('contact-us') }}" class="@yield('contact')">{{ translate('messages.contact') }}</a></li>
                </ul>

                <div class="bl-header-actions">
                    @if($lang)
                    <div class="bl-lang-wrap">
                        <button class="bl-lang-btn" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path d="M10 0a10 10 0 100 20A10 10 0 0010 0zm6.5 6h-3a15 15 0 00-1.4-3.8A8 8 0 0116.5 6zM10 2c.6 0 1.7 1.6 2.3 4H7.7C8.3 3.6 9.4 2 10 2zM2.1 12a8.1 8.1 0 010-4h3.4a17 17 0 000 4H2.1zm.4 2h3a15 15 0 001.4 3.8A8 8 0 012.5 14zm3-8h-3A8 8 0 017.9 2.2 15 15 0 005.5 6zm2.2 8h4.6C11.7 16.4 10.6 18 10 18s-1.7-1.6-2.3-4zm5-2H7.3a15 15 0 010-4h5.4a15 15 0 010 4zm.3 2a15 15 0 01-1.4 3.8A8 8 0 0017.5 14h-3zm3.4-2h-3.4a17 17 0 000-4h3.4a8.1 8.1 0 010 4z"/></svg>
                            @foreach($lang??[] as $d)
                                @if($d['code']==$local)<span>{{$d['code']}}</span>
                                @elseif(!$local && $d['default']==true)<span>{{$d['code']}}</span>
                                @endif
                            @endforeach
                        </button>
                        <div class="bl-lang-dropdown">
                            @foreach($lang??[] as $d)
                                @if($d['status']==1)
                                    <a href="{{ route('lang',[$d['code']]) }}">{{ $d['code'] }}</a>
                                    <hr>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if((isset($toggle_dm_registration) && $toggle_dm_registration) || (isset($toggle_restaurant_registration) && $toggle_restaurant_registration))
                    <div class="bl-join-wrap">
                        <button class="bl-btn bl-btn-primary" type="button">{{ translate('Join us') }} ▾</button>
                        <div class="bl-join-dropdown">
                            @if(isset($toggle_restaurant_registration) && $toggle_restaurant_registration)
                            <a href="{{ route('restaurant.create') }}">{{ translate('messages.join_as_restaurant') }}</a>
                            @endif
                            @if(isset($toggle_dm_registration) && $toggle_dm_registration)
                            <a href="{{ route('deliveryman.create') }}">{{ translate('messages.join_as_deliveryman') }}</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <button class="bl-nav-toggle" id="bl-nav-toggle" type="button" aria-label="Menu">
                        <span></span><span></span><span></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    @yield('content')


        <?php
            $datas = \App\Models\DataSetting::with('translations')->where('type','admin_landing_page')->whereIn('key', ['news_letter_title','news_letter_sub_title','footer_data','shipping_policy_status','refund_policy_status','cancellation_policy_status'])->get();
            $data = [];
            foreach ($datas as $key => $value) {
                if(count($value->translations)>0){
                    $cred = [$value->key => $value->translations[0]['value']];
                    array_push($data,$cred);
                }else{
                    $cred = [$value->key => $value->value];
                    array_push($data,$cred);
                }
            }
            $landing_data = [];
            foreach($data as $single_data){
                foreach($single_data as $key=>$single_value){
                    $landing_data[$key] = $single_value;
                }
            }
        ?>

        @if(isset($landing_data['news_letter_title']))
        <section class="bl-newsletter">
            <div class="bl-container">
                <div class="bl-newsletter-inner">
                    <div>
                        <div class="bl-newsletter-title">{{ $landing_data['news_letter_title'] }}</div>
                        <div class="bl-newsletter-sub">{{ $landing_data['news_letter_sub_title'] ?? '' }}</div>
                    </div>
                    <form method="post" action="{{ route('newsletter.subscribe') }}" class="bl-newsletter-form">
                        @csrf
                        <input type="email" name="email" required placeholder="{{ translate('Enter your email address') }}" value="{{ old('email') }}">
                        <button type="submit">{{ translate('Subscribe') }} →</button>
                    </form>
                </div>
            </div>
        </section>
        @endif

        <footer class="bl-footer">
            <div class="bl-container">
                <div class="bl-footer-grid">
                    <div>
                        <div class="bl-footer-logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ \App\CentralLogics\Helpers::get_full_url('business',$logo,$logo_storage) }}" alt="">
                            </a>
                        </div>
                        <div class="bl-footer-desc">{{ isset($landing_data['footer_data']) ? $landing_data['footer_data'] : '' }}</div>
                        @php($social_media = \App\Models\SocialMedia::where('status', 1)->get())
                        @if(isset($social_media) && count($social_media))
                        <div class="bl-footer-social">
                            @foreach($social_media as $social)
                            <a href="{{ $social->link }}" target="_blank"><i class="fab fa-{{ $social->name }}"></i></a>
                            @endforeach
                        </div>
                        @endif
                        @if(isset($landing_page_links) && ($landing_page_links['app_url_android_status'] || $landing_page_links['app_url_ios_status']))
                        <div class="bl-footer-app-btns">
                            @if($landing_page_links['app_url_android_status'])
                            <a href="{{ $landing_page_links['app_url_android'] }}">
                                <img src="{{dynamicAsset('/public/assets/landing/assets_new/img/google.svg')}}" alt="Google Play">
                            </a>
                            @endif
                            @if($landing_page_links['app_url_ios_status'])
                            <a href="{{ $landing_page_links['app_url_ios'] }}">
                                <img src="{{dynamicAsset('/public/assets/landing/assets_new/img/apple.svg')}}" alt="App Store">
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div>
                        <div class="bl-footer-heading">{{ translate('messages.Support') }}</div>
                        <ul class="bl-footer-links">
                            @if(isset($landing_data['shipping_policy_status']) && $landing_data['shipping_policy_status']==1)
                            <li><a href="{{ route('shipping-policy') }}">{{ translate('messages.shipping_policy') }}</a></li>
                            @endif
                            <li><a href="{{ route('privacy-policy') }}">{{ translate('messages.privacy_policy') }}</a></li>
                            @if(isset($landing_data['refund_policy_status']) && $landing_data['refund_policy_status']==1)
                            <li><a href="{{ route('refund-policy') }}">{{ translate('messages.refund_policy') }}</a></li>
                            @endif
                            @if(isset($landing_data['cancellation_policy_status']) && $landing_data['cancellation_policy_status']==1)
                            <li><a href="{{ route('cancellation-policy') }}">{{ translate('messages.cancellation_policy') }}</a></li>
                            @endif
                            <li><a href="{{ route('terms-and-conditions') }}">{{ translate('messages.terms_and_condition') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <div class="bl-footer-heading">{{ translate('messages.quick_links') }}</div>
                        <ul class="bl-footer-links">
                            <li><a href="{{ route('about-us') }}">{{ translate('messages.about_us') }}</a></li>
                            <li><a href="{{ route('contact-us') }}">{{ translate('messages.contact_us') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <div class="bl-footer-heading">{{ translate('messages.contact_us') }}</div>
                        @php($address = \App\CentralLogics\Helpers::get_business_settings('address'))
                        @php($email_address = \App\CentralLogics\Helpers::get_business_settings('email_address'))
                        @php($phone = \App\CentralLogics\Helpers::get_business_settings('phone'))
                        <ul class="bl-footer-links">
                            @if($address)
                            <li><a href="http://maps.google.com/?q={{ $address }}" target="_blank">
                                <svg width="14" height="14" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2379 2.73992C9.26683 1.06417 7.54208 0.0403906 5.62411 0.00126563C5.54223 -0.000421875 5.45983 -0.000421875 5.37792 0.00126563C3.45998 0.0403906 1.73523 1.06417 0.764169 2.73992C-0.228393 4.4528 -0.25555 6.5103 0.691513 8.24376L4.65911 15.5059C4.83908 15.8189 5.15179 16 5.50108 16C5.85033 16 6.16304 15.8189 6.33757 15.5155L10.3106 8.24376C11.2576 6.5103 11.2304 4.4528 10.2379 2.73992ZM5.50101 7.25002C4.26036 7.25002 3.25101 6.24067 3.25101 5.00002C3.25101 3.75936 4.26036 2.75002 5.50101 2.75002C6.74167 2.75002 7.75101 3.75936 7.75101 5.00002C7.75101 6.24067 6.7417 7.25002 5.50101 7.25002Z" fill="rgba(255,255,255,.5)"/></svg>
                                {{ $address }}
                            </a></li>
                            @endif
                            @if($email_address)
                            <li><a href="mailto:{{ $email_address }}">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.333768 2.97362C2.52971 4.83334 6.38289 8.10516 7.51539 9.12531C7.66742 9.263 7.83049 9.333 7.99977 9.333C8.16871 9.333 8.33149 9.26366 8.48317 9.12663C9.61664 8.10547 13.4698 4.83334 15.6658 2.97362C15.8025 2.85806 15.8234 2.65494 15.7127 2.51366C15.4568 2.18719 15.0753 2 14.6664 2H1.33311C0.924268 2 0.542737 2.18719 0.286893 2.51369C0.176205 2.65494 0.197049 2.85806 0.333768 2.97362Z" fill="rgba(255,255,255,.5)"/><path d="M15.8067 3.98127C15.6885 3.92627 15.5495 3.94546 15.4512 4.02946C13.0159 6.0939 9.90788 8.74008 8.93 9.62124C8.38116 10.1167 7.61944 10.1167 7.06931 9.62058C6.027 8.68146 2.53675 5.71433 0.548813 4.02943C0.449844 3.94543 0.310531 3.9269 0.193344 3.98124C0.0755312 4.03596 0 4.1538 0 4.28368V12.6665C0 13.4019 0.597969 13.9998 1.33334 13.9998H14.6667C15.402 13.9998 16 13.4019 16 12.6665V4.28368C16 4.1538 15.9245 4.03565 15.8067 3.98127Z" fill="rgba(255,255,255,.5)"/></svg>
                                {{ $email_address }}
                            </a></li>
                            @endif
                            @if($phone)
                            <li><a href="tel:{{ $phone }}">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.6043 10.2746L11.6505 8.32085C10.9528 7.62308 9.76655 7.90222 9.48744 8.80928C9.27812 9.4373 8.58035 9.78618 7.95236 9.6466C6.55683 9.29772 4.67287 7.48353 4.32398 6.01822C4.11465 5.39021 4.53331 4.69244 5.1613 4.48314C6.0684 4.20403 6.3475 3.01783 5.64974 2.32007L3.696 0.366327C3.13778 -0.122109 2.30047 -0.122109 1.81203 0.366327L0.486277 1.69208C-0.839476 3.08761 0.62583 6.78576 3.90533 10.0653C7.18482 13.3448 10.883 14.8799 12.2785 13.4843L13.6043 12.1586C14.0927 11.6003 14.0927 10.763 13.6043 10.2746Z" fill="rgba(255,255,255,.5)"/></svg>
                                {{ $phone }}
                            </a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="bl-footer-bottom">
                    {{ \App\CentralLogics\Helpers::get_business_settings('footer_text') }}
                    {{ translate('By') }} <span>{{ \App\CentralLogics\Helpers::get_business_settings('business_name') }}</span>
                </div>
            </div>
        </footer>
        {{-- legacy footer-bg stub removed --}}



        <script src="{{dynamicAsset('/public/assets/landing/assets_new/js/jquery-3.6.0.min.js')}}"></script>
        <script src="{{ dynamicAsset('/public/assets/admin/js/toastr.js') }}"></script>
        <script src="{{ dynamicAsset('/public/assets/admin/js/select2.min.js') }}"></script>
        <script src="{{dynamicAsset('public/assets/admin/intltelinput/js/intlTelInput.min.js')}}"></script>
        {!! Toastr::message() !!}
        @if ($errors->any())
        <script>
            @foreach($errors->all() as $error)
            toastr.error('{{$error}}', Error, { CloseButton: true, ProgressBar: true });
            @endforeach
        </script>
        @endif

        @stack('script_2')

        <script>
        (function() {
            // Preloader
            window.addEventListener('load', function() {
                var p = document.getElementById('bl-preloader');
                if(p){ p.classList.add('hidden'); setTimeout(function(){ p.style.display='none'; }, 500); }
            });
            // Mobile nav toggle
            var toggle = document.getElementById('bl-nav-toggle');
            var nav = document.getElementById('bl-nav');
            if(toggle && nav){
                toggle.addEventListener('click', function(){
                    nav.classList.toggle('open');
                });
            }
            // Services tabs
            document.querySelectorAll('.bl-services-tab').forEach(function(tab){
                tab.addEventListener('click', function(){
                    document.querySelectorAll('.bl-services-tab').forEach(function(t){ t.classList.remove('active'); });
                    document.querySelectorAll('.bl-services-pane').forEach(function(p){ p.classList.remove('active'); });
                    tab.classList.add('active');
                    var target = tab.getAttribute('data-target');
                    var pane = document.getElementById(target);
                    if(pane) pane.classList.add('active');
                });
            });
            // Activate first services tab
            var firstTab = document.querySelector('.bl-services-tab');
            if(firstTab) firstTab.click();
        })();



        document.addEventListener('DOMContentLoaded', function() {
            var telInputs = document.querySelectorAll('input[type="tel"]');
            if(telInputs.length && window.intlTelInput) {
                @if(\App\CentralLogics\Helpers::get_business_settings('country_picker_status') != 1)
                var iti_options = {
                    initialCountry: "{{$countryCode}}",
                    utilsScript: "{{ dynamicAsset('public/assets/admin/intltelinput/js/utils.js') }}",
                    autoInsertDialCode: true,
                    nationalMode: false,
                    formatOnDisplay: false,
                    strictMode: true,
                    onlyCountries: ["{{$countryCode}}"]
                };
                @else
                var iti_options = {
                    initialCountry: "{{$countryCode}}",
                    utilsScript: "{{ dynamicAsset('public/assets/admin/intltelinput/js/utils.js') }}",
                    autoInsertDialCode: true,
                    nationalMode: false,
                    formatOnDisplay: false,
                    strictMode: true
                };
                @endif
                telInputs.forEach(function(input) {
                    window.intlTelInput(input, iti_options);
                });
            }

            function keepNumbersAndPlus(inputString) {
                var regex = /[0-9+]/g;
                var filteredString = inputString.match(regex);
                return filteredString ? filteredString.join('') : '';
            }
            document.addEventListener('keyup', function(e) {
                if(e.target && e.target.type === 'tel') {
                    e.target.value = keepNumbersAndPlus(e.target.value);
                }
            });
        });
        </script>


    </body>

    </html>
