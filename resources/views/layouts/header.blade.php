<meta name="csrf-token" content="{{ csrf_token() }}"/>
<!-- Minimalistic Header -->
<header class="minimalist-header">
    <?php
    if (Session::get('takeawayOption') == 'true' || Session::get('takeawayOption') == true) {
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
                        return false;
                    }
                } else {
                    let isExecuted = confirm("If you select take away option then it will empty cart. are you sure want to do ?");
                    if (isExecuted) {
                    } else {
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
    
    <div class="header-container">
        <!-- Left: Logo + Location -->
        <div class="header-left">
            <a href="{{url('/')}}" class="header-logo">
                <img alt="Dooeats" src="{{asset('img/logo_web.png')}}" id="logo_web">
            </a>
            
            <div class="header-location">
                <i class="feather-map-pin"></i>
                <input id="user_locationnew" type="text" class="location-input" placeholder="Calabar, Cross River, Nigeria" value="<?php echo @$_COOKIE['address_name'] ?? 'Calabar, Cross River, Nigeria'; ?>" readonly>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="header-right">
            <!-- Delivery/Pickup Toggle -->
            <div class="delivery-toggle-wrapper">
                <label class="delivery-switch">
                    <input type="checkbox" onclick="takeAwayOnOff(this)" <?php if (Session::get('takeawayOption') == "true") { ?> checked <?php } ?>>
                    <span class="switch-slider">
                        <span class="switch-label delivery-label">Delivery</span>
                        <span class="switch-label pickup-label">Pickup</span>
                    </span>
                </label>
            </div>

            <!-- Search Button -->
            <button class="header-btn search-btn" onclick="window.location.href='{{url('search')}}'">
                <i class="feather-search"></i>
            </button>

            <!-- Cart Button -->
            <a href="{{url('/checkout')}}" class="header-btn cart-btn">
                <i class="feather-shopping-cart"></i>
                <span class="cart-badge" id="cart-count" style="display:none;">0</span>
            </a>

            <!-- User Menu -->
            @auth
            <div class="user-dropdown">
                <button class="header-btn user-btn" id="userMenuButton">
                    <i class="feather-user"></i>
                </button>
                <div class="dropdown-menu-custom" id="userDropdownMenu">
                    <a class="dropdown-item-custom" href="{{url('profile')}}">
                        <i class="feather-user"></i> {{trans('lang.my_account')}}
                    </a>
                    <a class="dropdown-item-custom" href="{{url('my-order')}}">
                        <i class="feather-shopping-bag"></i> My Orders
                    </a>
                    <a class="dropdown-item-custom" href="{{ route('terms') }}">
                        <i class="feather-file-text"></i> {{trans('lang.terms_use')}}
                    </a>
                    <a class="dropdown-item-custom" href="{{ route('privacy') }}">
                        <i class="feather-shield"></i> {{trans('lang.privacy_policy')}}
                    </a>
                    <a class="dropdown-item-custom" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="feather-log-out"></i> {{trans('lang.logout')}}
                    </a>
                </div>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            @else
            <a href="{{url('login')}}" class="header-btn login-btn">
                <i class="feather-user"></i>
                <span class="login-text">Sign In</span>
            </a>
            @endauth
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenuModal">
        <div class="mobile-delivery-toggle">
            <label class="delivery-switch">
                <input type="checkbox" onclick="takeAwayOnOff(this)" <?php if (Session::get('takeawayOption') == "true") { ?> checked <?php } ?>>
                <span class="switch-slider">
                    <span class="switch-label delivery-label">Delivery</span>
                    <span class="switch-label pickup-label">Pickup</span>
                </span>
            </label>
        </div>

        <a href="{{url('search')}}" class="mobile-menu-link">
            <i class="feather-search"></i> Search
        </a>
        <a href="{{url('restaurants')}}" class="mobile-menu-link">
            <i class="feather-grid"></i> Restaurants
        </a>
        
        <div class="mobile-menu-divider"></div>
        
        @auth
        <a href="{{url('profile')}}" class="mobile-menu-link">
            <i class="feather-user"></i> My Account
        </a>
        <a href="{{ route('logout') }}" class="mobile-menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="feather-log-out"></i> Logout
        </a>
        @else
        <a href="{{url('login')}}" class="mobile-menu-link">
            <i class="feather-log-in"></i> Sign In
        </a>
        <a href="{{url('signup')}}" class="mobile-menu-link">
            <i class="feather-user-plus"></i> Sign Up
        </a>
        @endauth
    </div>
</header>

<!-- Header JavaScript -->
<script src="{{ asset('js/glass-nav.js') }}"></script>
