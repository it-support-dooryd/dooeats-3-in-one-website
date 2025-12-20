<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="app_name">Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body class="auth-body">
    <div class="auth-overlay"></div>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 450px;">
            <div class="auth-logo">
                <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats Logo">
            </div>



            <h4 class="text-center mb-1" style="font-weight: 700; color: #102A1C;">Welcome Back</h4>
            <span class="auth-subtitle text-center mb-4" style="color: #666; font-size: 14px;">Sign in to your admin account</span>
            
            @if(count($errors) > 0)
                <div class="alert alert-danger mb-3" style="border-radius: 12px; border: none; background: #FFF1F1; color: #D63031; font-size: 14px;">
                    @foreach( $errors->all() as $message )
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group-auth">
                    <input type="email" id="email" name="email" class="form-control-auth @error('email') is-invalid @enderror" placeholder="Email Address" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <div class="error" style="display:block; color:#D63031; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group-auth mt-3">
                    <div class="password-input-group">
                        <input type="password" id="password" name="password" class="form-control-auth @error('password') is-invalid @enderror" placeholder="Password" required autocomplete="current-password">
                        <div class="password-toggle-icon" onclick="togglePassword()">
                            <i class="fa fa-eye"></i>
                        </div>
                    </div>
                    @error('password')
                        <div class="error" style="display:block; color:#D63031; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-me-group mb-4 mt-2">
                    <label class="custom-switch-auth mb-0">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="slider-auth"></span>
                        <span style="font-size: 13px; color: #666;">{{ __('Remember Me') }}</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password-link">Forgot Password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-auth-primary" id="login-btn">
                    <span>{{ __('Login') }}</span>
                </button>

                <div class="text-center mt-4">
                    <p style="color:#999; font-size: 12px;">
                       For use by authorized admins only. By logging in, you agree to our <a href="#" style="color: #047857;">Terms of Service</a>.
                    </p>
                </div>
            </form>

        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery.cookie.js') }}"></script>
    <script type="text/javascript">
        function togglePassword() {
            var x = document.getElementById("password");
            var icon = document.querySelector(".password-toggle-icon i");
            if (x.type === "password") {
                x.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                x.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        $(document).ready(function () {
             // Cookie logic for title/favicon if needed
        });
    </script>
</body>
</html>
