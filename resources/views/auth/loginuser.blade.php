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
    <link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
</head>
<body>
    <?php
    // Load countries data for phone authentication
    $filepath = public_path('countriesdata.json');
    $countries = file_get_contents($filepath);
    $countries = json_decode($countries);
    $countries = (array) $countries;
    $newcountries = [];
    $newcountriesjs = [];
    foreach ($countries as $keycountry => $valuecountry) {
        $newcountries[$valuecountry->phoneCode] = $valuecountry;
        $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
    }
    ?>

    <!-- Main Auth Page Container -->
    <div class="auth-page">
        <div class="auth-container">
            
            <!-- Left Panel: Login Form -->
            <div class="auth-form-panel">
                
                <!-- Tab Switcher: Restaurant / Customer -->
                <div class="auth-tabs show-tabs">
                    <a href="http://127.0.0.1:8001/login" class="auth-tab" data-tab="restaurant">
                        Restaurant Login
                    </a>
                    <button class="auth-tab active" data-tab="customer">
                        Customer Login
                    </button>
                </div>

                <!-- Header -->
                <div class="auth-header" style="text-align: center;">
                    <h1 class="auth-title">{{trans('lang.welcome_back')}}</h1>
                    <p class="auth-subtitle">{{trans('lang.sign_in_to_continue')}}</p>
                </div>

                <!-- Email/Password Login Form -->
                <form id="login-form" class="auth-form">
                    @csrf
                    
                    <!-- Email Input -->
                    <div class="form-group">
                        <div class="form-input-wrapper">
                            <input type="email" id="email" name="email" class="form-input" placeholder="admin@dooeats.com" autocomplete="email">
                        </div>
                        <div class="error-message" id="email-error"></div>
                    </div>

                    <!-- Password Input with Toggle -->
                    <div class="form-group">
                        <div class="form-input-wrapper">
                            <input type="password" id="password" name="password" class="form-input" placeholder="••••••••••••" autocomplete="current-password">
                            <!-- Show/Hide Password Toggle -->
                            <svg class="password-toggle" id="toggle-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div class="error-message" id="password-error"></div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="remember-me-wrapper">
                        <label class="toggle-label">
                            <input type="checkbox" id="remember-me">
                            <span>Remember Me</span>
                        </label>
                        <a href="{{url('forgot-password')}}" class="auth-link" style="font-size: 0.875rem; color: var(--secondary-color);">Forgot Password?</a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn btn-primary" id="login-btn">
                        <span id="login-btn-text" style="flex: 1; text-align: center;">LOG IN</span>
                    </button>

                    <!-- Social Auth Buttons -->
                    <div class="social-auth-buttons" style="margin-top: 1.5rem;">
                        <button type="button" class="social-btn" id="google-auth-btn">
                            <div class="social-btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                                    <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                                    <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                                    <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                                </svg>
                            </div>
                            Google
                        </button>
                        <button type="button" class="social-btn" id="apple-auth-btn">
                            <div class="social-btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                </svg>
                            </div>
                            Apple
                        </button>
                    </div>

                    <!-- Login with OTP Link -->
                    <div class="text-center mt-2">
                        <button type="button" class="auth-link" id="show-otp-login" style="background: none; border: none; cursor: pointer; font-size: 0.875rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display: inline-block; margin-right: 0.25rem;">
                                <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                                <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/>
                            </svg>
                            Login with OTP
                        </button>
                    </div>
                </form>

                <!-- OTP Login Form (Hidden by default) -->
                <form id="otp-login-form" class="auth-form otp-section">
                    @csrf
                    
                    <div id="phone-input-section">
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <select name="country" id="country-selector" class="country-selector">
                                <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                    <option code="<?php echo $valuecy->code; ?>" value="<?php echo $keycy; ?>">
                                        +<?php echo $valuecy->phoneCode; ?> {{$valuecy->countryName}}
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{trans('lang.user_phone')}}</label>
                            <div class="form-input-wrapper">
                                <input type="tel" id="phone" name="phone" class="form-input" placeholder="{{trans('lang.user_phone')}}">
                            </div>
                            <div class="error-message" id="phone-error"></div>
                        </div>

                        <button type="button" class="btn btn-primary" id="send-otp-btn">
                            {{trans('lang.otp_send')}}
                        </button>
                    </div>

                    <div id="otp-input-section" class="hidden">
                        <div class="form-group">
                            <label class="form-label">{{trans('lang.otp')}}</label>
                            <div class="form-input-wrapper">
                                <input type="text" id="otp-code" name="otp" class="form-input" placeholder="Enter 6-digit code" maxlength="6">
                            </div>
                            <div class="error-message" id="otp-error"></div>
                        </div>

                        <button type="button" class="btn btn-primary" id="verify-otp-btn">
                            {{trans('lang.otp_verify')}}
                        </button>
                    </div>

                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <button type="button" class="btn btn-outline" id="back-to-email-login">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                        </svg>
                        {{ __('Login') }} {{trans('lang.with_email')}}
                    </button>
                </form>

                <!-- Hidden reCAPTCHA container -->
                <div id="recaptcha-container"></div>

                <!-- Footer -->
                <div class="auth-footer">
                    Don't have an account? <a href="{{route('signup')}}" class="auth-link">{{trans('lang.sign_up')}}</a>
                </div>
            </div>



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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="{{ asset('js/crypto-js.js') }}"></script>
    <script src="{{ asset('js/jquery.cookie.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.js') }}"></script>

    <script type="text/javascript">
        var database = firebase.firestore();
        var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
        newcountriesjs = JSON.parse(newcountriesjs);

        // ============================================
        // PASSWORD TOGGLE FUNCTIONALITY
        // ============================================
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

        // ============================================
        // REMEMBER ME FUNCTIONALITY
        // ============================================
        $(document).ready(function() {
            const savedEmail = localStorage.getItem('customer_email');
            const savedPassword = localStorage.getItem('customer_password');
            const rememberMe = localStorage.getItem('customer_remember_me');

            if (rememberMe === 'true' && savedEmail && savedPassword) {
                $('#email').val(savedEmail);
                $('#password').val(atob(savedPassword));
                $('#remember-me').prop('checked', true);
            }

            $("#country-selector").select2({
                templateResult: formatState,
                templateSelection: formatState2,
                placeholder: "Select Country",
                allowClear: true
            });
        });

        function formatState(state) {
            if (!state.id) return state.text;
            var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";
            var $state = $(
                '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.png" class="img-flag" style="width: 20px; margin-right: 8px;" /> ' + state.text + '</span>'
            );
            return $state;
        }

        function formatState2(state) {
            if (!state.id) return state.text;
            var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/"
            var $state = $(
                '<span><img class="img-flag" style="width: 20px; margin-right: 8px;" /> <span></span></span>'
            );
            $state.find("span").text(state.text);
            $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".png");
            return $state;
        }

        // ============================================
        // EMAIL/PASSWORD LOGIN
        // ============================================
        $('#login-form').on('submit', function(e) {
            e.preventDefault();
            loginClick();
        });

        function loginClick() {
            var email = $("#email").val().trim();
            var password = $("#password").val();

            $("#email-error").removeClass('show').html('');
            $("#password-error").removeClass('show').html('');

            if (email == '') {
                $("#email-error").addClass('show').html('Please enter email id');
                return false;
            }
            
            if (password == '') {
                $("#password-error").addClass('show').html('Please enter valid password');
                return false;
            }

            $('#login-btn').prop('disabled', true);
            $('#login-btn-text').html('<span class="spinner"></span> Logging in...');

            firebase.auth().signInWithEmailAndPassword(email, password).then(function(result) {
                var userEmail = result.user.email;
                database.collection("users").where("email", "==", email).where('active', '==', true).get().then(async function(snapshots) {
                    if (snapshots.docs.length) {
                        var userData = snapshots.docs[0].data();
                        if (userData.role == "customer") {
                            if ($('#remember-me').is(':checked')) {
                                localStorage.setItem('customer_email', email);
                                localStorage.setItem('customer_password', btoa(password));
                                localStorage.setItem('customer_remember_me', 'true');
                            } else {
                                localStorage.removeItem('customer_email');
                                localStorage.removeItem('customer_password');
                                localStorage.removeItem('customer_remember_me');
                            }

                            var userToken = result.user.getIdToken();
                            var uid = result.user.uid;
                            var user = userData.id;
                            var firstName = userData.firstName;
                            var lastName = userData.lastName;
                            var imageURL = userData.profilePictureURL;
                            var url = "{{route('setToken')}}";
                            $.ajax({
                                type: 'POST',
                                url: url,
                                data: {
                                    id: uid,
                                    userId: user,
                                    email: email,
                                    password: password,
                                    firstName: firstName,
                                    lastName: lastName,
                                    profilePicture: imageURL
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(data) {
                                    if (data.access) {
                                        setCookie("loginType", "EmailPassword");
                                        window.location = "{{url('/')}}";
                                    }
                                }
                            });
                        } else {
                            $("#password-error").addClass('show').html('This account is not a customer account');
                            $('#login-btn').prop('disabled', false);
                            $('#login-btn-text').html('{{trans("lang.log_in")}}');
                        }
                    } else {
                        $("#password-error").addClass('show').html('{{trans("lang.waiting_for_approval")}}');
                        $('#login-btn').prop('disabled', false);
                        $('#login-btn-text').html('{{trans("lang.log_in")}}');
                    }
                })
            })
            .catch(function(error) {
                $("#password-error").addClass('show').html("The entered password is invalid. Please check and try again.");
                $('#login-btn').prop('disabled', false);
                $('#login-btn-text').html('{{trans("lang.log_in")}}');
            });
            return false;
        }

        // ============================================
        // GOOGLE AUTHENTICATION
        // ============================================
        $('#google-auth-btn').click(function() {
            googleAuth();
        });

        function googleAuth() {
            var provider = new firebase.auth.GoogleAuthProvider();
            firebase.auth().signInWithPopup(provider)
                .then(function(result) {
                    var user = result.user;
                    saveUserData(user);
                }).catch(function(error) {
                    console.error("Google Sign-In Error:", error.message);
                });
        }

        function saveUserData(user, event) {
            database.collection("users").doc(user.uid).get().then(async function(snapshots_login) {
                var userData = snapshots_login.data();
                if (userData) {
                    if (userData.role == "customer") {
                        var uid = user.uid;
                        var firstName = userData.firstName || '';
                        var lastName = userData.lastName || '';
                        var imageURL = userData.profilePictureURL || '';
                        var url = "{{route('setToken')}}";
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                id: uid,
                                userId: user.uid,
                                email: userData.email || '',
                                password: '',
                                firstName: firstName,
                                lastName: lastName,
                                profilePicture: imageURL,
                                provider: 'google'
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                if (data.access) {
                                    setCookie("loginType", "Social");
                                    window.location = "{{url('/')}}";
                                }
                            }
                        });
                    } else {
                        $("#password-error").addClass('show').html("User not found.");
                    }
                } else {
                    var phoneNumber = user.phoneNumber || '';
                    var firstName = user.displayName ? user.displayName.split(' ')[0] : '';
                    var lastName = '';
                    if (user.displayName.split(' ')[1] == "" || user.displayName.split(' ')[1] == null || user.displayName.split(' ')[1] == undefined) {
                        lastName = " ";
                    } else {
                        lastName = user.displayName.split(' ')[1];
                    }
                    var uuid = user.uid;
                    var email = user.email || '';
                    var photoURL = user.photoURL || '';
                    var createdAtman = firebase.firestore.Timestamp.fromDate(new Date());
                    var redirectUrl = `{{ url('socialsignup') }}?uuid=${encodeURIComponent(uuid)}&phoneNumber=${encodeURIComponent(phoneNumber)}&firstName=${encodeURIComponent(firstName)}&lastName=${encodeURIComponent(lastName)}&email=${encodeURIComponent(email)}&photoURL=${encodeURIComponent(photoURL)}&createdAt=${createdAtman.toDate()}`;
                    window.location.href = redirectUrl;
                }
            }).catch(function(error) {
                console.log(error);
                $("#password-error").addClass('show').html(error.message);
            });
        }

        // ============================================
        // APPLE AUTHENTICATION
        // ============================================
        $('#apple-auth-btn').click(function() {
            alert("Apple Sign-In will be implemented based on your Apple Developer configuration.");
        });

        // ============================================
        // OTP LOGIN FLOW
        // ============================================
        $('#show-otp-login').click(function() {
            $('#login-form').hide();
            $('#otp-login-form').addClass('active');
            
            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'size': 'invisible',
                'callback': (response) => {}
            });
        });

        $('#back-to-email-login').click(function() {
            $('#otp-login-form').removeClass('active');
            $('#login-form').show();
            $('#phone-input-section').show();
            $('#otp-input-section').addClass('hidden');
        });

        $('#send-otp-btn').click(function() {
            sendOTP();
        });

        function sendOTP() {
            var phone = $('#phone').val().trim();
            var countryCode = $('#country-selector').val();

            $('#phone-error').removeClass('show').html('');

            if (phone == '') {
                $('#phone-error').addClass('show').html('Enter valid phone number');
                return;
            }

            var phoneNumber = '+' + countryCode + phone;

            $('#send-otp-btn').prop('disabled', true).html('<span class="spinner"></span> Sending...');

            database.collection("users").where("phoneNumber", "==", phone).where("role", "==", 'customer').where('active', '==', true).get().then(async function(snapshots) {
                if (snapshots.docs.length) {
                    var userData = snapshots.docs[0].data();
                    firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                        .then(function(confirmationResult) {
                            window.confirmationResult = confirmationResult;
                            if (confirmationResult.verificationId) {
                                $('#phone-input-section').hide();
                                $('#otp-input-section').removeClass('hidden');
                                $('#send-otp-btn').prop('disabled', false).html('{{trans("lang.otp_send")}}');
                            }
                        });
                } else {
                    $('#phone-error').addClass('show').html("User not found.");
                    $('#send-otp-btn').prop('disabled', false).html('{{trans("lang.otp_send")}}');
                }
            });
        }

        $('#verify-otp-btn').click(function() {
            applicationVerifier();
        });

        function applicationVerifier() {
            var code = $('#otp-code').val().trim();
            var phone = $('#phone').val().trim();
            var countryCode = $('#country-selector').val();

            $('#otp-error').removeClass('show').html('');

            if (code === "") {
                $('#otp-error').addClass('show').html('Please Enter OTP');
                return;
            }

            $('#verify-otp-btn').prop('disabled', true).html('<span class="spinner"></span> Verifying...');

            window.confirmationResult.confirm(code).then(function(result) {
                database.collection("users").where('phoneNumber', '==', phone).get().then(async function(snapshots_login) {
                    userData = snapshots_login.docs[0].data();
                    if (userData) {
                        if (userData.role == "customer") {
                            var uid = result.user.uid;
                            var user = result.user.uid;
                            var firstName = userData.firstName;
                            var lastName = userData.lastName;
                            var imageURL = userData.profilePictureURL;
                            var url = "{{route('setToken')}}";
                            $.ajax({
                                type: 'POST',
                                url: url,
                                data: {
                                    id: uid,
                                    userId: user,
                                    email: userData.phoneNumber,
                                    password: '',
                                    firstName: firstName,
                                    lastName: lastName,
                                    profilePicture: imageURL
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(data) {
                                    if (data.access) {
                                        setCookie("loginType", "Phone");
                                        window.location = "{{url('/')}}";
                                    }
                                }
                            });
                        } else {
                            $('#otp-error').addClass('show').html("User not found.");
                            $('#verify-otp-btn').prop('disabled', false).html('{{trans("lang.otp_verify")}}');
                        }
                    }
                })
            }).catch(function(error) {
                $('#otp-error').addClass('show').html(error.message);
                $('#verify-otp-btn').prop('disabled', false).html('{{trans("lang.otp_verify")}}');
            });
        }

        // ============================================
        // UTILITY FUNCTIONS
        // ============================================
        function setCookie(cname, cvalue, exdays) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }
    </script>
</body>
</html>
