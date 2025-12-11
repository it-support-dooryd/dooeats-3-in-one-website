<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <!-- Main Auth Page Container -->
    <div class="auth-page">
        <div class="auth-container">
            
            <!-- Left Panel: Login Form -->
            <div class="auth-form-panel">
                
                <!-- Tab Switcher: Restaurant / Customer -->
                <div class="auth-tabs show-tabs">
                    <button class="auth-tab active" data-tab="restaurant">
                        Restaurant Login
                    </button>
                    <!-- Adjust this link to point to the customer login page -->
                    <a href="{{ url('/login') }}" class="auth-tab" data-tab="customer">
                        Customer Login
                    </a>
                </div>

                <!-- Header -->
                <div class="auth-header" style="text-align: center;">
                    <h1 class="auth-title">{{trans('Welcome Back')}}</h1>
                    <p class="auth-subtitle">{{trans('lang.sign_in_to_continue')}}</p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf
                    
                    @if(count($errors) > 0)
                        <div class="error-message show" style="margin-bottom: 1rem; text-align: center;">
                            @foreach( $errors->all() as $message )
                                <span>{{ $message }}</span><br>
                            @endforeach
                        </div>
                    @endif

                    <!-- Email Input -->
                    <div class="form-group">
                        <div class="form-input-wrapper">
                            <input type="email" id="email" name="email" class="form-input @error('email') is-invalid @enderror" placeholder="email@domain.com" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        </div>
                        @error('email')
                            <div class="error-message show">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Input with Toggle -->
                    <div class="form-group">
                        <div class="form-input-wrapper">
                            <input type="password" id="password" name="password" class="form-input @error('password') is-invalid @enderror" placeholder="••••••••••••" required autocomplete="current-password">
                            
                            <!-- Show/Hide Password Toggle -->
                            <svg class="password-toggle" id="toggle-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        @error('password')
                            <div class="error-message show">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="remember-me-wrapper">
                        <label class="toggle-label">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>{{ __('Remember Me') }}</span>
                        </label>
                        <!-- Check if route exists, otherwise just # -->
                         @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="auth-link" style="font-size: 0.875rem; color: var(--secondary-color);">Forgot Password?</a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn btn-primary" id="login-btn">
                        <span style="flex: 1; text-align: center;">{{ __('Login') }}</span>
                    </button>

                </form>

                <!-- Footer -->
                <div class="auth-footer">
                    <p class="auth-terms">
                       For use by adults only (18 years of age and older). By logging in, you agree to our <a href="#">Terms of Service</a>.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery.cookie.js') }}"></script>
    <script type="text/javascript">
        // PASSWORD TOGGLE FUNCTIONALITY
        $('#toggle-password').click(function() {
            const passwordInput = $('#password');
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            
            if (type === 'text') {
                $(this).html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />');
            } else {
                $(this).html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />');
            }
        });

        $(document).ready(function () {
             // Cookie logic for title/favicon if needed
             var app_name = "<?php echo @$_COOKIE['meta_title']; ?>";
             if(app_name){
                 document.title = app_name;
             }
        });
    </script>
</body>
</html>
