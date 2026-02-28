@extends('layouts.admin.app')

@section('title', translate('Create Page'))

@push('css_or_js')
<style>
.template-card {
    border: 2px solid #e7eaf3;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s;
}
.template-card:hover, .template-card.selected {
    border-color: #FC6A57;
    box-shadow: 0 4px 15px rgba(252,106,87,0.15);
}
.template-card.selected::after {
    content: '\2713';
    position: absolute;
    top: 10px;
    right: 10px;
    background: #FC6A57;
    color: #fff;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
.template-thumb {
    height: 120px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    display: flex;
    align-items: center;
    justify-content: center;
}
.template-thumb i {
    font-size: 40px;
    color: #adb5bd;
}
.template-body {
    padding: 12px;
    text-align: center;
}
.template-name {
    font-size: 13px;
    font-weight: 600;
    color: #1e2022;
}
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-add-circle text-primary"></i></span>
                    {{ translate('Create New Page') }}
                </h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.page-builder.index') }}" class="btn btn-outline-secondary">
                    <i class="tio-arrow-backward mr-1"></i> {{ translate('Back') }}
                </a>
            </div>
        </div>
    </div>

    <form id="create-page-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Page Info -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('Page Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="input-label">{{ translate('Page Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="page-title" class="form-control" placeholder="{{ translate('e.g. Summer Sale, New Arrivals') }}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="input-label">{{ translate('Description') }}</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="{{ translate('Brief description of this page') }}"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Templates -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('Start with a Template') }} <small class="text-muted">({{ translate('Optional') }})</small></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Blank Template -->
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="template-card selected" data-template="blank">
                                    <div class="template-thumb">
                                        <i class="tio-document-text-outlined"></i>
                                    </div>
                                    <div class="template-body">
                                        <div class="template-name">{{ translate('Blank Page') }}</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Promotion Template -->
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="template-card" data-template="promotion">
                                    <div class="template-thumb" style="background: linear-gradient(135deg, #FC6A57, #ff8a75)">
                                        <i class="tio-gift text-white"></i>
                                    </div>
                                    <div class="template-body">
                                        <div class="template-name">{{ translate('Promotion') }}</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Products Showcase -->
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="template-card" data-template="products">
                                    <div class="template-thumb" style="background: linear-gradient(135deg, #8DC63F, #a8d96a)">
                                        <i class="tio-restaurant text-white"></i>
                                    </div>
                                    <div class="template-body">
                                        <div class="template-name">{{ translate('Products') }}</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Restaurants -->
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="template-card" data-template="restaurants">
                                    <div class="template-thumb" style="background: linear-gradient(135deg, #F5D800, #ffe44d)">
                                        <i class="tio-shop text-white"></i>
                                    </div>
                                    <div class="template-body">
                                        <div class="template-name">{{ translate('Restaurants') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Page Type -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('Page Type') }}</h5>
                    </div>
                    <div class="card-body">
                        <select name="page_type" class="form-control">
                            <option value="custom">{{ translate('Custom Page') }}</option>
                            <option value="promotion">{{ translate('Promotion') }}</option>
                            <option value="category">{{ translate('Category Page') }}</option>
                            <option value="landing">{{ translate('Landing Page') }}</option>
                            <option value="event">{{ translate('Event Page') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block" id="create-btn">
                            <i class="tio-add mr-1"></i> {{ translate('Create & Edit Page') }}
                        </button>
                        <a href="{{ route('admin.page-builder.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                            {{ translate('Cancel') }}
                        </a>
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
    // Template selection
    $('.template-card').on('click', function() {
        $('.template-card').removeClass('selected');
        $(this).addClass('selected');
    });

    // Form submit
    $('#create-page-form').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $('#create-btn');
        btn.prop('disabled', true).html('<i class="tio-loading tio-spin mr-1"></i> Creating...');

        const template = $('.template-card.selected').data('template');
        const formData = new FormData(this);
        formData.append('template', template);

        $.ajax({
            url: '{{ route("admin.page-builder.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.redirect) {
                    toastr.success(response.message || 'Page created!');
                    window.location.href = response.redirect;
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="tio-add mr-1"></i> Create & Edit Page');
                const res = xhr.responseJSON;
                if (res && res.errors) {
                    res.errors.forEach(e => toastr.error(e.message));
                } else {
                    toastr.error('Something went wrong');
                }
            }
        });
    });
});
</script>
@endpush
