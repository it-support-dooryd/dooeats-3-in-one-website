<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Dooeats') }} - Login</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/dist/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css')}}" rel="stylesheet">
</head>
<body class="auth-body">
    <?php
        $filepath = public_path('countriesdata.json');
        $countries = file_get_contents($filepath);
        $countries = json_decode($countries);
        $countries = (array) $countries;
        $newcountries = array();
        $newcountriesjs = array();
        foreach ($countries as $keycountry => $valuecountry) {
            $newcountries[$valuecountry->phoneCode] = $valuecountry;
            $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
        }
    ?>

    <div class="auth-overlay"></div>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats Logo">
            </div>

            <!-- Tab Bar -->
            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab-link active">Customer</a>
                <a href="http://127.0.0.1:8002/login" class="auth-tab-link">Restaurant</a>
            </div>

            <h4 class="text-center mb-4" style="font-weight: 600; color: #333;">{{trans('lang.sign_in')}}</h4>
            
            <div class="error" style="color: red; text-align: center;" id="field_error"></div>
            <div class="error" id="field_error1" style="color:red; display:none; text-align: center;"></div>

            <form class="auth-form" action="javascript:void(0)" onsubmit="return loginClick()">
                
                <div id="login-fields">
                    <div class="form-group-auth" id="email_div">
                        <input type="email" class="form-control-auth" id="email" placeholder="Enter Email Address" autocomplete="email" required>
                        <input type="hidden" id="hidden_email" />
                    </div>

                    <div class="form-group-auth" id="phone-box" style="display:none;">
                        <div class="phone-input-wrapper" style="display: flex; gap: 8px;">
                            <select name="country" id="country_selector" class="form-control-auth" style="width: 120px; padding-left: 10px;">
                                <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                    <option code="<?php echo $valuecy->code; ?>" value="<?php echo $keycy; ?>">+<?php echo $valuecy->phoneCode; ?></option>
                                <?php } ?>
                            </select>
                            <input class="form-control-auth" placeholder="{{trans('lang.user_phone')}}" id="mobileNumber" type="number" name="mobileNumber" style="flex: 1;">
                        </div>
                        <input type="hidden" id="hidden_countrycode" />
                        <input type="hidden" id="hidden_phone" />
                    </div>

                    <div class="form-group-auth" id="pass_div">
                        <div class="password-input-group">
                            <input type="password" class="form-control-auth" id="password" placeholder="Enter Password" minlength="8" required autocomplete="current-password">
                            <div class="password-toggle-icon" onclick="togglePassword()">
                                <i class="fa fa-eye"></i>
                            </div>
                        </div>
                    </div>

                    <div class="remember-me-group">
                        <label class="custom-switch-auth">
                            <input type="checkbox" id="remember_me">
                            <span class="slider-auth"></span>
                            Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-password-link">Forgot Password?</a>
                    </div>
                </div>

                <div class="form-group-auth" id="otp-box" style="display:none;">
                     <label class="form-label-auth">{{trans('lang.otp')}}</label>
                    <input class="form-control-auth" placeholder="{{trans('lang.otp')}}" id="verificationcode" type="text" name="otp" autocomplete="otp">
                    <div class="otp_error" style="color:red;"></div>
                </div>

                <div id="recaptcha-container" style="display:none;"></div>

                <button type="submit" class="btn-auth-primary btn-login" id="btn-login">{{trans('lang.sign_in')}}</button>
                
                <button type="button" style="display:none;" onclick="applicationVerifier()" id="verify_btn" class="btn-auth-primary">{{trans('lang.otp_verify')}}</button>
                
                <button type="button" class="btn-auth-primary" onclick="sendOTP()" id="send-code" style="display:none">
                    {{trans('lang.otp_send')}}
                </button>

                <div class="or-divider">
                    <span>OR</span>
                </div>

                <div class="social-login-group">
                    <div class="social-btn" onclick="loginWithPhone()" id="btn-login-phone" title="{{trans('lang.signin_with_phone')}}" style="width: 100%; border-radius: 12px; gap: 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <span style="font-weight: 500;">{{trans('lang.signin_with_phone')}}</span>
                    </div>

                    <div class="social-btn" onclick="loginWithEmail()" id="btn-login-email" style="display:none; width: 100%; border-radius: 12px; gap: 10px;">
                         <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                         <span style="font-weight: 500;">{{trans('lang.signin_with_email')}}</span>
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
    <script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-firestore.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-storage.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-database.js"></script>
    
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
             // Show error in UI
             jQuery(document).ready(function() {
                 $("#field_error").html("System error: Failed to connect to services. Please try again later.").show();
                 $(".btn-login").prop('disabled', true);
                 $("#btn-login-phone").prop('disabled', true);
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
            
            firebase.auth().signInWithEmailAndPassword(email, password)
                .then((userCredential) => {
                    var uuid = userCredential.user.uid;
                    
                    // Check if user is a customer
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

        var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
        var newcountriesjs = JSON.parse(newcountriesjs);
        function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "<?php echo url('/'); ?>/flags/120/";
            var $state = $(
                '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
            );
            return $state;
        }
        function formatState2(state) {
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "<?php echo url('/'); ?>/flags/120/";
            var $state = $(
                '<span><img class="img-flag" /> <span></span></span>'
            );
            $state.find("span").text(state.text);
            $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".png");
            return $state;
        }
        jQuery(document).ready(function () {
            jQuery("#country_selector").select2({
                templateResult: formatState,
                templateSelection: formatState2,
                placeholder: "Select Country",
                allowClear: true,
                width: '100%'
            });
        });

        function loginWithPhone() {
            $('#email_div').hide();
            $('#pass_div').hide();
            $('#btn-login-phone').hide();
            $('#btn-login').hide();
            $('#send-code').show();
            $('#btn-login-email').show();
            $('#phone-box').show();
            jQuery("#otp-box").hide();

            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'size': 'invisible',
                'callback': (response) => {
                }
            });
        }

        function loginWithEmail() {
            $('#email_div').show();
            $('#pass_div').show();
            $('#phone-box').hide();
            $('#btn-login-phone').show();
            $('#btn-login').show();
            $('#send-code').hide();
            $('#verify_btn').hide();
            jQuery("#otp-box").hide();
            $('#verificationcode').removeAttr('required');
            $('#btn-login-email').hide();
        }

        function sendOTP() {
            if (jQuery("#mobileNumber").val() && jQuery("#country_selector").val()) {
                $("#field_error1").hide();
                var phoneNumber = '+' + jQuery("#country_selector").val() + jQuery("#mobileNumber").val();
                
                database.collection("users").where('phoneNumber', '==', phoneNumber).get().then(async function (snapshots) {
                    if (snapshots.docs.length > 0) {
                        var userData = snapshots.docs[0].data();
                        if (userData.role === "customer") {
                            $("#hidden_phone").val(jQuery("#mobileNumber").val());
                            $("#hidden_countrycode").val(jQuery("#country_selector").val());
                            
                            firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                                .then(function (confirmationResult) {
                                    window.confirmationResult = confirmationResult;
                                    if (confirmationResult.verificationId) {
                                        $('#phone-box').hide();
                                        $('#send-code').hide();
                                        jQuery("#recaptcha-container").hide();
                                        jQuery("#otp-box").show();
                                        $('#verificationcode').attr('required', 'true');
                                        jQuery("#verify_btn").show();
                                    }
                                }).catch((error) => {
                                console.error("Error sending OTP: ", error);
                                $("#field_error").html(error.message).show();
                                window.scrollTo(0, 0);
                            });
                        } else {
                            $("#field_error1").html("This phone number is not registered as a customer.").show();
                        }
                    } else {
                        $("#field_error1").html("{{trans('lang.no_account_with_number')}}").show();
                        return false;
                    }
                });
            }
        }

        function applicationVerifier() {
            var code = $('#verificationcode').val();
            if (code == "") {
                $('.otp_error').html('Please Enter OTP').show();
            } else {
                window.confirmationResult.confirm(document.getElementById("verificationcode").value)
                    .then(async function (result) {
                        var mobileNumber = result.user.phoneNumber;
                        var uuid = result.user.uid;
                        
                        var url = "{{route('newLogin')}}";
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                userId: uuid,
                                email: mobileNumber,
                                password: ""
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (data) {
                                if (data.access) {
                                    window.location = "{{url('/')}}";
                                }
                            }
                        });
                    }).catch((error) => {
                        $('.otp_error').html("Invalid OTP. Please try again.").show();
                    });
            }
        }
    </script>
</body>
</html>
