<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restaurant Panel - Login</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    <link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body class="auth-body">
    <div class="auth-overlay"></div>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats Logo">
            </div>

            <div class="auth-tabs">
                <a href="{{ url('/login') }}" class="auth-tab-link">Customer</a>
                <a href="{{ url('/restaurant/login') }}" class="auth-tab-link active">Restaurant</a>
            </div>

            <h4>Restaurant Login</h4>
            <span class="auth-subtitle">Manage your restaurant with the premium dashboard.</span>
            
            @if(count($errors) > 0)
                <div class="alert-danger">
                    @foreach($errors->all() as $message)
                        <div><i class="fa fa-exclamation-circle"></i> {{ $message }}</div>
                    @endforeach
                </div>
            @endif
            <div id="error_message" class="alert-danger" style="display:none;"></div>

            <form action="{{ route('login') }}" method="POST" id="login-form">
                @csrf
                <div class="form-group-auth">
                    <i class="fa fa-envelope-o input-icon"></i>
                    <input type="email" name="email" id="email" class="form-control-auth" placeholder="Email Address" required autofocus value="{{ old('email') }}">
                </div>

                <div class="form-group-auth">
                    <div class="password-input-group">
                        <i class="fa fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="form-control-auth" placeholder="Password" required>
                        <div class="password-toggle-icon" onclick="togglePassword()">
                            <i class="fa fa-eye" id="password-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="remember-me-group">
                    <label class="custom-switch-auth">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="slider-auth"></span>
                        Remember me
                    </label>
                    <a href="{{ route('forgot-password') }}" class="forgot-password-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-auth-primary" id="btn-login">
                    <span>Sign In</span>
                </button>

                <div class="text-center mt-4">
                    <span style="color:var(--text-secondary); font-size:14px;">Don't have an account? </span>
                    <a href="{{ route('register') }}" class="forgot-password-link">Join Us</a>
                </div>
            </form>

            <div class="or-divider">
                <span>or</span>
            </div>

            <div class="social-login-group">
                <div class="social-btn" onclick="googleAuth()">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20">
                    <span>Sign in with Google</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-auth.js"></script>

    <script type="text/javascript">
        function togglePassword() {
            var x = document.getElementById("password");
            var icon = document.getElementById("password-icon");
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

        $('#login-form').on('submit', function(e) {
            // No verification needed, just submit
            return true;
        });

        // Initialize Firebase for Google Auth
        var firebaseConfig = {
            apiKey: "{{ config('firebase.api_key') }}",
            authDomain: "{{ config('firebase.auth_domain') }}",
            projectId: "{{ config('firebase.project_id') }}",
        };
        firebase.initializeApp(firebaseConfig);

        function googleAuth() {
            var provider = new firebase.auth.GoogleAuthProvider();
            firebase.auth().signInWithPopup(provider).then(async (result) => {
                const user = result.user;
                const database = firebase.firestore();
                
                try {
                    // Check if user exists and is a vendor
                    const doc = await database.collection("users").doc(user.uid).get();
                    if (doc.exists) {
                        const userData = doc.data();
                        if (userData.role === "vendor" || userData.role === "restaurant") {
                             // Login to Laravel
                            const sessionResponse = await $.ajax({
                                type: 'POST',
                                url: "{{ route('setToken') }}",
                                data: { id: user.uid, email: user.email, password: "", isSubscribed: userData.isSubscribed || '' },
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                            });

                            if (sessionResponse.access) {
                                window.location.href = "{{ route('dashboard') }}";
                            } else {
                                alert("Failed to create session.");
                            }
                        } else {
                            firebase.auth().signOut();
                            alert("This account is not registered as a restaurant.");
                        }
                    } else {
                        // Redirect to registration if not found? Or show error.
                        // Ideally, we redirect to register to complete profile (phone etc)
                         window.location.href = "{{ route('register') }}";
                    }
                } catch (error) {
                    console.error(error);
                    alert("Login failed: " + error.message);
                }
            }).catch(function(error) {
                alert(error.message);
            });
        }
    </script>
</body>
</html>