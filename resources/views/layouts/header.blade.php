<meta name="csrf-token" content="{{ csrf_token() }}"/>
<header class="main-header">
    <?php
    if (\Session::get('takeawayOption') == 'true' || \Session::get('takeawayOption') == true) {
        $takeaway_options = true;
    } else {
        $takeaway_options = false;
    }
    ?>
    <script>
        <?php if($takeaway_options){ ?>
        var takeaway_options = true;
        <?php }else{ ?>
        var takeaway_options = false;
        <?php } ?>
        function takeAwayOnOff(takeAway) {
            var check_val;
            if (takeaway_options == true) {
                if (takeAway.checked == false) {
                    let isExecuted = confirm("If you select take away option then it will empty cart. are you sure want to do ?");
                    if (isExecuted) {
                    } else {
                        takeAway.checked = true;
                        return false;
                    }
                } else {
                    let isExecuted = confirm("If you select take away option then it will empty cart. are you sure want to do ?");
                    if (isExecuted) {
                    } else {
                        takeAway.checked = false;
                        return false;
                    }
                }
            }
            if (takeAway.checked == true) {
                check_val = true;
                takeaway_options = true;
            } else {
                check_val = false;
                takeaway_options = false;
            }
            $.ajax({
                type: 'POST',
                url: 'takeaway',
                data: {
                    takeawayOption: check_val,
                    "_token": "{{ csrf_token() }}",
                },
                success: function (result) {
                    result = $.parseJSON(result);
                    location.reload();
                }
            });
        }
    </script>

    <style>
        :root {
            --header-height: 80px;
            --mobile-header-height: 64px;
        }

        .main-header {
            background: var(--main-bg);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }

        .header-content {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
        }

        .header-logo img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .header-location-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem;
            background: var(--secondary-bg);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            max-width: 300px;
            border: 1px solid var(--card-border);
        }

        .header-location-btn:hover {
            background: var(--green-tint-bg);
            border-color: var(--brand-green);
        }

        .location-icon {
            width: 36px;
            height: 36px;
            background: var(--deep-green);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .location-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .location-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted-text);
            line-height: 1.2;
        }

        .location-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-icon-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            color: var(--secondary-text);
            padding: 0.5rem;
            border-radius: 12px;
            transition: all 0.2s ease;
            min-width: 64px;
        }

        .nav-icon-link:hover {
            color: var(--deep-green);
            background: var(--green-tint-bg);
        }

        .nav-icon-link i {
            font-size: 1.25rem;
        }

        .nav-icon-link span {
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Takeaway Toggle */
        .header-toggle-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--secondary-bg);
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            border: 1px solid var(--card-border);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--deep-green);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(20px);
        }

        .toggle-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary-text);
        }

        /* Mobile Styles */
        .mobile-header {
            display: none;
            height: var(--mobile-header-height);
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background: var(--main-bg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .mobile-left, .mobile-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .mobile-logo img {
            height: 32px;
        }

        .mobile-menu-toggle {
            font-size: 1.5rem;
            color: var(--deep-green);
            padding: 0.5rem;
        }

        .cart-badge-container {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent-red);
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        @media (max-width: 991px) {
            .main-header { display: none; }
            .mobile-header { display: flex; }
        }

        /* Dropdown custom styling */
        .account-dropdown .dropdown-menu {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 0.75rem;
            margin-top: 10px;
            min-width: 220px;
        }

        .account-dropdown .dropdown-item {
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-weight: 500;
            color: var(--secondary-text);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .account-dropdown .dropdown-item:hover {
            background: var(--green-tint-bg);
            color: var(--deep-green);
        }

        .account-dropdown .dropdown-item i {
            font-size: 1.1rem;
            width: 20px;
        }
    </style>

    <!-- Desktop Header -->
    <div class="header-content d-none d-lg-flex">
        <div class="header-left">
            <a href="{{url('/')}}" class="header-logo">
                <img alt="Logo" src="{{asset('img/logo_web (1).png')}}" onerror="this.onerror=null; this.src='{{asset('img/logo_web.png')}}';">
            </a>

            <div class="header-location-btn" data-toggle="modal" data-target="#headerLocationModal">
                <div class="location-icon">
                    <i class="feather-map-pin"></i>
                </div>
                <div class="location-info">
                    <span class="location-label">{{ trans('lang.location') }}</span>
                    <span class="location-value" id="headerLocationDisplay">
                        @if(!empty(request()->cookie('address_name')))
                            {{ request()->cookie('address_name') }}
                        @else
                            {{ trans('lang.select_location') }}
                        @endif
                    </span>
                </div>
                <i class="feather-chevron-down ml-auto text-muted" style="font-size: 0.8rem;"></i>
            </div>
            <input type="hidden" id="user_locationnew" class="pac-target-input">
        </div>

        <div class="header-right">
            <a href="{{url('search')}}" class="nav-icon-link">
                <i class="feather-search"></i>
                <span>{{trans('lang.search')}}</span>
            </a>

            <a href="{{url('offers')}}" class="nav-icon-link">
                <i class="feather-percent"></i>
                <span>{{trans('lang.offers')}}</span>
            </a>

            <!-- Takeaway Toggle -->
            <div class="header-toggle-container mx-2">
                <span class="toggle-label mr-1">
                    @if(\Session::get('takeawayOption') == "true")
                        {{trans('lang.take_away')}}
                    @else
                        {{trans('lang.delivery')}}
                    @endif
                </span>
                <label class="toggle-switch mb-0">
                    <input type="checkbox" onclick="takeAwayOnOff(this)" @if(\Session::get('takeawayOption') == "true") checked @endif>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            @auth
                <div class="dropdown account-dropdown">
                    <a href="#" class="nav-icon-link dropdown-toggle" id="accountDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="feather-user"></i>
                        <span>{{trans('lang.my_account')}}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="accountDropdown">
                        <a class="dropdown-item" href="{{url('profile')}}">
                            <i class="feather-settings"></i> {{trans('lang.my_account')}}
                        </a>
                        <a class="dropdown-item" href="{{url('restaurants')}}">
                            <i class="feather-list"></i> {{trans('lang.all_restaurants')}}
                        </a>
                        <a class="dropdown-item dine_in_menu" style="display: none;" href="{{url('restaurants')}}?dinein=1">
                            <i class="feather-coffee"></i> {{trans('lang.dine_in_restaurants')}}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('faq') }}">
                            <i class="feather-help-circle"></i> {{trans('lang.delivery_support')}}
                        </a>
                        <a class="dropdown-item" href="{{url('contact-us')}}">
                            <i class="feather-phone"></i> {{trans('lang.contact_us')}}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="feather-log-out"></i> {{trans('lang.logout')}}
                        </a>
                    </div>
                </div>
            @else
                <a href="{{url('login')}}" class="nav-icon-link">
                    <i class="feather-user"></i>
                    <span>{{trans('lang.signin')}}</span>
                </a>
            @endauth

            <a href="{{url('/checkout')}}" class="nav-icon-link cart-badge-container">
                <i class="feather-shopping-cart"></i>
                <span>{{trans('lang.cart')}}</span>
                <!-- Cart count can be added here if available in session/js -->
            </a>

            <a class="toggle ml-2 mobile-menu-toggle hc-nav-trigger hc-nav-1" href="#" role="button" aria-controls="hc-nav-1">
                <span></span>
            </a>
        </div>
    </div>

    <!-- Mobile Header -->
    <div class="mobile-header d-flex d-lg-none">
        <div class="mobile-left">
            <a class="toggle mobile-menu-toggle hc-nav-trigger hc-nav-1" href="#" role="button" aria-controls="hc-nav-1">
                <i class="feather-menu"></i>
            </a>
            <a href="{{url('/')}}" class="mobile-logo">
                <img alt="Logo" src="{{asset('img/logo_web (1).png')}}" onerror="this.onerror=null; this.src='{{asset('img/logo_web.png')}}';">
            </a>
        </div>

        <div class="mobile-right">
            <a href="{{url('search')}}" class="text-dark p-2">
                <i class="feather-search h5 mb-0"></i>
            </a>
            <a href="{{url('/checkout')}}" class="text-dark p-2 cart-badge-container">
                <i class="feather-shopping-cart h5 mb-0"></i>
            </a>
            @auth
                <a href="{{url('profile')}}" class="text-dark p-2">
                    <i class="feather-user h5 mb-0"></i>
                </a>
            @else
                <a href="{{url('login')}}" class="text-dark p-2">
                    <i class="feather-user h5 mb-0"></i>
                </a>
            @endauth
        </div>
    </div>
</header>
