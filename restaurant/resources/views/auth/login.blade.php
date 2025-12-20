<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dooeatery</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
</head>
<body class="auth-body">
    <?php
        $filepath = public_path('countriesdata.json');
        if (file_exists($filepath)) {
            $countries = file_get_contents($filepath);
            $countries = json_decode($countries);
            $countries = (array) $countries;
            $newcountries = array();
            $newcountriesjs = array();
            foreach ($countries as $keycountry => $valuecountry) {
                $newcountries[$valuecountry->phoneCode] = $valuecountry;
                $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
            }
        } else {
            $newcountries = array();
            $newcountriesjs = array();
        }
    ?>
    <div class="auth-overlay"></div>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 450px;">
            <div class="auth-logo">
                <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats Logo">
            </div>

            <!-- Tab Bar -->
            <div class="auth-tabs">
                <a href="http://127.0.0.1:8000/login" class="auth-tab-link">{{trans('lang.customer_login')}}</a>
                <a href="{{ route('login') }}" class="auth-tab-link active">{{trans('lang.restaurant_login')}}</a>
            </div>

            <h4 class="text-center mb-1" style="font-weight: 700; color: #102A1C;">{{trans('lang.welcome_back')}}</h4>
            <span class="auth-subtitle text-center mb-4" style="color: #666; font-size: 14px;">Please login to your account</span>
            
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
            <div class="error mb-3" id="error" style="display:none; color:#D63031; text-align:center; font-size: 14px; background: #FFF1F1; padding: 10px; border-radius: 12px;"></div>

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="post" id="login-form">
                @csrf
                <div class="form-group-auth">
                    <input type="email" class="form-control-auth" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{trans('lang.user_email')}}">
                </div>

                <div class="form-group-auth mt-3">
                    <div class="password-input-group">
                        <input type="password" class="form-control-auth" id="password" name="password" required autocomplete="current-password" placeholder="{{trans('lang.password')}}">
                        <div class="password-toggle-icon" onclick="togglePassword()">
                            <i class="fa fa-eye"></i>
                        </div>
                    </div>
                </div>

                <div class="remember-me-group mb-4 mt-2">
                    <label class="custom-switch-auth mb-0">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="slider-auth"></span>
                        <span style="font-size: 13px; color: #666;">{{trans('lang.remember_me')}}</span>
                    </label>
                    <a href="{{ route('forgot-password') }}" class="forgot-password-link">{{trans('lang.forgot_password')}}?</a>
                </div>

                <button type="submit" class="btn-auth-primary" id="login_btn">
                    <span>{{trans('lang.log_in')}}</span>
                </button>

                <div class="text-center mt-4 mb-4">
                    <span style="color:#888; font-size: 14px;">{{trans('lang.dont_have_account')}} </span>
                    <a href="{{ route('register') }}" class="forgot-password-link">{{trans('lang.sign_up')}}</a>
                </div>

                <div class="or-divider d-flex align-items-center justify-content-center mb-3">
                    <span style="background: white; padding: 0 10px; color: #999; font-size: 12px;">OR CONTINUE WITH</span>
                </div>

                <div class="social-login-group">
                    <div class="social-btn" onclick="googleAuth()" id="google-login-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="22px" height="22px">
                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                            <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                            <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                            <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                        </svg>
                        <span>Google</span>
                    </div>

                    <div class="social-btn" onclick="loginWithPhoneClick()">
                        <i class="fa fa-phone" style="color: #102A1C;"></i> 
                        <span>{{trans('lang.login_with_phone')}}</span>
                    </div>
                </div>
            </form>

            <!-- Phone Login Form (Hidden) -->
            <form id="otp-login-form" action="#" style="display:none; text-align: left;">
                @csrf
                <div class="text-center mb-4">
                    <h5 style="font-weight:700; color: #102A1C;">{{trans('lang.login_with_phone')}}</h5>
                    <p style="color: #666; font-size: 14px;">We'll send you an OTP code</p>
                </div>
                
                <div class="form-group-auth" id="phone-box">
                    <div class="d-flex gap-2">
                         <select name="country" id="country_selector" class="form-control-auth" style="width: 120px; padding-left: 10px; flex-shrink: 0; margin-right: 5px;">
                            <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                <option value="<?php echo $keycy; ?>">+<?php echo $valuecy->phoneCode; ?></option>
                            <?php } ?>
                        </select>
                        <input placeholder="{{trans('lang.user_phone')}}" id="phone" type="number" class="form-control-auth" style="padding-left: 20px;" name="phone" required>
                    </div>
                    <div class="error mt-2" id="phone-error" style="display:none; color:#D63031; font-size: 13px;"></div>
                </div>

                <div class="form-group-auth mt-3" id="otp-box" style="display:none;">
                    <input class="form-control-auth" placeholder="{{trans('lang.otp')}}" id="verificationcode" type="text" name="otp" style="padding-left: 20px;">
                    <div class="error mt-2" id="otp-error" style="display:none; color:#D63031; font-size: 13px;"></div>
                </div>

                <div id="recaptcha-container" style="display:none;" class="mt-3"></div>

                <button type="button" style="display:none;" onclick="applicationVerifier()" id="verify_btn" class="btn-auth-primary mt-4">
                    <span>{{trans('lang.otp_verify')}}</span>
                </button>
                <button type="button" onclick="sendOTP()" id="sendotp_btn" class="btn-auth-primary mt-4">
                    <span>{{trans('lang.otp_send')}}</span>
                </button>

                <div class="text-center mt-4">
                    <a href="javascript:void(0)" onclick="loginBackClick()" class="forgot-password-link">
                        <i class="fa fa-arrow-left"></i> {{trans('lang.back_to_login')}}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-firestore.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-storage.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-database.js"></script>
    <script src="{{ asset('js/jquery.cookie.js') }}"></script>

    <script>
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

        var database;
        try {
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }
            database = firebase.firestore();
        } catch (error) {
            console.error("Firebase initialization failed:", error);
        }

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

        function loginWithPhoneClick() {
            $('#login-form').hide();
            $('#otp-login-form').show();
            $('#recaptcha-container').show();
            
            if(!window.recaptchaVerifier) {
                window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    'size': 'invisible',
                    'callback': (response) => {}
                });
            }
        }

        function loginBackClick() {
            $('#otp-login-form').hide();
            $('#login-form').show();
            $('#phone-box').show();
            $('#otp-box').hide();
            $('#sendotp_btn').show();
            $('#verify_btn').hide();
        }

        function googleAuth() {
            var provider = new firebase.auth.GoogleAuthProvider();
            firebase.auth().signInWithPopup(provider)
                .then(function(result) {
                    var user = result.user;
                    checkVendorAccount(user);
                }).catch(function(error) {
                    console.error("Google Sign-In Error:", error.message);
                    $('#error').html("Google authentication failed: " + error.message).show();
                });
        }

        function checkVendorAccount(user) {
            database.collection("users").where("email", "==", user.email).get().then(function(snapshots) {
                if (!snapshots.empty) {
                    var userData = snapshots.docs[0].data();
                    if (userData.role === 'vendor') {
                         if (userData.active) {
                            var uid = userData.id;
                            var firstName = userData.firstName;
                            var lastName = userData.lastName;
                            var imageURL = userData.profilePictureURL;

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('setToken') }}",
                                data: {
                                    id: uid,
                                    userId: uid,
                                    email: userData.email,
                                    password: '',
                                    firstName: firstName,
                                    lastName: lastName,
                                    profilePicture: imageURL
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (data) {
                                    if (data.access) {
                                        window.location = "{{ route('dashboard') }}";
                                    }
                                }
                            });
                         } else {
                             $('#error').html("{{trans('lang.account_waiting_approval')}}").show();
                         }
                    } else {
                        $('#error').html("{{trans('lang.not_a_vendor')}}").show();
                    }
                } else {
                    $('#error').html("{{trans('lang.user_not_found')}}").show();
                }
            });
        }

        function sendOTP() {
            var phone = $('#phone').val();
            var countryCode = $('#country_selector').val();
            
            $('#phone-error').hide();

            if (!phone) {
                $('#phone-error').text("{{trans('lang.enter_valid_phone')}}").show();
                return;
            }

            var phoneNumber = '+' + countryCode + phone;
            
            database.collection("users").where("phoneNumber", "==", phone).where("role", "==", 'vendor').where('active', '==', true).get().then(async function (snapshots) {
                if (snapshots.docs.length) {
                    firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                        .then(function (confirmationResult) {
                            window.confirmationResult = confirmationResult;
                            $('#phone-box').hide();
                            $('#otp-box').show();
                            $('#sendotp_btn').hide();
                            $('#verify_btn').show();
                        }).catch(function (error) {
                            $('#phone-error').text(error.message).show();
                        });
                } else {
                     $('#phone-error').text("{{trans('lang.user_not_found')}}").show();
                }
            });
        }

        function applicationVerifier() {
            var otp = $('#verificationcode').val();
            if(!otp) {
                $('#otp-error').text("{{trans('lang.enter_otp')}}").show();
                return;
            }

            window.confirmationResult.confirm(otp).then(function (result) {
                var phone = $('#phone').val();
                database.collection("users").where('phoneNumber', '==', phone).get().then(function (snapshots) {
                    var userData = snapshots.docs[0].data();
                    if (userData) {
                        var uid = userData.id;
                        var firstName = userData.firstName;
                        var lastName = userData.lastName;
                        var imageURL = userData.profilePictureURL;
                        
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('setToken') }}",
                            data: {
                                id: uid,
                                userId: uid,
                                email: userData.email || userData.phoneNumber, 
                                password: '',
                                firstName: firstName,
                                lastName: lastName,
                                profilePicture: imageURL
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (data) {
                                if (data.access) {
                                    window.location = "{{ route('dashboard') }}";
                                }
                            }
                        });
                    }
                });
            }).catch(function (error) {
                $('#otp-error').text("{{trans('lang.otp_invalid')}}").show();
            });
        }
        
        $(document).ready(function() {
            $("#country_selector").select2({
                width: 'resolve' 
            });

            // Remember Me Logic
            if (localStorage.getItem("remember_me") === "true") {
                $("#email").val(localStorage.getItem("email"));
                $("#password").val(localStorage.getItem("password"));
                $("#remember").prop("checked", true);
            }

            $('#login-form').on('submit', function() {
                if ($("#remember").is(":checked")) {
                    localStorage.setItem("remember_me", "true");
                    localStorage.setItem("email", $("#email").val());
                    localStorage.setItem("password", $("#password").val());
                } else {
                    localStorage.removeItem("remember_me");
                    localStorage.removeItem("email");
                    localStorage.removeItem("password");
                }
            });
        });
    </script>
</body>
</html>