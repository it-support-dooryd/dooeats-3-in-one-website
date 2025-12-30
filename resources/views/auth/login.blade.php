<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Dooeats') }} - Login</title>
    <link rel="icon" type="image/x-xicon" href="{{ asset('images/logo-light-icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/login-styles.css') }}" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats Logo">
            </div>

            <!-- Tab Bar -->
            <div class="login-tabs">
                <a href="{{ route('login') }}" class="login-tab-link active">Customer</a>
                <a href="http://127.0.0.1:8002/login" class="login-tab-link">Restaurant</a>
            </div>

            <h4 class="text-center mb-4" style="font-weight: 600; color: #333;">{{trans('lang.sign_in')}}</h4>
            
            <div class="error" style="color: red; text-align: center;" id="field_error"></div>
            <div class="error" id="field_error1" style="color:red; display:none; text-align: center;"></div>

            <form class="login-form" action="javascript:void(0)" onsubmit="return loginClick()">
                
                <div id="login-fields">
                    <div class="form-group-login" id="email_div">
                        <input type="email" class="form-control-login" id="email" placeholder="Enter Email Address" autocomplete="email" required>
                        <input type="hidden" id="hidden_email" />
                    </div>

                    <div class="form-group-login" id="pass_div">
                        <div class="password-input-group">
                            <input type="password" class="form-control-login" id="password" placeholder="Enter Password" minlength="8" required autocomplete="current-password">
                            <div class="password-toggle-icon" onclick="togglePassword()">
                                <i class="fa fa-eye"></i>
                            </div>
                        </div>
                    </div>

                    <div class="remember-me-group">
                        <label class="custom-switch-login">
                            <input type="checkbox" id="remember_me">
                            <span class="slider-login"></span>
                            Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-password-link">Forgot Password?</a>
                    </div>
                </div>

                <div id="recaptcha-container" style="display:none;"></div>

                <button type="submit" class="btn-login-primary btn-login" id="btn-login">{{trans('lang.sign_in')}}</button>
                
                <div class="or-divider">
                    <span>OR</span>
                </div>

                <div class="social-login-group">
                    <div class="social-btn google-btn" onclick="loginWithGoogle()" title="Sign in with Google">
                        <img src="{{ asset('images/google-icon.png') }}" alt="Google Icon" class="social-icon">
                        <span class="social-text">Sign in with Google</span>
                    </div>
                </div>

                 <div class="text-center mt-3">
                    <span style="color:#666; font-size:14px;">{{trans('lang.dont_have_account')}} </span>
                    <a href="{{url('signup')}}" class="forgot-password-link">{{trans('lang.sign_up')}}</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
        
        var firebaseInitialized = false;
        var database;
        try {
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }
            database = firebase.firestore();
            firebaseInitialized = true;
        } catch (error) {
            console.error("Firebase init error:", error);
             jQuery(document).ready(function() {
                 $("#field_error").html("System error: Failed to connect to services. Please try again later.").show();
                 $(".btn-login").prop('disabled', true);
             });
        }

        function togglePassword() {
            var x = document.getElementById("password");
            var iconContainer = document.querySelector(".password-toggle-icon");
            if (x.type === "password") {
                x.type = "text";
                iconContainer.classList.add("active");
            } else {
                x.type = "password";
                iconContainer.classList.remove("active");
            }
        }

        async function loginClick() {
            $(".btn-login").text('Please wait...');
            var email = $("#email").val();
            var password = $("#password").val();

            try {
                await new Promise((resolve, reject) => {
                    grecaptcha.enterprise.ready(async () => {
                        try {
                            const token = await grecaptcha.enterprise.execute('6LcKSzosAAAAADS4s80I4QKaDK0ub7tkwRuwSrLd', {action: 'LOGIN'});
                            
                            $.ajax({
                                type: 'POST',
                                url: "{{ route('verify-recaptcha') }}",
                                data: { token: token, action: 'LOGIN' },
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                success: function(data) {
                                    if (data.success) {
                                        resolve(true); 
                                    } else {
                                        console.error('reCAPTCHA Failed:', data);
                                        reject('Security verification failed. Please try again.');
                                    }
                                },
                                error: function(err) {
                                    console.error('reCAPTCHA Error:', err);
                                    reject('Unable to verify security token.');
                                }
                            });
                        } catch(e) {
                            reject(e);
                        }
                    });
                });
            } catch (err) {
                $("#field_error").html(err).show();
                $(".btn-login").text("{{trans('lang.sign_in')}}");
                return false;
            }
            
            firebase.auth().signInWithEmailAndPassword(email, password)
                .then((userCredential) => {
                    var uuid = userCredential.user.uid;
                    
                    database.collection("users").doc(uuid).get().then((doc) => {
                        if (doc.exists) {
                            var userData = doc.data();
                            if (userData.role === "customer") {
                                var url = "{{route('newLogin')}}";
                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                        userId: uuid,
                                        email: email,
                                        password: password
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function (data) {
                                        if (data.access) {
                                            window.location = "{{url('/')}}";
                                        }
                                    },
                                    error: function() {
                                        $("#field_error").html("Login failed. Please try again.").show();
                                        $(".btn-login").text("{{trans('lang.sign_in')}}");
                                    }
                                });
                            } else {
                                $("#field_error").html("This account is not a customer account.").show();
                                $(".btn-login").text("{{trans('lang.sign_in')}}");
                                firebase.auth().signOut();
                            }
                        } else {
                            $("#field_error").html("User not found.").show();
                            $(".btn-login").text("{{trans('lang.sign_in')}}");
                        }
                    });
                })
                .catch((error) => {
                    var errorMessage = error.message;
                    $("#field_error").html(errorMessage).show();
                    window.scrollTo(0, 0);
                    $(".btn-login").text("{{trans('lang.sign_in')}}");
                });
            return false;
        }

        function loginWithGoogle() {
            var provider = new firebase.auth.GoogleAuthProvider();
            firebase.auth().signInWithPopup(provider)
                .then((result) => {
                    var user = result.user;
                    database.collection("users").doc(user.uid).get().then((doc) => {
                        if (doc.exists) {
                            var userData = doc.data();
                            if (userData.role === "customer") {
                                var url = "{{route('newLogin')}}";
                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                        userId: user.uid,
                                        email: user.email,
                                        password: ""
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function (data) {
                                        if (data.access) {
                                            window.location = "{{url('/')}}";
                                        }
                                    },
                                    error: function() {
                                        $("#field_error").html("Login failed. Please try again.").show();
                                    }
                                });
                            } else {
                                $("#field_error").html("This account is not a customer account.").show();
                                firebase.auth().signOut();
                            }
                        } else {
                            $("#field_error").html("User not found.").show();
                        }
                    });
                })
                .catch((error) => {
                    var errorMessage = error.message;
                    $("#field_error").html(errorMessage).show();
                    window.scrollTo(0, 0);
                });
        }
    </script>
</body>
</html>