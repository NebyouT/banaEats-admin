<style>
    /* ── BanaEats Header ── */
    #header.navbar {
        background: #fff !important;
        border-bottom: 2px solid #F5D800 !important;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important;
        padding: 0 20px !important;
        height: 62px !important;
    }
    .bana-header-search-btn {
        display: flex; align-items: center; gap: 8px;
        background: #f4f6f9; border: 1.5px solid #e8e8e8;
        border-radius: 10px; padding: 7px 14px;
        color: #888; font-size: 13px; cursor: pointer;
        transition: border-color .2s, color .2s; white-space: nowrap;
    }
    .bana-header-search-btn:hover { border-color: #8DC63F; color: #8DC63F; }
    .bana-header-search-btn .shortcut {
        background: #fff; border: 1px solid #e0e0e0;
        border-radius: 5px; padding: 1px 6px;
        font-size: 10px; font-weight: 700; color: #aaa;
        margin-left: 8px;
    }
    .bana-header-icon-btn {
        width: 38px; height: 38px; border-radius: 10px;
        background: #f4f6f9; border: none; display: flex;
        align-items: center; justify-content: center;
        color: #555; font-size: 17px; cursor: pointer;
        transition: background .2s, color .2s; position: relative;
        text-decoration: none;
    }
    .bana-header-icon-btn:hover { background: rgba(141,198,63,.12); color: #8DC63F; }
    .bana-header-badge {
        position: absolute; top: -3px; right: -3px;
        background: #ff4040; color: #fff; font-size: 9px;
        font-weight: 700; min-width: 16px; height: 16px;
        border-radius: 8px; display: flex; align-items: center;
        justify-content: center; padding: 0 3px; line-height: 1;
        border: 2px solid #fff;
    }
    .bana-header-divider {
        width: 1px; height: 28px; background: #eee; margin: 0 8px;
    }
    .bana-header-user {
        display: flex; align-items: center; gap: 10px;
        cursor: pointer; padding: 6px 10px; border-radius: 10px;
        transition: background .2s; text-decoration: none;
    }
    .bana-header-user:hover { background: #f4f6f9; }
    .bana-header-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        object-fit: cover; border: 2px solid #8DC63F;
    }
    .bana-header-user-info { line-height: 1.2; }
    .bana-header-user-name {
        font-size: 13px; font-weight: 700; color: #1A1A1A;
        display: block; max-width: 120px;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .bana-header-user-role {
        font-size: 11px; color: #8DC63F; font-weight: 600;
    }
    .bana-header-chevron { color: #aaa; font-size: 14px; }
</style>

<div id="headerMain" class="d-none">
    <header id="header" class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
        <div class="navbar-nav-wrap" style="width:100%;display:flex;align-items:center;justify-content:space-between;">

            {{-- Left: sidebar toggle + logo --}}
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker bana-header-icon-btn mr-2">
                    <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                    <i class="tio-last-page navbar-vertical-aside-toggle-full-align"></i>
                </button>
                @php($restaurant_logo=\App\CentralLogics\Helpers::getSettingsDataFromConfig(settings:'logo',relations:['storage']))
                <a class="navbar-brand d-none d-md-flex align-items-center gap-2" href="{{route('admin.dashboard')}}" style="text-decoration:none;">
                    <img class="navbar-brand-logo"
                         src="{{ \App\CentralLogics\Helpers::get_full_url('business',$restaurant_logo?->value,$restaurant_logo?->storage[0]?->value ?? 'public', 'favicon') }}"
                         style="height:36px;width:auto;object-fit:contain;" alt="logo">
                </a>
            </div>

            {{-- Center: search --}}
            <div class="d-none d-md-block">
                <button type="button" class="bana-header-search-btn" data-toggle="modal" data-target="#staticBackdrop">
                    <i class="tio-search"></i>
                    <span>{{ translate('Search anything...') }}</span>
                    <span class="shortcut">Ctrl+K</span>
                </button>
            </div>

            {{-- Right: actions --}}
            <div class="d-flex align-items-center gap-2">

                {{-- Language --}}
                @php($local = session()->has('local') ? session('local') : null)
                @php($lang = \App\CentralLogics\Helpers::get_business_settings('system_language'))
                @if($lang)
                <div class="dropdown d-none d-sm-block">
                    <a class="bana-header-icon-btn" href="#" data-toggle="dropdown" title="{{ translate('Language') }}" style="width:auto;padding:0 10px;gap:5px;font-size:12px;font-weight:600;">
                        <i class="tio-world"></i>
                        @foreach($lang??[] as $data)
                            @if($data['code']==$local)
                                <span>{{strtoupper($data['code'])}}</span>
                            @elseif(!$local && $data['default'] == true)
                                <span>{{strtoupper($data['code'])}}</span>
                            @endif
                        @endforeach
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        @foreach($lang??[] as $data)
                            @if($data['status']==1)
                            <li>
                                <a class="dropdown-item" href="{{route('admin.lang',[$data['code']])}}">
                                    <span class="text-capitalize">{{$data['code']}}</span>
                                </a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Messages --}}
                @php($message=\App\Models\Conversation::whereUserType('admin')->whereHas('last_message', function($q){ $q->whereColumn('conversations.sender_id','messages.sender_id'); })->where('unread_message_count','>',0)->count())
                <a class="bana-header-icon-btn d-none d-sm-flex" href="{{route('admin.message.list', ['tab'=>'customer'])}}" title="{{ translate('Messages') }}">
                    <i class="tio-messages-outlined"></i>
                    @if($message > 0)
                        <span class="bana-header-badge">{{$message > 9 ? '9+' : $message}}</span>
                    @endif
                </a>

                {{-- Pending orders --}}
                @php($count=\App\Models\Order::where('order_status','pending')->count())
                <a class="bana-header-icon-btn d-none d-sm-flex" href="{{route('admin.order.list',['status'=>'pending'])}}" title="{{ translate('Pending Orders') }}">
                    <i class="tio-shopping-cart-outlined"></i>
                    @if($count > 0)
                        <span class="bana-header-badge">{{$count > 9 ? '9+' : $count}}</span>
                    @endif
                </a>

                <div class="bana-header-divider d-none d-sm-block"></div>

                {{-- User dropdown --}}
                <div class="hs-unfold">
                    <a class="js-hs-unfold-invoker bana-header-user" href="javascript:;"
                       data-hs-unfold-options='{"target":"#accountNavbarDropdown","type":"css-animation"}'>
                        <img class="bana-header-avatar"
                             src="{{ auth('admin')?->user()?->image_full_url ?? dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                             alt="avatar"
                             onerror="this.src='{{ dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}'">
                        <div class="bana-header-user-info d-none d-md-block">
                            <span class="bana-header-user-name">{{auth('admin')->user()->f_name}} {{auth('admin')->user()->l_name}}</span>
                            <span class="bana-header-user-role">{{ translate('Admin') }}</span>
                        </div>
                        <i class="tio-chevron-down bana-header-chevron d-none d-md-block"></i>
                    </a>

                    <div id="accountNavbarDropdown"
                         class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account"
                         style="min-width:220px;">
                        <div class="dropdown-item-text py-3 px-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ auth('admin')?->user()?->image_full_url ?? dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                                     style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #8DC63F;" alt="">
                                <div>
                                    <div style="font-size:13px;font-weight:700;color:#1A1A1A;">
                                        {{auth('admin')->user()->f_name}} {{auth('admin')->user()->l_name}}
                                    </div>
                                    <div style="font-size:11px;color:#888;">{{auth('admin')->user()->email}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider my-1"></div>
                        <a class="dropdown-item" href="{{route('admin.settings')}}">
                            <i class="tio-settings mr-2" style="color:#8DC63F;"></i>
                            {{translate('messages.settings')}}
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <a class="dropdown-item text-danger" href="javascript:"
                           onclick="Swal.fire({title:`{{ translate('messages.Do_You_Want_To_Sign_Out_?') }}`,showCancelButton:true,confirmButtonColor:`#8DC63F`,cancelButtonColor:`#e0e0e0`,confirmButtonText:`{{ translate('messages.Yes') }}`,cancelButtonText:`{{ translate('messages.cancel') }}`}).then((r)=>{if(r.value)location.href=`{{route('logout')}}`})">
                            <i class="tio-exit mr-2"></i>
                            {{translate('messages.sign_out')}}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>

<div class="modal fade removeSlideDown" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered max-w-520">
        <div class="modal-content modal-content__search border-0">
            <div class="d-flex flex-column gap-3 rounded-20 bg-card py-2 px-3">
                <div class="d-flex gap-2 align-items-center position-relative">
                    <form class="flex-grow-1" id="searchForm" action="{{ route('admin.search.routing') }}">
                        @csrf
                        <div class="d-flex align-items-center global-search-container">
                            <input class="form-control flex-grow-1 rounded-10 search-input" id="searchInput" name="search" type="search" placeholder="Search" aria-label="Search" autofocus>
                        </div>
                    </form>
                    <div class="position-absolute right-0 pr-2">
                        <button class="border-0 rounded px-2 py-1" type="button" data-dismiss="modal">{{ translate('Esc') }}</button>
                    </div>
                </div>
                <div class="min-h-350">
                    <div class="search-result" id="searchResults">
                        <div class="text-center text-muted py-5">{{translate('It appears that you have not yet searched.')}}.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
