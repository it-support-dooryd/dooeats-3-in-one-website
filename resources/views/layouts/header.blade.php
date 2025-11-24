<meta name="csrf-token" content="{{ csrf_token() }}"/>
<!-- Glassmorphic Floating Navigation Bar -->
<header class="floating-header-wrapper">
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
    
    <!-- Floating Glassmorphic Nav -->
    <nav class="glass-nav">
        <div class="glass-nav-container">
            
            <!-- Left: Logo + Location -->
            <div class="glass-nav-left">
                <a href="{{url('/')}}" class="glass-brand">
                    <img alt="Dooeats" class="glass-logo" src="{{asset('img/logo_web.png')}}" id="logo_web">
                </a>
                
                <!-- Location Button (Big & Shows Current Location) -->
                <div class="glass-location-wrapper big-location-widget">
                    <button class="glass-icon-btn big-location-btn" onclick="getCurrentLocation('reload')" title="Set Location">
                        <i class="feather-map-pin"></i>
                        <span class="glass-btn-label location-text" id="current-location-text">Select Location</span>
                    </button>
                    <input id="user_locationnew" type="text" class="pac-target-input glass-location-input">
                </div>
            </div>
            
            <!-- Center: Navigation Links -->
            <div class="glass-nav-center">
                <a href="{{url('search')}}" class="glass-nav-link">
                    <i class="feather-search"></i>
                    <span class="glass-link-text">Search</span>
                </a>
                <a href="{{url('restaurants')}}" class="glass-nav-link">
                    <i class="feather-grid"></i>
                    <span class="glass-link-text">Restaurants</span>
                </a>
            </div>
            
            <!-- Right: User Actions + Cart -->
            <div class="glass-nav-right">
                
                <!-- Delivery/Takeaway Toggle (Big Switch) -->
                <div class="glass-delivery-toggle big-switch-wrapper">
                    <label class="big-switch">
                        <input type="checkbox" onclick="takeAwayOnOff(this)" <?php if (Session::get('takeawayOption') == "true") { ?> checked <?php } ?>>
                        <span class="big-slider round">
                            <span class="switch-label delivery-label"><i class="feather-truck"></i> Delivery</span>
                            <span class="switch-label takeaway-label"><i class="feather-shopping-bag"></i> Pickup</span>
                        </span>
                    </label>
                </div>
                
                <!-- Cart Button -->
                <a href="{{url('/checkout')}}" class="glass-icon-btn glass-cart-btn">
                    <i class="feather-shopping-cart"></i>
                    <span class="glass-btn-label">Cart</span>
                    <span class="glass-cart-badge" id="cart-count" style="display:none;">0</span>
                </a>
                
                <!-- User Menu -->
                @auth
                <div class="glass-dropdown">
                    <button class="glass-cta-btn" id="userMenuButton">
                        <i class="feather-user"></i>
                        <span>Account</span>
                    </button>
                    <div class="glass-dropdown-menu" id="userDropdownMenu">
                        <a class="glass-dropdown-item" href="{{url('profile')}}">
                            <i class="feather-user"></i> {{trans('lang.my_account')}}
                        </a>
                        <a class="glass-dropdown-item" href="{{url('my-order')}}">
                            <i class="feather-shopping-bag"></i> My Orders
                        </a>
                        <a class="glass-dropdown-item" href="{{ route('terms') }}">
                            <i class="feather-file-text"></i> {{trans('lang.terms_use')}}
                        </a>
                        <a class="glass-dropdown-item" href="{{ route('privacy') }}">
                            <i class="feather-shield"></i> {{trans('lang.privacy_policy')}}
                        </a>
                        <a class="glass-dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="feather-log-out"></i> {{trans('lang.logout')}}
                        </a>
                    </div>
                </div>
                @else
                <a href="{{url('login')}}" class="glass-cta-btn">
                    <i class="feather-user"></i>
                    <span>Sign In</span>
                </a>
                @endauth
                
                <!-- Mobile Menu Toggle -->
                <button class="glass-mobile-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            
        </div>
    </nav>
    <!-- Mobile Menu Modal -->
    <div class="glass-mobile-menu" id="mobileMenuModal">
        <a href="{{url('search')}}" class="glass-mobile-link">
            <i class="feather-search"></i> Search
        </a>
        <a href="{{url('restaurants')}}" class="glass-mobile-link">
            <i class="feather-grid"></i> Restaurants
        </a>
        
        <div class="glass-mobile-divider"></div>
        
        @auth
        <a href="{{url('profile')}}" class="glass-mobile-link">
            <i class="feather-user"></i> My Account
        </a>
        <a href="{{ route('logout') }}" class="glass-mobile-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="feather-log-out"></i> Logout
        </a>
        @else
        <a href="{{url('login')}}" class="glass-mobile-link">
            <i class="feather-log-in"></i> Sign In
        </a>
        <a href="{{url('signup')}}" class="glass-mobile-link">
            <i class="feather-user-plus"></i> Sign Up
        </a>
        @endauth
    </div>
</header>

<!-- Glassmorphic Nav JavaScript -->
<script src="{{ asset('js/glass-nav.js') }}"></script>
