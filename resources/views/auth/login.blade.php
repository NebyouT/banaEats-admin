<!DOCTYPE html>
    <?php
    $log_email_succ = session()->get('log_email_succ');
    ?>
<html dir="{{ $site_direction }}" lang="{{ $locale }}" class="{{ $site_direction === 'rtl'?'active':'' }}">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @php
        $app_name = \App\CentralLogics\Helpers::get_business_settings('business_name', false);
        $icon = \App\CentralLogics\Helpers::get_business_settings('icon', false);
    @endphp
    <!-- Title -->
    <title>{{ translate('messages.login') }} | {{$app_name??translate('STACKFOOD')}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset($icon ? 'storage/app/public/business/'.$icon : 'public/favicon.ico')}}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/style.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/toastr.css">
    <style>
        *{font-family:'Inter',sans-serif;box-sizing:border-box}
        body{margin:0;padding:0;background:#f5f5f5}
        .bana-login-wrap{display:flex;min-height:100vh}
        .bana-brand-panel{width:45%;background:#1A1A1A;display:flex;flex-direction:column;justify-content:space-between;padding:48px 56px;position:relative;overflow:hidden}
        .bana-brand-panel::before{content:'';position:absolute;top:-120px;right:-120px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(245,216,0,.12) 0%,transparent 70%);pointer-events:none}
        .bana-brand-panel::after{content:'';position:absolute;bottom:-80px;left:-80px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(141,198,63,.10) 0%,transparent 70%);pointer-events:none}
        .bana-brand-logo{display:flex;align-items:center;gap:14px;text-decoration:none}
        .bana-brand-logo img{height:48px;width:auto;object-fit:contain;filter:brightness(0) invert(1)}
        .bana-brand-logo-text{font-size:22px;font-weight:800;color:#F5D800;letter-spacing:-.5px}
        .bana-brand-body{flex:1;display:flex;flex-direction:column;justify-content:center;padding:60px 0 40px;position:relative;z-index:1}
        .bana-brand-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(245,216,0,.12);border:1px solid rgba(245,216,0,.25);color:#F5D800;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;padding:6px 14px;border-radius:100px;width:fit-content;margin-bottom:28px}
        .bana-brand-tag::before{content:'';width:6px;height:6px;border-radius:50%;background:#F5D800}
        .bana-brand-headline{font-size:42px;font-weight:800;line-height:1.15;color:#fff;margin:0 0 20px;letter-spacing:-1px}
        .bana-brand-headline span{color:#F5D800}
        .bana-brand-desc{font-size:15px;color:rgba(255,255,255,.55);line-height:1.7;max-width:340px;margin:0 0 40px}
        .bana-stats-row{display:flex;gap:32px}
        .bana-stat{display:flex;flex-direction:column}
        .bana-stat-num{font-size:26px;font-weight:800;color:#8DC63F;line-height:1}
        .bana-stat-label{font-size:11px;color:rgba(255,255,255,.45);margin-top:4px;text-transform:uppercase;letter-spacing:.06em}
        .bana-brand-footer p{font-size:12px;color:rgba(255,255,255,.3);margin:0}
        .bana-form-panel{flex:1;background:#fff;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:48px 40px;position:relative}
        .bana-version-badge{position:absolute;top:24px;right:24px;background:rgba(141,198,63,.10);color:#5a8a1a;font-size:11px;font-weight:600;padding:4px 10px;border-radius:100px;border:1px solid rgba(141,198,63,.25)}
        .bana-form-inner{width:100%;max-width:400px}
        .bana-form-header{margin-bottom:36px}
        .bana-form-header h1{font-size:28px;font-weight:800;color:#1A1A1A;margin:0 0 8px;letter-spacing:-.5px}
        .bana-form-header p{font-size:14px;color:#888;margin:0}
        .bana-form-group{margin-bottom:20px}
        .bana-form-group label{display:block;font-size:13px;font-weight:600;color:#333;margin-bottom:8px}
        .bana-input-wrap{position:relative}
        .bana-input-wrap .bana-input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#aaa;font-size:16px;pointer-events:none}
        .bana-input-wrap input{width:100%;height:50px;padding:0 44px 0 42px;border:1.5px solid #e8e8e8;border-radius:10px;font-size:14px;color:#1A1A1A;background:#fafafa;transition:border-color .2s,box-shadow .2s,background .2s;outline:none}
        .bana-input-wrap input:focus{border-color:#8DC63F;background:#fff;box-shadow:0 0 0 3px rgba(141,198,63,.12)}
        .bana-input-wrap .bana-pass-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#aaa;cursor:pointer;font-size:16px;padding:0}
        .bana-input-wrap .bana-pass-toggle:hover{color:#8DC63F}
        .bana-form-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px}
        .bana-remember{display:flex;align-items:center;gap:8px;font-size:13px;color:#555;cursor:pointer}
        .bana-remember input[type=checkbox]{width:16px;height:16px;accent-color:#8DC63F;cursor:pointer}
        .bana-forgot{font-size:13px;color:#8DC63F;font-weight:600;text-decoration:none;cursor:pointer;background:none;border:none;padding:0}
        .bana-forgot:hover{color:#6FA82E;text-decoration:underline}
        .bana-captcha-wrap{display:flex;gap:10px;margin-bottom:20px;align-items:stretch}
        .bana-captcha-wrap input{flex:1;height:46px;padding:0 14px;border:1.5px solid #e8e8e8;border-radius:10px;font-size:14px;background:#fafafa;outline:none}
        .bana-captcha-wrap input:focus{border-color:#8DC63F;box-shadow:0 0 0 3px rgba(141,198,63,.12)}
        .bana-captcha-img-wrap{display:flex;align-items:center;gap:6px;background:#f5f5f5;border:1.5px solid #e8e8e8;border-radius:10px;padding:4px 10px}
        .bana-captcha-img-wrap img{height:36px;border-radius:6px}
        .bana-captcha-img-wrap .capcha-spin{color:#8DC63F;cursor:pointer;font-size:18px;padding:0}
        .bana-btn-submit{width:100%;height:52px;background:#8DC63F;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:background .2s,transform .1s,box-shadow .2s;letter-spacing:.02em;box-shadow:0 4px 16px rgba(141,198,63,.30)}
        .bana-btn-submit:hover{background:#6FA82E;box-shadow:0 6px 20px rgba(141,198,63,.40);transform:translateY(-1px)}
        .bana-btn-submit:active{transform:translateY(0)}
        .bana-switch-link{text-align:center;margin-top:20px;font-size:13px;color:#888}
        .bana-switch-link a{color:#8DC63F;font-weight:600;text-decoration:none}
        .bana-switch-link a:hover{text-decoration:underline}
        .bana-demo-box{margin-top:24px;background:#f9fdf2;border:1px solid rgba(141,198,63,.25);border-radius:10px;padding:14px 16px}
        .bana-demo-box .bana-demo-row{display:flex;justify-content:space-between;align-items:center}
        .bana-demo-box span{font-size:12px;color:#555;display:block}
        .bana-demo-box strong{color:#1A1A1A}
        .bana-demo-copy{background:#8DC63F;color:#fff;border:none;border-radius:8px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;flex-shrink:0}
        .bana-demo-copy:hover{background:#6FA82E}
        @media(max-width:991px){.bana-brand-panel{width:40%;padding:40px 36px}.bana-brand-headline{font-size:32px}}
        @media(max-width:767px){.bana-brand-panel{display:none}.bana-form-panel{padding:40px 24px}}
    </style>
</head>

<body>
<main id="content" role="main">
    @php($systemlogo=\App\Models\BusinessSetting::where(['key'=>'logo'])->first())
    @php($role = $role ?? null)
    @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))

    <div class="bana-login-wrap">

        {{-- ── Left brand panel ── --}}
        <div class="bana-brand-panel">
            <a class="bana-brand-logo" href="javascript:">
                <img src="{{ \App\CentralLogics\Helpers::get_full_url('business',$systemlogo?->value,$systemlogo?->storage[0]?->value ?? 'public', 'authfav') }}"
                     onerror="this.src='{{ dynamicAsset('/public/assets/admin/img/auth-fav.png') }}'" alt="logo">
                <span class="bana-brand-logo-text">{{ $app_name ?? 'BanaEats' }}</span>
            </a>

            <div class="bana-brand-body">
                <div class="bana-brand-tag">Admin Control Center</div>
                <h1 class="bana-brand-headline">
                    Deliver <span>faster.</span><br>
                    Manage <span>smarter.</span>
                </h1>
                <p class="bana-brand-desc">
                    Your all-in-one operations hub. Monitor orders, manage restaurants, track delivery partners, and grow your business — all from one place.
                </p>
                <div class="bana-stats-row">
                    <div class="bana-stat">
                        <span class="bana-stat-num">99.9%</span>
                        <span class="bana-stat-label">Uptime</span>
                    </div>
                    <div class="bana-stat">
                        <span class="bana-stat-num">Real-time</span>
                        <span class="bana-stat-label">Order tracking</span>
                    </div>
                    <div class="bana-stat">
                        <span class="bana-stat-num">24/7</span>
                        <span class="bana-stat-label">Operations</span>
                    </div>
                </div>
            </div>

            <div class="bana-brand-footer">
                <p>&copy; {{ date('Y') }} {{ $app_name ?? 'BanaEats' }}. All rights reserved.</p>
            </div>
        </div>

        {{-- ── Right form panel ── --}}
        <div class="bana-form-panel">
            <span class="bana-version-badge">v{{env('SOFTWARE_VERSION')}}</span>

            <div class="bana-form-inner">
                <div class="bana-form-header">
                    @if ($role == 'vendor')
                        <h1>{{ translate('messages.Signin_To_Your_Restaurant_Panel') }}</h1>
                        <p>{{ translate('Enter your credentials to access the restaurant panel') }}</p>
                    @else
                        <h1>{{ translate('messages.Signin_To_Your_Panel') }}</h1>
                        <p>{{ translate('Enter your credentials to access the admin panel') }}</p>
                    @endif
                </div>

                <form action="{{route('login_post')}}" method="post" id="form-id">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}">

                    {{-- Email --}}
                    <div class="bana-form-group">
                        <label for="signinSrEmail">{{ translate('messages.your_email') }}</label>
                        <div class="bana-input-wrap">
                            <i class="tio-email-outlined bana-input-icon"></i>
                            <input type="email" id="signinSrEmail" name="email"
                                   value="{{ $email ?? '' }}" required
                                   placeholder="you@example.com" autocomplete="email">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="bana-form-group">
                        <label for="signupSrPassword">{{ translate('messages.password') }}</label>
                        <div class="bana-input-wrap">
                            <i class="tio-lock-outlined bana-input-icon"></i>
                            <input type="password" id="signupSrPassword" name="password"
                                   value="{{ $password ?? '' }}" required
                                   placeholder="••••••••" autocomplete="current-password">
                            <button type="button" class="bana-pass-toggle" id="passToggleBtn">
                                <i class="tio-visible-outlined" id="passToggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember / Forgot --}}
                    <div class="bana-form-row">
                        <label class="bana-remember">
                            <input type="checkbox" name="remember" {{ $password ? 'checked' : '' }}>
                            {{ translate('messages.remember_me') }}
                        </label>
                        @if ($role == 'admin')
                            <button type="button" class="bana-forgot" data-toggle="modal" data-target="#forgetPassModal">
                                {{ translate('Forget_Password?') }}
                            </button>
                        @elseif ($role == 'vendor')
                            <button type="button" class="bana-forgot" data-toggle="modal" data-target="#forgetPassModal1">
                                {{ translate('Forget_Password?') }}
                            </button>
                        @endif
                    </div>

                    {{-- Captcha --}}
                    @if(isset($recaptcha) && $recaptcha['status'] == 1)
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                        <input type="hidden" name="set_default_captcha" id="set_default_captcha_value" value="0">
                        <div class="bana-captcha-wrap d-none" id="reload-captcha">
                            <input type="text" name="custome_recaptcha" id="custome_recaptcha" required
                                   placeholder="{{ translate('Enter recaptcha value') }}" autocomplete="off"
                                   value="{{ env('APP_MODE')=='dev' ? session('six_captcha') : '' }}">
                            <div class="bana-captcha-img-wrap">
                                <img src="<?php echo $custome_recaptcha->inline(); ?>" alt="captcha">
                                <span class="capcha-spin reloadCaptcha"><i class="tio-cached"></i></span>
                            </div>
                        </div>
                    @else
                        <div class="bana-captcha-wrap" id="reload-captcha">
                            <input type="text" name="custome_recaptcha" id="custome_recaptcha" required
                                   placeholder="{{ translate('Enter recaptcha value') }}" autocomplete="off"
                                   value="{{ env('APP_MODE')=='dev' ? session('six_captcha') : '' }}">
                            <div class="bana-captcha-img-wrap">
                                <img src="<?php echo $custome_recaptcha->inline(); ?>" alt="captcha">
                                <span class="capcha-spin reloadCaptcha"><i class="tio-cached"></i></span>
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="bana-btn-submit" id="signInBtn">
                        {{ translate('messages.sign_in') }}
                    </button>

                    @if ($role == 'admin')
                        @php($vendorLoginSlug = \App\Models\DataSetting::where('type','login_restaurant')->pluck('value')->first() ?? 'restaurant')
                        <p class="bana-switch-link">
                            {{ translate('Login as Restaurant Owner?') }}
                            <a href="{{ url('/') }}/login/{{ $vendorLoginSlug }}">{{ translate('Login Here') }}</a>
                        </p>
                    @endif
                </form>

                {{-- Demo credentials --}}
                @if(env('APP_MODE') == 'demo')
                    @if(isset($role) && $role == 'admin')
                        <div class="bana-demo-box">
                            <div class="bana-demo-row">
                                <div>
                                    <span><strong>Email:</strong> admin@admin.com</span>
                                    <span><strong>Password:</strong> 12345678</span>
                                </div>
                                <button class="bana-demo-copy" id="copy_cred"><i class="tio-copy"></i></button>
                            </div>
                        </div>
                    @endif
                    @if(isset($role) && $role == 'vendor')
                        <div class="bana-demo-box">
                            <div class="bana-demo-row">
                                <div>
                                    <span><strong>Email:</strong> test.restaurant@gmail.com</span>
                                    <span><strong>Password:</strong> 12345678</span>
                                </div>
                                <button class="bana-demo-copy" id="copy_cred2"><i class="tio-copy"></i></button>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</main>


<div class="modal fade" id="forgetPassModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header justify-content-end">
          <span type="button" class="close-modal-icon" data-dismiss="modal">
              <i class="tio-clear"></i>
          </span>
        </div>
        <div class="modal-body">
          <div class="forget-pass-content">
              <img src="{{dynamicAsset('/public/assets/admin/img/send-mail.svg')}}" alt="">
              <!-- After Succeed -->
              <h4>
                  {{ translate('Send_Mail_to_Your_Email_?') }}
              </h4>
              <p>
                  {{ translate('A_mail_will_be_send_to_your_registered_email_with_a_link_to_change_passowrd') }}
              </p>
              <a class="btn btn-lg btn-block btn--primary mt-3" href="{{route('reset-password')}}">
                  {{ translate('Send_Mail') }}
              </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="forgetPassModal1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header justify-content-end">
          <span type="button" class="close-modal-icon" data-dismiss="modal">
              <i class="tio-clear"></i>
          </span>
        </div>
        <div class="modal-body">
          <div class="forget-pass-content">
              <img src="{{dynamicAsset('/public/assets/admin/img/send-mail.svg')}}" alt="">
              <!-- After Succeed -->
              <h4>
                  {{ translate('messages.Send_Mail_to_Your_Email_?') }}
              </h4>
              <form class="" action="{{ route('vendor-reset-password') }}" method="post">
                  @csrf

                  <input type="email" name="email" id="" class="form-control" required>
                  <button type="submit" class="btn btn-lg btn-block btn--primary mt-3">{{ translate('messages.Send_Mail') }}</button>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="successMailModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header justify-content-end">
            <span type="button" class="close-modal-icon" data-dismiss="modal">
                <i class="tio-clear"></i>
            </span>
          </div>
          <div class="modal-body">
            <div class="forget-pass-content">
                <!-- After Succeed -->
                <img src="{{dynamicAsset('/public/assets/admin/img/sent-mail.svg')}}" alt="">
                <h4>
                  {{ translate('A_mail_has_been_sent_to_your_registered_email') }}!
                </h4>
                <p>
                  {{ translate('Click_the_link_in_the_mail_description_to_change_password') }}
                </p>
            </div>
          </div>
        </div>
      </div>
    </div>


<!-- JS Implementing Plugins -->
<script src="{{dynamicAsset('public/assets/admin')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{dynamicAsset('public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{dynamicAsset('public/assets/admin')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{translate($error)}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif
@if ($log_email_succ)
@php(session()->forget('log_email_succ'))
    <script>
        $('#successMailModal').modal('show');
    </script>
@endif

<script>
    // Password visibility toggle
    document.getElementById('passToggleBtn').addEventListener('click', function() {
        var input = document.getElementById('signupSrPassword');
        var icon  = document.getElementById('passToggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'tio-hidden-outlined';
        } else {
            input.type = 'password';
            icon.className = 'tio-visible-outlined';
        }
    });
</script>

<script>
    $(document).on('click', '.reloadCaptcha', function(){
        $.ajax({
            url: "{{ route('reload-captcha') }}",
            type: "GET",
            dataType: 'json',
            beforeSend: function () { $('.capcha-spin').addClass('active'); },
            success: function(data) { $('#reload-captcha').html(data.view); },
            complete: function () { $('.capcha-spin').removeClass('active'); }
        });
    });
</script>

@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script src="https://www.google.com/recaptcha/api.js?render={{$recaptcha['site_key']}}"></script>
@endif
@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script>
        $(document).ready(function() {
            $('#signInBtn').click(function (e) {
                if( $('#set_default_captcha_value').val() == 1){
                    $('#form-id').submit();
                    return true;
                }
                e.preventDefault();
                if (typeof grecaptcha === 'undefined') {
                    toastr.error('Invalid recaptcha key provided. Please check the recaptcha configuration.');
                    $('#reload-captcha').removeClass('d-none');
                    $('#set_default_captcha_value').val('1');

                    return;
                }
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{$recaptcha['site_key']}}', {action: 'submit'}).then(function (token) {
                        $('#g-recaptcha-response').value = token;
                        $('#form-id').submit();
                    });
                });
                window.onerror = function (message) {
                    var errorMessage = 'An unexpected error occurred. Please check the recaptcha configuration';
                    if (message.includes('Invalid site key')) {
                        errorMessage = 'Invalid site key provided. Please check the recaptcha configuration.';
                    } else if (message.includes('not loaded in api.js')) {
                        errorMessage = 'reCAPTCHA API could not be loaded. Please check the recaptcha API configuration.';
                    }
                    $('#reload-captcha').removeClass('d-none');
                    $('#set_default_captcha_value').val('1');
                    toastr.error(errorMessage)
                    return true;
                };
            });
        });
    </script>
@endif
{{-- recaptcha scripts end --}}



@if(env('APP_MODE') =='demo')
    <script>
        $("#copy_cred").click(function() {
            $('#signinSrEmail').val('admin@admin.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        })
        $("#copy_cred2").click(function() {
            $('#signinSrEmail').val('test.restaurant@gmail.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        })
    </script>
@endif

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{dynamicAsset('public//assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
