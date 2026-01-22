<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Dooeats') }} - Premium Login</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    <link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body class="auth-body">
    <div class="auth-overlay"></div>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo Section -->
            <div class="auth-logo">
                <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats Logo">
            </div>

            <!-- Tab Bar -->
            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab-link active">Customer</a>
                <a href="{{ url('/restaurant/login') }}" class="auth-tab-link">Restaurant</a>
            </div>

            <h4>Welcome Back</h4>
            <span class="auth-subtitle">Sign in to your account</span>
            
            <div id="error_message" class="alert-danger" style="display:none;"></div>

            <form id="login-form" autocomplete="off">
                <div class="form-group-auth">
                    <i class="fa fa-envelope-o input-icon"></i>
                    <input type="email" id="email" class="form-control-auth" placeholder="Email Address" required autofocus>
                </div>

                <div class="form-group-auth">
                    <div class="password-input-group">
                        <i class="fa fa-lock input-icon"></i>
                        <input type="password" id="password" class="form-control-auth" placeholder="Password" required>
                        <div class="password-toggle-icon" onclick="togglePassword()">
                            <i class="fa fa-eye" id="password-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="remember-me-group">
                    <label class="custom-switch-auth">
                        <input type="checkbox" id="remember_me">
                        <span class="slider-auth"></span>
                        Remember me
                    </label>
                    <a href="{{ route('forgot-password') }}" class="forgot-password-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-auth-primary" id="btn-login">
                    <span>Login</span>
                    <div class="arrow-box">
                        <i class="fa fa-arrow-right"></i>
                    </div>
                </button>

                <div class="or-divider">
                    <span>or</span>
                </div>

                <div class="social-login-group">
                    <div class="social-btn" onclick="loginWithGoogle()">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20">
                        <span>Sign in with Google</span>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <span style="color:var(--text-secondary); font-size:14px;">Didn't have an account? </span>
                    <a href="{{ url('signup') }}" class="forgot-password-link">Sign up</a>
                </div>
            </form>
        </div>
    </div>


    <!-- Firebase Scripts -->
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-firestore.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-auth.js"></script>

    <script type="text/javascript">
        var firebaseConfig = {
            apiKey: "{{ config('firebase.api_key') }}",
            authDomain: "{{ config('firebase.auth_domain') }}",
            databaseURL: "{{ config('firebase.database_url') }}",
            projectId: "{{ config('firebase.project_id') }}",
            storageBucket: "{{ str_replace('gs://', '', config('firebase.storage_bucket')) }}",
            messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
            appId: "{{ config('firebase.app_id') }}",
            measurementId: "{{ config('firebase.measurement_id') }}"
        };
        
        firebase.initializeApp(firebaseConfig);
        const database = firebase.firestore();

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

        $('#login-form').on('submit', async function(e) {
            e.preventDefault();
            const email = $("#email").val();
            const password = $("#password").val();
            const $btn = $('#btn-login');
            const originalText = $btn.find('span').text();

            $btn.prop('disabled', true).find('span').text('Authenticating...');
            $('#error_message').hide();

            try {
                // Firebase Auth
                const userCredential = await firebase.auth().signInWithEmailAndPassword(email, password);
                const uuid = userCredential.user.uid;

                // Check role
                const doc = await database.collection("users").doc(uuid).get();
                if (doc.exists && doc.data().role === "customer") {
                    // Create Session
                    const sessionResponse = await $.ajax({
                        type: 'POST',
                        url: "{{ route('setToken') }}",
                        data: { userId: uuid, email: email, password: password },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    });

                    if (sessionResponse.access) {
                        window.location.href = "{{ url('/') }}";
                    } else {
                        throw new Error("Session creation failed.");
                    }
                } else {
                    firebase.auth().signOut();
                    throw new Error("This account is not a customer account.");
                }

            } catch (error) {
                $('#error_message').text(error.message).show();
                $btn.prop('disabled', false).find('span').text(originalText);
            }
        });

        function loginWithGoogle() {
            var provider = new firebase.auth.GoogleAuthProvider();
            firebase.auth().signInWithPopup(provider).then(async (result) => {
                const user = result.user;
                const doc = await database.collection("users").doc(user.uid).get();
                
                if (doc.exists) {
                    if (doc.data().role === "customer") {
                        const sessionResponse = await $.ajax({
                            type: 'POST',
                            url: "{{ route('setToken') }}",
                            data: { userId: user.uid, email: user.email, password: "" },
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                        });
                        if (sessionResponse.access) {
                            window.location.href = "{{ url('/') }}";
                        }
                    } else {
                        firebase.auth().signOut();
                        alert("This Google account is associated with a different role. Please use a customer account.");
                    }
                } else {
                    // New User - Redirect to social signup to complete profile
                    const nameParts = (user.displayName || "User").split(" ");
                    const firstName = nameParts[0] || "";
                    const lastName = nameParts.slice(1).join(" ") || "";
                    
                    const params = new URLSearchParams({
                        uuid: user.uid,
                        email: user.email || "",
                        firstName: firstName,
                        lastName: lastName,
                        photoURL: user.photoURL || ""
                    });
                    
                    window.location.href = "{{ url('socialsignup') }}?" + params.toString();
                }
            }).catch(error => {
                console.error("Google Auth Error:", error);
                alert(error.message);
            });
        }
    </script>
</body>
</html>