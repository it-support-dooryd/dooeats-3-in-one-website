@include('auth.default')
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
<link href="{{ asset('vendor/select2/dist/css/select2.min.css')}}" rel="stylesheet">
<link href="{{ asset('/css/font-awesome.min.css')}}" rel="stylesheet">
<link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">

<body class="auth-body">
    <div class="auth-overlay"></div>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats Logo">
            </div>

            <!-- Tab Bar -->
            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab-link active">{{trans('lang.customer_login')}}</a>
                <a href="http://127.0.0.1:8001/login" class="auth-tab-link">{{trans('lang.restaurant_login')}}</a>
            </div>

            <h4 class="text-center mb-4" style="font-weight: 600; color: #333;">{{trans('lang.welcome_back')}}</h4>
            <div class="error" id="error" style="display:none; color:red; text-align:center;"></div>

            <!-- Login Form -->
            <form action="#" onsubmit="return loginClick()" id="login-box">
                <div class="form-group-auth">
                    <label class="form-label-auth">{{trans('lang.user_email')}}</label>
                    <input type="email" class="form-control-auth" id="email" name="email"
                        placeholder="{{trans('lang.user_email_help_2')}}">
                    <div class="error" id="email_required"
                        style="display:none; color:red; font-size:12px; margin-top:5px;"></div>
                </div>

                <div class="form-group-auth">
                    <label class="form-label-auth">{{trans('lang.password')}}</label>
                    <div class="password-input-group">
                        <input type="password" class="form-control-auth" id="password" name="password"
                            placeholder="{{trans('lang.user_password_help_2')}}">
                        <div class="password-toggle-icon" onclick="togglePassword()">
                            <i class="fa fa-eye"></i>
                        </div>
                    </div>
                    <div class="error" id="password_required"
                        style="display:none; color:red; font-size:12px; margin-top:5px;"></div>
                </div>

                <div class="remember-me-group">
                    <label class="custom-switch-auth">
                        <input type="checkbox" id="remember_me">
                        <span class="slider-auth"></span>
                        <span>{{trans('lang.remember_me')}}</span>
                    </label>
                    <a href="{{url('forgot-password')}}"
                        class="forgot-password-link">{{trans('lang.forgot_password')}}?</a>
                </div>

                <button type="submit" class="btn-auth-primary" id="login_btn">{{trans('lang.log_in')}}</button>

                <div class="text-center mt-3">
                    <span style="color:#666; font-size:14px;">{{trans('lang.dont_have_account')}} </span>
                    <a href="{{route('signup')}}" class="forgot-password-link">{{trans('lang.sign_up')}}</a>
                </div>

                <div class="or-divider">
                    <span>{{trans('lang.or_continue_with')}}</span>
                </div>

                <div class="social-login-group">
                    <div class="social-btn" onclick="googleAuth()" title="Google">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="24px" height="24px">
                            <path fill="#FFC107"
                                d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" />
                            <path fill="#FF3D00"
                                d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" />
                            <path fill="#4CAF50"
                                d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z" />
                            <path fill="#1976D2"
                                d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z" />
                        </svg>
                    </div>
                    <!-- Apple would go here if configured -->
                    <div class="social-btn" onclick="loginWithPhoneClick()" title="Phone">
                        <i class="fa fa-phone" style="font-size: 20px; color: #333;"></i>
                    </div>
                </div>
            </form>

            <!-- Phone Login Form (Hidden) -->
            <form id="login-with-phone-box" action="#" style="display:none;">
                @csrf
                <div class="text-center mb-4">
                    <h5 style="font-weight:600;">{{trans('lang.login_with_phone')}}</h5>
                </div>

                <div class="form-group-auth" id="phone-box">
                    <label class="form-label-auth">{{trans('lang.user_phone')}}</label>
                    <div style="display:flex;">
                        <select name="country" id="country_selector" class="form-control-auth"
                            style="width: 35%; margin-right: 5px;">
                            <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                            <?php    $selected = ""; ?>
                            <option <?php    echo $selected; ?> code="<?php    echo $valuecy->code; ?>"
                                value="<?php    echo $keycy; ?>">+<?php    echo $valuecy->phoneCode; ?></option>
                            <?php } ?>
                        </select>
                        <input placeholder="{{trans('lang.user_phone')}}" id="phone" type="number"
                            class="form-control-auth" name="phone" required>
                    </div>
                    <div class="error" id="password_required_new1" style="display:none; color:red;"></div>
                </div>

                <div class="form-group-auth" id="otp-box" style="display:none;">
                    <label class="form-label-auth">{{trans('lang.otp')}}</label>
                    <input class="form-control-auth" placeholder="{{trans('lang.otp')}}" id="verificationcode"
                        type="text" name="otp">
                </div>

                <div id="recaptcha-container" style="display:none;"></div>

                <button type="button" style="display:none;" onclick="applicationVerifier()" id="verify_btn"
                    class="btn-auth-primary">{{trans('lang.otp_verify')}}</button>
                <button type="button" style="display:none;" onclick="sendOTP()" id="sendotp_btn"
                    class="btn-auth-primary">{{trans('lang.otp_send')}}</button>

                <div class="text-center mt-3">
                    <a href="javascript:void(0)" onclick="loginBackClick()" class="forgot-password-link">
                        <i class="fa fa-arrow-left"></i> {{trans('lang.back_to_login')}}
                    </a>
                </div>
                <div class="error" id="password_required_new" style="color:red; text-align:center; margin-top:10px;">
                </div>
            </form>

        </div>
    </div>
</body>

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

    // Remember Me Logic
    $(document).ready(function () {
        if (localStorage.getItem("remember_me") === "true") {
            $("#email").val(localStorage.getItem("email"));
            $("#password").val(localStorage.getItem("password"));
            $("#remember_me").prop("checked", true);
        }
    });

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

    function loginClick() {
        var email = $("#email").val();
        var password = $("#password").val();
        $("#email_required").css('display', 'none');
        $("#password_required").html("");
        $("#password_required").hide();

        if (email == '') {
            $("#email_required").css('display', 'block');
            jQuery("#email_required").html("{{trans('lang.user_email_help_2')}}");
            return false;
        }
        else if (password == '') {
            $("#password_required").css('display', 'block');
            jQuery("#password_required").html("{{trans('lang.user_password_help_2')}}");
            return false;
        }

        // Remember Me Save
        if ($("#remember_me").is(":checked")) {
            localStorage.setItem("remember_me", "true");
            localStorage.setItem("email", email);
            localStorage.setItem("password", password);
        } else {
            localStorage.removeItem("remember_me");
            localStorage.removeItem("email");
            localStorage.removeItem("password");
        }

        firebase.auth().signInWithEmailAndPassword(email, password).then(function (result) {
            var userEmail = result.user.email;
            database.collection("users").where("email", "==", email).where('active', '==', true).get().then(async function (snapshots) {
                if (snapshots.docs.length) {
                    var userData = snapshots.docs[0].data();
                    if (userData.role == "customer") {
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
                            success: function (data) {
                                if (data.access) {
                                    setCookie("loginType", "EmailPassword");
                                    window.location = "{{url('/')}}"; // Redirect to Customer Home
                                }
                            }
                        });
                    }
                } else {
                    $("#password_required").css('display', 'block');
                    $("#password_required").html("{{trans('lang.waiting_for_approval')}}");
                }
            })
        })
            .catch(function (error) {
                $("#password_required").css('display', 'block');
                $("#password_required").html("{{trans('lang.invalid_password_email')}}");
            });
        return false;
    }

    function loginWithPhoneClick() {
        jQuery("#login-box").hide();
        jQuery("#login-with-phone-box").show();
        jQuery("#phone-box").show();
        jQuery("#recaptcha-container").show();
        jQuery("#sendotp_btn").show();
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
            'size': 'invisible',
            'callback': (response) => { }
        });
    }

    function loginBackClick() {
        jQuery("#login-box").show();
        jQuery("#login-with-phone-box").hide();
        jQuery("#otp-box").hide();
        jQuery("#sendotp_btn").hide();
        jQuery("#verify_btn").hide();
    }

    function sendOTP() {
        $("#password_required_new1").hide();
        if (jQuery("#phone").val() == "") {
            $("#password_required_new1").show();
            jQuery("#password_required_new1").html("{{trans('lang.enter_valid_phone')}}");
        } else if (jQuery("#phone").val() && jQuery("#country_selector").val()) {
            var countryCode = '+' + jQuery("#country_selector").val();
            var phone = jQuery("#phone").val();
            var phoneNumber = '+' + jQuery("#country_selector").val() + '' + jQuery("#phone").val();
            database.collection("users").where("phoneNumber", "==", phone).where("role", "==", 'customer').where('active', '==', true).get().then(async function (snapshots) {
                if (snapshots.docs.length) {
                    var userData = snapshots.docs[0].data();
                    firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                        .then(function (confirmationResult) {
                            window.confirmationResult = confirmationResult;
                            if (confirmationResult.verificationId) {
                                jQuery("#phone-box").hide();
                                jQuery("#recaptcha-container").hide();
                                jQuery("#otp-box").show();
                                jQuery("#verify_btn").show();
                                jQuery("#sendotp_btn").hide();
                                $("#password_required_new1").css('display', 'none');
                            }
                        });
                } else {
                    jQuery("#password_required_new1").show();
                    jQuery("#password_required_new1").html("{{trans('lang.user_not_found')}}");
                }
            });
        }
    }

    function applicationVerifier() {
        window.confirmationResult.confirm(document.getElementById("verificationcode").value)
            .then(function (result) {
                var phone = jQuery("#phone").val();
                database.collection("users").where('phoneNumber', '==', phone).get().then(async function (snapshots_login) {
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
                                success: function (data) {
                                    if (data.access) {
                                        setCookie("loginType", "Phone");
                                        window.location = "{{url('/')}}";
                                    }
                                }
                            });
                        } else {
                            $('#email_required').text('');
                            jQuery("#password_required_new").html("User not found.");
                        }
                    }
                })
            }).catch(function (error) {
                jQuery("#password_required_new").html("{{trans('lang.otp_invalid')}}");
            });
    }
    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "{{ url('/') }}/flags/120/";
        var $state = $(
            '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    }
    function formatState2(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo \URL::to('/'); ?>/flags/120/"
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
            allowClear: true
        });
    });
    function googleAuth() {
        var provider = new firebase.auth.GoogleAuthProvider();
        firebase.auth().signInWithPopup(provider).then(function (result) {
            var user = result.user;
            var userEmail = result.user.email;
            database.collection("users").where("email", "==", userEmail).where('active', '==', true).get().then(async function (snapshots) {
                if (snapshots.docs.length) {
                    var userData = snapshots.docs[0].data();
                    if (userData.role == "customer") {
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
                                email: userEmail,
                                password: "",
                                firstName: firstName,
                                lastName: lastName,
                                profilePicture: imageURL
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (data) {
                                if (data.access) {
                                    setCookie("loginType", "Google");
                                    window.location = "{{url('/')}}";
                                }
                            }
                        });
                    } else {
                        $("#password_required").css('display', 'block');
                        $("#password_required").html("{{trans('lang.waiting_for_approval')}}");
                    }
                } else {
                    // Redirect to social signup
                    var firstName = user.displayName ? user.displayName.split(' ')[0] : '';
                    var lastName = user.displayName ? (user.displayName.split(' ')[1] || '') : '';
                    var uuid = user.uid;
                    var email = user.email;
                    var photoURL = user.photoURL;
                    var phoneNumber = user.phoneNumber || '';
                    var redirectUrl = "{{ url('socialsignup') }}?uuid=" + uuid + "&firstName=" + firstName + "&lastName=" + lastName + "&email=" + email + "&photoURL=" + photoURL + "&phoneNumber=" + phoneNumber;
                    window.location.href = redirectUrl;
                }
            });
        }).catch(function (error) {
            console.log(error);
        });
    }
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
</script>