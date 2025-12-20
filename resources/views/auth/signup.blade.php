<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Dooeats') }}</title>
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

            <h4 class="text-center mb-4" style="font-weight: 600; color: #333;">{{trans('lang.sign_up_with_us')}}</h4>
            
            <div class="error" style="color: red; text-align: center;" id="field_error"></div>
            <div class="error" id="field_error1" style="color:red; display:none; text-align: center;"></div>

            <form class="auth-form" action="javascript:void(0)" onsubmit="return signupClick()">
                
                <div id="signup-fields">
                    <div class="form-row-auth">
                        <div class="form-group-auth" id="firstName_div">
                            <input type="text" class="form-control-auth" id="firstName" placeholder="Enter FirstName" required oninput="validateFName(this)">
                            <input type="hidden" id="hidden_fName" />
                        </div>

                        <div class="form-group-auth" id="lastName_div">
                            <input type="text" class="form-control-auth" id="lastName" placeholder="Enter LastName" required oninput="validateLName(this)">
                            <input type="hidden" id="hidden_lName" />
                        </div>
                    </div>

                    <div class="form-group-auth" id="email_div">
                        <input type="email" class="form-control-auth" id="email" placeholder="Enter Email Address" autocomplete="new-password" required>
                        <input type="hidden" id="hidden_email" />
                    </div>

                    <div class="form-group-auth" id="phone-box">
                        <div class="phone-input-wrapper" style="display: flex; gap: 8px;">
                            <select name="country" id="country_selector" class="form-control-auth" style="width: 120px; padding-left: 10px;">
                                <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                    <option code="<?php echo $valuecy->code; ?>" value="<?php echo $keycy; ?>">+<?php echo $valuecy->phoneCode; ?></option>
                                <?php } ?>
                            </select>
                            <input class="form-control-auth" placeholder="{{trans('lang.user_phone')}}" id="mobileNumber" type="number" name="mobileNumber" required style="flex: 1;">
                        </div>
                        <input type="hidden" id="hidden_countrycode" />
                        <input type="hidden" id="hidden_phone" />
                    </div>

                    <div class="form-group-auth" id="pass_div">
                        <div class="password-input-group">
                            <input type="password" class="form-control-auth" id="password" placeholder="Enter Password" minlength="8" required autocomplete="new-password">
                            <div class="password-toggle-icon" onclick="togglePassword()">
                                <i class="fa fa-eye"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-auth" id="referral_div">
                        <input type="text" class="form-control-auth" id="referral_code" placeholder="Enter Referral Code (Optional)">
                        <input type="hidden" id="hidden_referral" />
                    </div>
                </div>

                <div class="form-group-auth" id="otp-box" style="display:none;">
                     <label class="form-label-auth">{{trans('lang.otp')}}</label>
                    <input class="form-control-auth" placeholder="{{trans('lang.otp')}}" id="verificationcode" type="text" name="otp" autocomplete="otp">
                    <div class="otp_error" style="color:red;"></div>
                </div>

                <div id="recaptcha-container" style="display:none;"></div>

                <button type="submit" class="btn-auth-primary btn-sign-up" id="btn-sign-up">{{trans('lang.sign_up')}}</button>
                
                <button type="button" style="display:none;" onclick="applicationVerifier()" id="verify_btn" class="btn-auth-primary">{{trans('lang.otp_verify')}}</button>
                
                <button type="button" class="btn-auth-primary" onclick="sendOTP()" id="send-code" style="display:none">
                    {{trans('lang.otp_send')}}
                </button>

                <div class="or-divider">
                    <span>OR</span>
                </div>

                <div class="social-login-group">
                    <div class="social-btn" onclick="signupWithPhone()" id="btn-signup-phone" title="{{trans('lang.sinup_with_phone')}}" style="width: 100%; border-radius: 12px; gap: 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <span style="font-weight: 500;">{{trans('lang.sinup_with_phone')}}</span>
                    </div>

                    <div class="social-btn" onclick="signupWithEmail()" id="btn-signup-email" style="display:none; width: 100%; border-radius: 12px; gap: 10px;">
                         <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                         <span style="font-weight: 500;">{{trans('lang.signup_with_email')}}</span>
                    </div>
                </div>

                 <div class="text-center mt-3">
                    <span style="color:#666; font-size:14px;">{{trans('lang.already_an_account')}} </span>
                    <a href="{{url('login')}}" class="forgot-password-link">{{trans('lang.sign_in')}}</a>
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
    <!-- We don't need crypto-js.js or jquery.cookie.js / valdiate.js unless specifically used by old code which we are adapting -->
    
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
            var createdAtman = firebase.firestore.Timestamp.fromDate(new Date());
        } catch (error) {
            console.error("Firebase init error:", error);
             // Show error in UI
             jQuery(document).ready(function() {
                 $("#field_error").html("System error: Failed to connect to services. Please try again later.").show();
                 $(".btn-sign-up").prop('disabled', true);
                 $("#btn-signup-phone").prop('disabled', true);
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

        function validateFName(input) {
            // Remove leading and trailing spaces
            input.value = input.value.trimStart(); // Allow typing, but remove leading spaces
        }
        function validateLName(input) {
            // Remove leading and trailing spaces
            input.value = input.value.trimStart(); // Allow typing, but remove leading spaces
        }

        async function signupClick() {
            $(".btn-sign-up").text('Please wait...');
            var email = $("#email").val();
            var password = $("#password").val();
            var mobileNumber = '+' + jQuery("#country_selector").val() + '' + jQuery("#mobileNumber").val();
            var countryCode = jQuery("#country_selector").val();
            var mob_no = jQuery("#mobileNumber").val();
            var firstName = $("#firstName").val();
            var lastName = $("#lastName").val();
            var referralCode = $("#referral_code").val();
            var referralBy = '';
            
            if (referralCode) {
                var referralByRes = getReferralUserId(referralCode);
                var referralBy = await referralByRes.then(function (refUserId) {
                    return refUserId;
                });
            }
            
            var userReferralCode = Math.floor(Math.random() * 899999 + 100000);
            userReferralCode = userReferralCode.toString();
            
            firebase.auth().createUserWithEmailAndPassword(email, password)
                .then((userCredential) => {
                    var uuid = userCredential.user.uid;
                    database.collection("referral").doc(uuid).set({
                        'id': uuid,
                        'referralBy': referralBy ? referralBy : '',
                        'referralCode': userReferralCode,
                    });
                    database.collection("users").doc(uuid).set({
                        'appIdentifier':"web",
                        'email': email,
                        'firstName': firstName,
                        'lastName': lastName,
                        'id': uuid,
                        'countryCode':countryCode,
                        'phoneNumber': mob_no,
                        'role': "customer",
                        'profilePictureURL': "",
                        'provider':'email',
                        'createdAt': createdAtman,
                        'active':true
                    })
                        .then(() => {
                            firebase.auth().signInWithEmailAndPassword(email, password).then(function (result) {
                                var url = "{{route('newRegister')}}";
                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                        userId: uuid,
                                        email: email,
                                        password: password,
                                        firstName: firstName,
                                        lastName: lastName
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function (data) {
                                        if (data.access) {
                                            window.location = "{{url('/')}}";
                                        }
                                    }
                                })
                            })
                        })
                        .catch((error) => {
                            console.error("Error writing document: ", error);
                            $("#field_error").html(error).show();
                            window.scrollTo(0, 0);
                        });
                })
                .catch((error) => {
                    var errorCode = error.code;
                    var errorMessage = error.message;
                    $("#field_error").html(errorMessage).show();
                    window.scrollTo(0, 0);
                    $(".btn-sign-up").text("{{trans('lang.sign_up')}}");
                });
            return false;
        }

        async function getReferralUserId(referralCode) {
            var refUserId = database.collection('referral').where('referralCode', '==', referralCode).get().then(async function (snapshots) {
                if (snapshots.docs.length > 0) {
                    var referralData = snapshots.docs[0].data();
                    return referralData.id;
                }
            });
            return refUserId;
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

        function signupWithPhone() {
            $('#signup-fields').hide();
            $('#btn-signup-phone').hide();
            $('#btn-sign-up').hide();
            $('#send-code').show();
            $('#btn-signup-email').show();
            jQuery("#otp-box").hide();
            
            // Show only phone input and name for phone signup? 
            // The original logic hid lots of things:
            /*
            $('#pass_div').hide();
            $('#btn-signup-phone').hide();
            $('#btn-sign-up').hide();
            $('#send-code').show();
            $('#btn-signup-email').show();
            jQuery("#otp-box").hide();
            */
           // Replicating original behavior but with new IDs:
           $('#pass_div').hide();
           $('#email_div').hide();
           $('#referral_div').hide();
           
           // We need names and phone still visible
           $('#signup-fields').show(); // Keep parent visible
           $('#firstName_div').show();
           $('#lastName_div').show();
           $('#phone-box').show();

            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'size': 'invisible',
                'callback': (response) => {
                }
            });
        }

        function signupWithEmail() {
            $('#firstName_div').show();
            $('#lastName_div').show();
            $('#phone-box').show();
            $('#email_div').show();
            $('#pass_div').show();
            $('#referral_div').show();
            
            $('#signup-fields').show();

            $('#btn-signup-phone').show();
            $('#btn-sign-up').show();
            $('#send-code').hide();
            $('#verify_btn').hide();
            jQuery("#otp-box").hide();
            $('#verificationcode').removeAttr('required');
            $('#btn-signup-email').hide();
        }

        function sendOTP() {
            var firstName = $('#firstName').val();
            var lastName = $('#lastName').val();
            var referral = $('#referral_code').val();
            var email = $("#email").val(); // Email might be hidden or empty
            
            // Validation (adapted)
            if(firstName == ""){
                $("#field_error1").html("Please enter first name").show();
                return;
            }
            else if(lastName == ""){
                $("#field_error1").html("Please enter last name").show();
                return;
            }
            else if ($("#mobileNumber").val() == ""){
                $("#field_error1").html("Please enter phone number").show(); 
                return;
            }
            
            // If email is required for phone signup? Original code checked email if present, but for phone signup it hid email div.
            // Let's assume email is NOT required for phone signup flow based on original code hiding it.

            if (jQuery("#mobileNumber").val() && jQuery("#country_selector").val()) {
                $("#field_error1").hide();
                var phoneNumber = '+' + jQuery("#country_selector").val() + jQuery("#mobileNumber").val();
                    database.collection("users").where('phoneNumber', '==', phoneNumber).get().then(async function (snapshots) {
                        if (snapshots.docs.length > 0) {
                            $("#field_error1").html("{{trans('lang.account_exists_with_number')}}").show();
                            return false;
                        } else {
                            $('#hidden_fName').val(firstName);
                            $('#hidden_lName').val(lastName);
                            $('#hidden_referral').val(referral);
                            
                            // Original code set hidden email here, but where from?
                            // If email div is hidden, val is empty.
                            $("#hidden_email").val(email); 

                            $("#hidden_countrycode").val(jQuery("#country_selector").val());
                            $("#hidden_phone").val(jQuery("#mobileNumber").val());
                            
                            firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                                .then(function (confirmationResult) {
                                    window.confirmationResult = confirmationResult;
                                    if (confirmationResult.verificationId) {
                                        $('#firstName_div').hide();
                                        $('#lastName_div').hide();
                                        $('#email_div').hide();
                                        $('#pass_div').hide();
                                        $('#phone-box').hide();
                                        $('#referral_div').hide();
                                        $('#btn-signup-phone').hide();
                                        $('#btn-sign-up').hide();
                                        $('#send-code').hide(); // Hide send code button
                                        
                                        jQuery("#recaptcha-container").hide();
                                        jQuery("#otp-box").show();
                                        $('#verificationcode').attr('required', 'true');
                                        jQuery("#verify_btn").show();
                                    }
                                }).catch((error) => {
                                console.error("Error writing document: ", error);
                                $("#field_error").html(error.message).show();
                                window.scrollTo(0, 0);
                            });
                        }
                    })
            }
        }

        function applicationVerifier() {
            var code = $('#verificationcode').val();
            if (code == "") {
                $('.otp_error').html('Please Enter OTP').show();
            } else {
                window.confirmationResult.confirm(document.getElementById("verificationcode").value)
                    .then(async function (result) {
                        var countrycode =$("#hidden_countrycode").val();
                        var phone = $("#hidden_phone").val();
                        var mobileNumber = result.user.phoneNumber;
                        var firstName = $('#hidden_fName').val();
                        var lastName = $('#hidden_lName').val();
                        var email = $("#hidden_email").val();
                        var password = "";
                        var referralCode = $('#hidden_referral').val();
                        var referralBy = '';
                        if (referralCode) {
                            var referralByRes = getReferralUserId(referralCode);
                            var referralBy = await referralByRes.then(function (refUserId) {
                                return refUserId;
                            });
                        }
                        var userReferralCode = Math.floor(Math.random() * 899999 + 100000);
                        userReferralCode = userReferralCode.toString();
                        var uuid = result.user.uid;
                        
                        database.collection("referral").doc(uuid).set({
                            'id': uuid,
                            'referralBy': referralBy ? referralBy : '',
                            'referralCode': userReferralCode,
                        });
                        
                        database.collection("users").doc(uuid).set({
                            'appIdentifier':"web",
                            'email': email,
                            'firstName': firstName,
                            'lastName': lastName,
                            'id': uuid,
                            'countryCode' : countrycode,
                            'phoneNumber': phone,
                            'role': "customer",
                            'profilePictureURL': "",
                            'provider':'phone',
                            'createdAt': createdAtman,
                            'active':true
                        }).then(() => {
                            var url = "{{route('newRegister')}}";
                            $.ajax({
                                type: 'POST',
                                url: url,
                                data: {
                                    userId: uuid,
                                    email: mobileNumber,
                                    password: password,
                                    firstName: firstName,
                                    lastName: lastName
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (data) {
                                    if (data.access) {
                                        window.location = "{{url('/')}}";
                                    }
                                }
                            })
                        }).catch((error) => {
                            $("#field_error").html(error.message).show();
                            window.scrollTo(0, 0);
                        });
                    }).catch((error) => {
                        $('.otp_error').html("Invalid OTP. Please try again.").show();
                    });
            }
        }
    </script>
</body>
</html>