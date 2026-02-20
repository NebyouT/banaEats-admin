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

        @php($default_lang = str_replace('_', '-', app()->getLocale()))
        @if($language)
            <ul class="nav lp-lang-tabs">
                <li class="nav-item">
                    <a class="nav-link lang_link active" href="#" id="default-link">{{ translate('messages.default') }}</a>
                </li>
                @foreach (json_decode($language) as $lang)
                    <li class="nav-item">
                        <a class="nav-link lang_link" href="#" id="{{ $lang }}-link">
                            {{ \App\CentralLogics\Helpers::get_language_name($lang) . ' (' . strtoupper($lang) . ')' }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
            <div class="lp-section-label">
                <span class="lp-section-label-dot"></span>
                <span class="lp-section-label-text">{{ translate('Newsletter') }}</span>
            </div>
            <div class="card lp-card">
                <form action="{{ route('admin.landing_page.settings', 'fixed-data-newsletter') }}" method="post">
                    @csrf
                <div class="card-body">
                    <div class="row g-3 lang_form default-form" id="default-form">
                        <input type="hidden" name="lang[]" value="default">
                        <div class="col-sm-6">
                            <label  for="title"  class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})
                              <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                            <i class="tio-info-outined"></i>
                        </span>
                            </label>
                            <input id="title" type="text" maxlength="30"  name="title[]" value="{{ $news_letter_title?->getRawOriginal('value') ?? null}}" class="form-control" placeholder="{{translate('Enter_Title')}}">
                            <input type="hidden" name="key" value="news_letter_title" >
                        </div>
                        <div class="col-sm-6">
                            <label for="sub_title"   class="form-label">{{translate('Subtitle')}} ({{ translate('messages.default') }})
                              <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_subtitle_within_70_characters') }}">
                            <i class="tio-info-outined"></i>
                        </span>
                            </label>
                            <input id="sub_title" type="text" maxlength="70"  name="sub_title[]" value="{{ $news_letter_sub_title?->getRawOriginal('value') ?? null}}"  class="form-control" placeholder="{{translate('Enter_Sub_Title')}}">
                            <input type="hidden" name="key_2" value="news_letter_sub_title" >
                        </div>
                    </div>

                    @forelse(json_decode($language) as $lang)
                    <?php
                    if($news_letter_title?->translations){
                            $news_letter_title_translate = [];
                            foreach($news_letter_title->translations as $t)
                            {
                                if($t->locale == $lang && $t->key=='news_letter_title'){
                                    $news_letter_title_translate[$lang]['value'] = $t->value;
                                }
                            }
                        }
                    if($news_letter_sub_title?->translations){
                            $news_letter_sub_title_translate = [];
                            foreach($news_letter_sub_title->translations as $t)
                            {
                                if($t->locale == $lang && $t->key=='news_letter_sub_title'){
                                    $news_letter_sub_title_translate[$lang]['value'] = $t->value;
                                }
                            }
                        }
                        ?>

                    <div class="row g-3 d-none lang_form" id="{{$lang}}-form">
                        <input type="hidden" name="lang[]" value="{{$lang}}">
                        <div class="col-sm-6">
                            <label for="title{{$lang}}" class="form-label">{{translate('Title')}} ({{strtoupper($lang)}})
                             <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                            <i class="tio-info-outined"></i>
                        </span>
                            </label>
                            <input id="title{{$lang}}" type="text" name="title[]"   maxlength="30" value="{{ $news_letter_title_translate[$lang]['value'] ?? '' }}" class="form-control" placeholder="{{translate('Enter_Title')}}">
                        </div>
                        <div class="col-sm-6">
                            <label for="sub_title{{$lang}}" class="form-label">{{translate('Subtitle')}} ({{strtoupper($lang)}})
                             <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_subtitle_within_70_characters') }}">
                            <i class="tio-info-outined"></i>
                        </span>
                            </label>
                            <input id="sub_title{{$lang}}" type="text" name="sub_title[]" maxlength="70" value="{{ $news_letter_sub_title_translate[$lang]['value'] ?? '' }}" class="form-control" placeholder="{{translate('Enter_Title')}}">
                        </div>
                    </div>
                    @empty
                    @endforelse

                    <div class="lp-form-actions">
                        <button type="reset" class="lp-btn-reset">{{ translate('Reset') }}</button>
                        <button type="submit" class="lp-btn-save">{{ translate('Save') }}</button>
                    </div>
                </div>
                </form>
            </div>
            <div class="lp-section-label mt-4">
                <span class="lp-section-label-dot"></span>
                <span class="lp-section-label-text">{{ translate('Footer_Short_Description') }}</span>
            </div>
            <div class="card lp-card">
                <form action="{{ route('admin.landing_page.settings', 'fixed-data-footer') }}" method="post">
                    @csrf
                <div class="card-body">
                    <div class="row g-3 lang_form default-form" >
                        <input type="hidden" name="lang[]" value="default">

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="footer_data" class="form-label">{{translate('messages.Footer_description')}} ({{ translate('messages.default') }})
                                    <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_footer_description_within_300_characters') }}">
                                        <i class="tio-info-outined"></i>
                                    </span>

                                </label>
                                <input type="hidden" name="footer_key" value="footer_data" >
                                <textarea rows="5" id="footer_data" maxlength="300"   class="form-control" name="footer_data[]" placeholder="{{translate('messages.Short_Description')}}">{{ $footer_data?->getRawOriginal('value') ?? null}}</textarea>
                            </div>
                        </div>
                    </div>

                    @forelse(json_decode($language) as $lang)
                        <input type="hidden" name="lang[]" value="{{$lang}}">
                        <?php
                            if($footer_data?->translations){
                                    $footer_data_translate = [];
                                    foreach($footer_data->translations as $t)
                                    {
                                        if($t->locale == $lang && $t->key=='footer_data'){
                                            $footer_data_translate[$lang]['value'] = $t->value;
                                        }
                                    }
                                }
                            ?>
                        <div class="row g-3  d-none lang_form" id="{{$lang}}-form1">

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="footer_data{{$lang}}" class="form-label">{{translate('messages.Footer_description')}} ({{strtoupper($lang)}})
                                        <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_footer_description_within_300_characters') }}">
                                            <i class="tio-info-outined"></i>
                                        </span> </label>
                                    <textarea id="footer_data{{$lang}}" rows="5" class="form-control" maxlength="300" name="footer_data[]" placeholder="{{translate('messages.Short_Description')}}">{{ $footer_data_translate[$lang]['value'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        @empty
                    @endforelse
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" class="btn btn--reset">{{translate('Reset')}}</button>
                        <button type="submit"   class="btn btn--primary">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>
            </div>
        </div>

@endsection
