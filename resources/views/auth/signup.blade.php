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
            
            <!-- Left Panel: Signup Form -->
            <div class="auth-form-panel">
                
                <!-- Tab Switcher: Restaurant / Customer -->
                <div class="auth-tabs">
                    <a href="http://127.0.0.1:8001/register" class="auth-tab" data-tab="restaurant">
                        Restaurant Signup
                    </a>
                    <button class="auth-tab active" data-tab="customer">
                        Customer Signup
                    </button>
                </div>

                <!-- Logo -->
                <div class="auth-header" style="text-align: center;">
                    <div class="auth-logo">
                        <h1 class="auth-logo-text">Dooeats</h1>
                    </div>
                    <h1 class="auth-title">{{trans('lang.sign_up_with_us')}}</h1>
                    <p class="auth-subtitle">{{trans('lang.sign_up_to_continue')}}</p>
                </div>

                <!-- Success/Error Messages -->
                <div id="success-message" class="hidden" style="background: #d1fae5; border: 1px solid #10b981; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <p style="color: #047857; margin: 0; font-size: 0.875rem;" id="success-text"></p>
                </div>
                
                <div id="error-message" class="hidden" style="background: #fee2e2; border: 1px solid #dc2626; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <p style="color: #dc2626; margin: 0; font-size: 0.875rem;" id="error-text"></p>
                </div>

                <!-- Signup Form -->
                <form id="signup-form" class="auth-form">
                    @csrf
                    
                    <!-- First Name -->
                    <div class="form-group">
                        <label class="form-label">{{trans('lang.first_name')}}</label>
                        <div class="form-input-wrapper">
                            <input type="text" id="first-name" name="first_name" class="form-input" placeholder="Enter your first name" required>
                        </div>
                        <div class="error-message" id="first-name-error"></div>
                    </div>

                    <!-- Last Name -->
                    <div class="form-group">
                        <label class="form-label">{{trans('lang.last_name')}}</label>
                        <div class="form-input-wrapper">
                            <input type="text" id="last-name" name="last_name" class="form-input" placeholder="Enter your last name" required>
                        </div>
                        <div class="error-message" id="last-name-error"></div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">{{trans('lang.email_address')}}</label>
                        <div class="form-input-wrapper">
                            <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
                        </div>
                        <div class="error-message" id="email-error"></div>
                    </div>

                    <!-- Country Selector -->
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

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label class="form-label">{{trans('lang.user_phone')}}</label>
                        <div class="form-input-wrapper">
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="Enter phone number" required>
                        </div>
                        <div class="error-message" id="phone-error"></div>
                    </div>

                    <!-- Password with Toggle -->
                    <div class="form-group">
                        <label class="form-label">{{trans('lang.password')}}</label>
                        <div class="form-input-wrapper">
                            <input type="password" id="password" name="password" class="form-input" placeholder="Create a password (min 8 characters)" minlength="8" required>
                            <!-- Show/Hide Password Toggle -->
                            <svg class="password-toggle" id="toggle-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div class="error-message" id="password-error"></div>
                    </div>

                    <!-- Referral Code (Optional) -->
                    <div class="form-group">
                        <label class="form-label">{{trans('lang.referral_code')}} ({{trans('lang.optional')}})</label>
                        <div class="form-input-wrapper">
                            <input type="text" id="referral-code" name="referral_code" class="form-input" placeholder="Enter referral code (optional)">
                        </div>
                    </div>

                    <!-- Terms & Conditions Checkbox -->
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                            <input type="checkbox" id="terms" name="terms" style="width: 18px; height: 18px; cursor: pointer;" required>
                            <span>I agree to the <a href="#" class="auth-link">Terms & Conditions</a></span>
                        </label>
                        <div class="error-message" id="terms-error"></div>
                    </div>

                    <!-- Signup Button -->
                    <button type="submit" class="btn btn-primary" id="signup-btn">
                        <span id="signup-btn-text">{{trans('lang.sign_up')}}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                        </svg>
                    </button>

                    <!-- Divider -->
                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <!-- Social Auth Buttons -->
                    <div class="social-auth-buttons">
                        <button type="button" class="social-btn" id="google-signup-btn">
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
                        <button type="button" class="social-btn" id="apple-signup-btn">
                            <div class="social-btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                </svg>
                            </div>
                            Apple
                        </button>
                    </div>
                </form>

                <!-- Footer -->
                <div class="auth-footer">
                    {{trans('lang.already_an_account')}} <a href="{{url('login')}}" class="auth-link">{{trans('lang.sign_in')}}</a>
                </div>
            </div>



        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center;">
            <div class="spinner" style="width: 40px; height: 40px; margin: 0 auto 1rem;"></div>
            <p style="color: var(--text-dark); font-weight: 600;">Creating your account...</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
    <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
    <script src="{{ asset('js/crypto-js.js') }}"></script>
    <script src="{{ asset('js/jquery.cookie.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.js') }}"></script>

    <script type="text/javascript">
        var database = firebase.firestore();
        var createdAtman = firebase.firestore.Timestamp.fromDate(new Date());

        var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
        newcountriesjs = JSON.parse(newcountriesjs);

        // ============================================
        // INITIALIZE
        // ============================================
        $(document).ready(function() {
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
        // FORM VALIDATION & SUBMISSION
        // ============================================
        $('#signup-form').on('submit', function(e) {
            e.preventDefault();
            signupClick();
        });

        async function signupClick() {
            var firstName = $('#first-name').val().trim();
            var lastName = $('#last-name').val().trim();
            var email = $('#email').val().trim().toLowerCase();
            var password = $('#password').val();
            var countryCode = '+' + $('#country-selector').val();
            var phone = $('#phone').val().trim();
            var referralCode = $('#referral-code').val().trim();
            var termsAccepted = $('#terms').is(':checked');

            // Clear previous errors
            $('.error-message').removeClass('show').html('');
            $('#error-message').addClass('hidden');
            $('#success-message').addClass('hidden');

            // Validation
            var hasError = false;
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (firstName == '') {
                $('#first-name-error').addClass('show').html('Please enter your first name');
                hasError = true;
            }

            if (lastName == '') {
                $('#last-name-error').addClass('show').html('Please enter your last name');
                hasError = true;
            }

            if (email == '') {
                $('#email-error').addClass('show').html('Please enter your email address');
                hasError = true;
            } else if (!emailRegex.test(email)) {
                $('#email-error').addClass('show').html('Please enter a valid email address');
                hasError = true;
            }

            if (phone == '') {
                $('#phone-error').addClass('show').html('Please enter your phone number');
                hasError = true;
            }

            if (password == '') {
                $('#password-error').addClass('show').html('Please enter a password');
                hasError = true;
            } else if (password.length < 8) {
                $('#password-error').addClass('show').html('Password must be at least 8 characters');
                hasError = true;
            }

            if (!termsAccepted) {
                $('#terms-error').addClass('show').html('You must agree to the Terms & Conditions');
                hasError = true;
            }

            if (hasError) {
                return false;
            }

            // Show loading
            $('#loading-overlay').removeClass('hidden');
            $('#signup-btn').prop('disabled', true);

            try {
                // Get referral user ID if referral code provided
                var referralBy = '';
                if (referralCode) {
                    referralBy = await getReferralUserId(referralCode);
                }

                // Generate user referral code
                var userReferralCode = Math.floor(Math.random() * 899999 + 100000).toString();

                // Create Firebase user
                const userCredential = await firebase.auth().createUserWithEmailAndPassword(email, password);
                var uuid = userCredential.user.uid;

                // Create referral document
                await database.collection("referral").doc(uuid).set({
                    'id': uuid,
                    'referralBy': referralBy || '',
                    'referralCode': userReferralCode,
                });

                // Create user document
                await database.collection("users").doc(uuid).set({
                    'appIdentifier': "web",
                    'email': email,
                    'firstName': firstName,
                    'lastName': lastName,
                    'id': uuid,
                    'countryCode': countryCode,
                    'phoneNumber': phone,
                    'role': "customer",
                    'profilePictureURL': "",
                    'provider': 'email',
                    'createdAt': createdAtman,
                    'active': true
                });

                // Sign in the user
                await firebase.auth().signInWithEmailAndPassword(email, password);

                // Register with backend
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
                    success: function(data) {
                        $('#loading-overlay').addClass('hidden');
                        if (data.access) {
                            window.location = "{{url('/')}}";
                        }
                    },
                    error: function(error) {
                        $('#loading-overlay').addClass('hidden');
                        $('#signup-btn').prop('disabled', false);
                        $('#error-text').html('Registration failed. Please try again.');
                        $('#error-message').removeClass('hidden');
                    }
                });

            } catch (error) {
                $('#loading-overlay').addClass('hidden');
                $('#signup-btn').prop('disabled', false);
                $('#error-text').html(error.message || 'An error occurred during signup');
                $('#error-message').removeClass('hidden');
                window.scrollTo(0, 0);
            }
        }

        async function getReferralUserId(referralCode) {
            try {
                const snapshots = await database.collection('referral').where('referralCode', '==', referralCode).get();
                if (snapshots.docs.length > 0) {
                    var referralData = snapshots.docs[0].data();
                    return referralData.id;
                }
                return '';
            } catch (error) {
                console.error('Error getting referral user:', error);
                return '';
            }
        }

        // ============================================
        // GOOGLE SIGNUP
        // ============================================
        $('#google-signup-btn').click(function() {
            var provider = new firebase.auth.GoogleAuthProvider();
            firebase.auth().signInWithPopup(provider)
                .then(function(result) {
                    var user = result.user;
                    handleSocialSignup(user, 'google');
                }).catch(function(error) {
                    console.error("Google Sign-Up Error:", error.message);
                    $('#error-text').html("Google authentication failed: " + error.message);
                    $('#error-message').removeClass('hidden');
                });
        });

        // ============================================
        // APPLE SIGNUP
        // ============================================
        $('#apple-signup-btn').click(function() {
            alert("Apple Sign-Up will be implemented based on your Apple Developer configuration.");
        });

        async function handleSocialSignup(user, provider) {
            $('#loading-overlay').removeClass('hidden');

            try {
                const userDoc = await database.collection("users").doc(user.uid).get();
                
                if (userDoc.exists) {
                    var userData = userDoc.data();
                    if (userData.role === 'customer') {
                        alert('Account already exists. Redirecting to login...');
                        window.location.href = "{{ url('login') }}";
                        return;
                    }
                }

                var phoneNumber = user.phoneNumber || '';
                var firstName = user.displayName ? user.displayName.split(' ')[0] : '';
                var lastName = user.displayName ? user.displayName.split(' ').slice(1).join(' ') : '';
                var email = user.email || '';
                var photoURL = user.photoURL || '';

                var userReferralCode = Math.floor(Math.random() * 899999 + 100000).toString();

                await database.collection("referral").doc(user.uid).set({
                    'id': user.uid,
                    'referralBy': '',
                    'referralCode': userReferralCode,
                });

                await database.collection('users').doc(user.uid).set({
                    'appIdentifier': "web",
                    'email': email,
                    'firstName': firstName,
                    'lastName': lastName,
                    'phoneNumber': phoneNumber,
                    'profilePictureURL': photoURL,
                    'role': 'customer',
                    'id': user.uid,
                    'active': true,
                    'createdAt': createdAtman,
                    'provider': provider
                });

                var url = "{{route('newRegister')}}";
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        userId: user.uid,
                        email: email,
                        password: '',
                        firstName: firstName,
                        lastName: lastName
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#loading-overlay').addClass('hidden');
                        if (data.access) {
                            window.location = "{{url('/')}}";
                        }
                    }
                });

            } catch (error) {
                $('#loading-overlay').addClass('hidden');
                $('#error-text').html(error.message || 'An error occurred during signup');
                $('#error-message').removeClass('hidden');
            }
        }

        // ============================================
        // PHONE NUMBER VALIDATION
        // ============================================
        $('#phone').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Trim leading spaces from name inputs
        $('#first-name, #last-name').on('input', function() {
            this.value = this.value.trimStart();
        });
    </script>
</body>
</html>