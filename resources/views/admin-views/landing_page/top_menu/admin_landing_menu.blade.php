<nav class="lp-tab-nav">
    <a href="{{ route('admin.landing_page.header') }}"
       class="{{ Request::is('admin/landing-page/header') ? 'active' : '' }}">
        <i class="tio-home-outlined"></i> {{ translate('messages.Header') }}
    </a>
    <a href="{{ route('admin.landing_page.about_us') }}"
       class="{{ Request::is('admin/landing-page/about-us') ? 'active' : '' }}">
        <i class="tio-info-outlined"></i> {{ translate('messages.about_us') }}
    </a>
    <a href="{{ route('admin.landing_page.features') }}"
       class="{{ Request::is('admin/landing-page/feature*') ? 'active' : '' }}">
        <i class="tio-star-outlined"></i> {{ translate('messages.Features') }}
    </a>
    <a href="{{ route('admin.landing_page.services') }}"
       class="{{ Request::is('admin/landing-page/services') ? 'active' : '' }}">
        <i class="tio-layers-outlined"></i> {{ translate('messages.Services') }}
    </a>
    <a href="{{ route('admin.landing_page.earn_money') }}"
       class="{{ Request::is('admin/landing-page/earn-money') ? 'active' : '' }}">
        <i class="tio-money"></i> {{ translate('messages.Earn_money') }}
    </a>
    <a href="{{ route('admin.landing_page.why_choose_us') }}"
       class="{{ Request::is('admin/landing-page/why-choose-us*') ? 'active' : '' }}">
        <i class="tio-checkmark-circle-outlined"></i> {{ translate('messages.why_choose_us') }}
    </a>
    <a href="{{ route('admin.landing_page.testimonial') }}"
       class="{{ Request::is('admin/landing-page/testimonial*') ? 'active' : '' }}">
        <i class="tio-comment-outlined"></i> {{ translate('messages.Testimonials') }}
    </a>
    <a href="{{ route('admin.landing_page.available_zone') }}"
       class="{{ Request::is('admin/landing-page/available-zone*') ? 'active' : '' }}">
        <i class="tio-map-outlined"></i> {{ translate('messages.available_zone') }}
    </a>
    <a href="{{ route('admin.landing_page.fixed_data') }}"
       class="{{ Request::is('admin/landing-page/fixed-data*') ? 'active' : '' }}">
        <i class="tio-chart-bar-1"></i> {{ translate('messages.Fixed_data') }}
    </a>
    <a href="{{ route('admin.landing_page.links') }}"
       class="{{ Request::is('admin/landing-page/links*') ? 'active' : '' }}">
        <i class="tio-link"></i> {{ translate('messages.button_&_links') }}
    </a>
    <a href="{{ route('admin.landing_page.backgroung_color') }}"
       class="{{ Request::is('admin/landing-page/backgroung-color') ? 'active' : '' }}">
        <i class="tio-color-picker-outlined"></i> {{ translate('messages.Background_color') }}
    </a>
</nav>

{{-- How it Works modal (kept for functionality) --}}
<div class="modal fade" id="how-it-works">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0;padding:16px 20px;">
                <h6 style="font-weight:700;color:#1A1A1A;margin:0;">{{ translate('How it works') }}</h6>
                <button type="button" class="close" data-dismiss="modal" style="font-size:18px;">
                    <span class="tio-clear"></span>
                </button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div class="single-item-slider owl-carousel">
                    <div class="item text-center">
                        <div style="width:56px;height:56px;background:rgba(245,216,0,.15);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px;color:#B89E00;">
                            <i class="tio-info-outlined"></i>
                        </div>
                        <h6 style="font-weight:700;color:#1A1A1A;margin-bottom:8px;">{{ translate('Notice!') }}</h6>
                        <p style="font-size:13px;color:#888;line-height:1.6;">
                            {{ translate("If_you_want_to_disable_or_turn_off_any_section_please_leave_that_section_empty_don't_make_any_changes_there!") }}
                        </p>
                    </div>
                    <div class="item text-center">
                        <div style="width:56px;height:56px;background:rgba(141,198,63,.12);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px;color:#6FA82E;">
                            <i class="tio-world"></i>
                        </div>
                        <h6 style="font-weight:700;color:#1A1A1A;margin-bottom:8px;">{{ translate('If_You_Want_to_Change_Language') }}</h6>
                        <p style="font-size:13px;color:#888;line-height:1.6;">
                            {{ translate("Change_the_language_on_tab_bar_and_input_your_data_again!") }}
                        </p>
                    </div>
                    <div class="item text-center">
                        <div style="width:56px;height:56px;background:rgba(26,26,26,.08);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px;color:#1A1A1A;">
                            <i class="tio-eye-outlined"></i>
                        </div>
                        <h6 style="font-weight:700;color:#1A1A1A;margin-bottom:8px;">{{ translate('Let\'s_See_The_Changes!') }}</h6>
                        <p style="font-size:13px;color:#888;line-height:1.6;">
                            {{ translate('Visit_landing_page_to_see_the_changes_you_made_in_the_settings_option!') }}
                        </p>
                        <a href="{{ url('/') }}" class="lp-btn-save" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;margin-top:8px;">
                            <i class="tio-open-in-new"></i> {{ translate('Visit_Now') }}
                        </a>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <div class="slide-counter"></div>
                </div>
            </div>
        </div>
    </div>
</div>
