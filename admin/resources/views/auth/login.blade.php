<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
    <link rel="icon" id="favicon" type="image/x-icon" href="<?php echo str_replace('images/','images%2F',@$_COOKIE['favicon']); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .form-input-container {
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(229, 231, 235, 0.8);
        }
        .form-input-container:focus-within {
             border-color: #F97316;
        }
        .form-input {
            background: transparent;
            border: none;
            outline: none;
        }
        .gradient-circle {
            background: linear-gradient(135deg, #FDBA74, #F97316);
        }
        <?php if(isset($_COOKIE['admin_panel_color'])){ ?>
        .btn-primary-custom {
            background: <?php echo $_COOKIE['admin_panel_color']; ?>;
            border: 1px solid <?php echo $_COOKIE['admin_panel_color']; ?>;
        }
        .btn-primary-custom:hover {
            background: <?php echo $_COOKIE['admin_panel_color']; ?>;
            border-color: <?php echo $_COOKIE['admin_panel_color']; ?>;
            opacity: 0.9;
        }
        <?php } ?>
    </style>
</head>
<body class="bg-gray-100">

    <div class="min-h-screen w-full flex items-center justify-center p-4 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
        
        <div class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Left Side: Login Form & Welcome -->
            <div class="flex flex-col gap-8">
                <!-- Login Panel -->
                <div class="glass-effect p-8 rounded-3xl shadow-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-sm font-medium text-gray-800">DooEats</span>
                        <a href="#" class="text-sm font-semibold text-gray-900 hover:text-orange-600 transition-colors">Sign up</a>
                    </div>

                    <h1 class="text-4xl font-bold text-gray-900 mb-6">Log in</h1>
                    
                    <button class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-full border border-gray-400 bg-white/50 hover:bg-white/70 transition-colors mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                           <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM5.155 6.36C5.06 6.74 5 7.14 5 7.56c0 2.44 1.97 4.41 4.41 4.41 1.03 0 1.98-.35 2.74-.95-.2.27-.43.52-.68.75a4.5 4.5 0 01-6.36-6.36c.23-.25.48-.47.75-.68-.6.76-.95 1.71-.95 2.74zM14.845 13.64c.09-.38.15-.78.15-1.19 0-2.44-1.97-4.41-4.41-4.41-1.03 0-1.98.35-2.74.95.2-.27.43-.52.68-.75a4.5 4.5 0 016.36 6.36c-.23.25-.48.47-.75.68.6-.76.95-1.71.95-2.74z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-gray-800">Continue with Google</span>
                    </button>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        @if(count($errors) > 0)
                            @foreach( $errors->all() as $message )
                                <div class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    <span>{{ $message }}</span>
                                </div>
                            @endforeach
                        @endif
                        <div class="space-y-6">
                            <!-- Email Input -->
                            <div class="form-input-container flex items-center w-full rounded-full p-1 pl-4 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M14.243 5.757a6 6 0 10-8.486 8.486 6 6 0 008.486-8.486zM12.828 7.172a4 4 0 10-5.656 5.656 4 4 0 005.656-5.656z" clip-rule="evenodd" />
                                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.93 4.93a10 10 0 1110.14 10.14A10 10 0 014.93 4.93z" clip-rule="evenodd" />
                                </svg>
                                <input type="email" name="email" id="email" class="form-input w-full p-2 text-gray-800 placeholder-gray-500 @error('email') is-invalid @enderror" placeholder="{{ __('Email Address') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <!-- Password Input -->
                            <div class="flex items-center gap-2">
                                <div class="form-input-container flex items-center w-full rounded-full p-1 pl-4 transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                      <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v2H4a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-2V6a4 4 0 00-4-4zm2 6V6a2 2 0 10-4 0v2h4z" clip-rule="evenodd" />
                                    </svg>
                                    <input type="password" name="password" id="password" class="form-input w-full p-2 text-gray-800 placeholder-gray-500 @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}" required autocomplete="current-password">
                                    <a href="#" class="text-xs font-semibold text-gray-600 hover:text-orange-600 whitespace-nowrap px-4">Forgot?</a>
                                </div>
                                <button type="submit" class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-gray-900 hover:bg-gray-800 rounded-full text-white transition-colors btn-primary-custom">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <div class="flex items-center">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="text-sm text-gray-700 ml-2" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                    </form>
                    
                    <p class="text-xs text-gray-700 mt-8">For use by adults only (18 years of age and older). By logging in, you agree to our Terms of Service. Please consume responsibly!</p>
                </div>
                <!-- New In Panel -->
                 <div class="bg-gray-900 p-6 rounded-3xl flex items-center justify-between shadow-lg">
                    <div>
                        <h2 class="text-white font-bold text-xl">New in</h2>
                        <p class="text-gray-300 text-sm">Delicious Weekly Specials</p>
                    </div>
                    <a href="#" class="text-white font-semibold text-sm hover:text-orange-300 transition-colors">Discover</a>
                </div>
            </div>

            <!-- Right Side: Promo Card -->
            <div class="bg-white/95 p-8 rounded-3xl shadow-2xl flex flex-col">
                <div class="flex justify-between items-start mb-4">
                     <div>
                        <p class="font-semibold text-gray-500 text-sm">Special Offer</p>
                        <p class="font-medium text-gray-800">Free Delivery Friday</p>
                    </div>
                </div>

                <div class="flex-grow flex relative items-center">
                    <div class="w-1/3 text-gray-900">
                        <p class="text-6xl font-bold">Fri</p>
                        <p class="text-7xl font-extrabold -mt-2">26th</p>
                    </div>
                    
                    <div class="absolute left-1/4 top-1/2 -translate-y-1/2 w-48 h-48 rounded-full gradient-circle opacity-80"></div>

                    <div class="w-2/3 pl-16 z-10">
                        <p class="font-semibold text-lg text-gray-800">12 PM - 10 PM</p>
                        <p class="text-gray-600">On all orders over $20</p>
                        <p class="text-gray-600">Lagos, Nigeria</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mt-auto pt-6">
                    <div class="flex items-center gap-2">
                         <div class="w-8 h-8 rounded-full gradient-circle flex items-center justify-center text-white font-bold text-sm">DE</div>
                        <span class="font-bold text-gray-800">DooEats</span>
                    </div>
                    <a href="#" class="flex items-center justify-center gap-2 py-3 px-5 rounded-full bg-gray-900 hover:bg-gray-800 text-white transition-colors">
                        <span class="text-sm font-medium">Order Now</span>
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-database.js"></script>
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript">
    function copyToClipboard(text) {
        const elem = document.createElement('textarea');
        elem.value = text;
        document.body.appendChild(elem);
        elem.select();
        document.execCommand('copy');
        document.body.removeChild(elem);
    }
    var database = firebase.firestore();
    var ref = database.collection('settings').doc("globalSettings");
    $(document).ready(function () {
        ref.get().then(async function (snapshots) {
            var globalSettings = snapshots.data();
            setCookie('application_name', globalSettings.applicationName, 365);
            setCookie('meta_title', globalSettings.meta_title, 365);
            setCookie('favicon', globalSettings.favicon, 365);
            admin_panel_color = globalSettings.admin_panel_color;
            setCookie('admin_panel_color', admin_panel_color, 365);
            // The original login-register class is no longer used for background color
            // $('.login-register').css({'background-color': admin_panel_color}); 
            document.title = globalSettings.meta_title;
            var favicon = '<?php echo @$_COOKIE['favicon'] ?>';
        })
    });
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
</script>
</body>
</html>
