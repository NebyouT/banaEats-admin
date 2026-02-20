@extends('layouts.admin.app')
@section('title', translate('messages.Admin_Landing_Page'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header" style="padding-bottom:0;">
            <div class="lp-page-header">
                <h1 class="lp-page-title">
                    <span class="lp-page-title-icon"><i class="tio-globe"></i></span>
                    {{ translate('Landing_Page_Settings') }}
                </h1>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="lp-btn-reset" data-toggle="modal" data-target="#how-it-works" style="display:inline-flex;align-items:center;gap:6px;">
                        <i class="tio-info-outlined"></i> {{ translate('How_it_works') }}
                    </button>
                    <a href="{{ url('/') }}" target="_blank" class="lp-visit-btn">
                        <i class="tio-open-in-new"></i> {{ translate('Visit_Site') }}
                    </a>
                </div>
            </div>
            @include('admin-views.landing_page.top_menu.admin_landing_menu')
        </div>
        <div class="card lp-card mt-3">
            <div class="card-body">
                <form action="{{ route('admin.business-settings.landing-page-settings', 'background-change') }}"
                    method="POST">

                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <label  for="primary_1_hex" class="form-label d-block text-center">{{ translate('Primary_Color') }}</label>
                            <input  id="primary_1_hex" name="header-bg" type="color" class="form-control form-control-color" value="{{ data_get($backgroundChange,'primary_1_hex','#EF7822') }}" required>
                        </div>
{{--                        <div class="col-sm-6">--}}
{{--                            <label for="primary_2_hex"  class="form-label d-block text-center">{{ translate('Primary_Color_2') }}</label>--}}
{{--                            <input id="primary_2_hex"  name="footer-bg" type="color" class="form-control form-control-color"--}}
{{--                                   value="{{ data_get($backgroundChange,'primary_2_hex','#333E4F') }}" required>--}}
{{--                        </div>--}}

                    </div>
                    <div class="lp-form-actions">
                        <button type="submit" class="lp-btn-save">{{ translate('messages.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

