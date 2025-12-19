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
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    
    <style>
        /* Custom theme color overrides from cookies */
        <?php if (isset($_COOKIE['store_panel_color'])) { ?>
        :root {
            --primary-color: <?php echo $_COOKIE['store_panel_color']; ?>;
            --primary-gradient: linear-gradient(135deg, <?php echo $_COOKIE['store_panel_color']; ?> 0%, <?php echo $_COOKIE['store_panel_color']; ?> 100%);
        }
        <?php } ?>
    </style>
</head>
<body>
    <?php
    // Load countries data for phone authentication
    $countries = file_get_contents(public_path('countriesdata.json'));
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
                <div class="auth-tabs show-tabs">
                    <button class="auth-tab active" data-tab="restaurant">
                        Restaurant Signup
                    </button>
                    <a href="http://127.0.0.1:8000/signup" class="auth-tab" data-tab="customer">
                        Customer Signup
                    </a>
                </div>

                <!-- Header -->
                <div class="auth-header" style="text-align: center;">
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
                        <label class="form-label">{{ trans('lang.first_name') }}</label>
                        <div class="form-input-wrapper">
                            <input type="text" id="first-name" name="first_name" class="form-input" placeholder="Enter your first name" required>
                        </div>
                        <div class="error-message" id="first-name-error"></div>
                    </div>

                    <!-- Last Name -->
                    <div class="form-group">
                        <label class="form-label">{{ trans('lang.last_name') }}</label>
                        <div class="form-input-wrapper">
                            <input type="text" id="last-name" name="last_name" class="form-input" placeholder="Enter your last name" required>
                        </div>
                        <div class="error-message" id="last-name-error"></div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">{{ trans('lang.email') }}</label>
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
                                    +<?php echo $valuecy->phoneCode; ?> {{ $valuecy->countryName }}
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label class="form-label">{{ trans('lang.user_phone') }}</label>
                        <div class="form-input-wrapper">
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="Enter phone number" required>
                        </div>
                        <div class="error-message" id="phone-error"></div>
                    </div>

                    <!-- Password with Toggle -->
                    <div class="form-group">
                        <label class="form-label">{{ trans('lang.password') }}</label>
                        <div class="form-input-wrapper">
                            <input type="password" id="password" name="password" class="form-input" placeholder="Create a password" required>
                            <!-- Show/Hide Password Toggle -->
                            <svg class="password-toggle" id="toggle-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div class="error-message" id="password-error"></div>
                    </div>

                    <!-- Confirm Password with Toggle -->
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <div class="form-input-wrapper">
                            <input type="password" id="confirm-password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>
                            <!-- Show/Hide Password Toggle -->
                            <svg class="password-toggle" id="toggle-confirm-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div class="error-message" id="confirm-password-error"></div>
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
                        <span id="signup-btn-text" style="flex: 1; text-align: center;">SIGN UP</span>
                    </button>

                    <!-- Social Auth Buttons -->
                    <div class="social-auth-buttons" style="margin-top: 1.5rem;">
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
                    Already have an account? <a href="{{ route('login') }}" class="auth-link">Login</a>
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
    <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
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
        var createdAt = firebase.firestore.Timestamp.fromDate(new Date());
        var autoAprroveRestaurant = database.collection('settings').doc("restaurant");
        var adminEmail = '';
        var emailSetting = database.collection('settings').doc('emailSetting');
        var email_templates = database.collection('email_templates').where('type', '==', 'new_vendor_signup');
        var emailTemplatesData = null;

        var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
        newcountriesjs = JSON.parse(newcountriesjs);

        // ============================================
        // INITIALIZE
        // ============================================
        $(document).ready(async function() {
            // Initialize Select2 for country selector
            $("#country-selector").select2({
                templateResult: formatState,
                templateSelection: formatState2,
                placeholder: "Select Country",
                allowClear: true
            });

            // Load email templates
            await email_templates.get().then(async function(snapshots) {
                if (snapshots.docs.length > 0) {
                    emailTemplatesData = snapshots.docs[0].data();
                }
            });

            // Load admin email
            await emailSetting.get().then(async function(snapshots) {
                var emailSettingData = snapshots.data();
                if (emailSettingData) {
                    adminEmail = emailSettingData.userName;
                }
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
            togglePasswordVisibility('#password', $(this));
        });

        $('#toggle-confirm-password').click(function() {
            togglePasswordVisibility('#confirm-password', $(this));
        });

        function togglePasswordVisibility(inputId, iconElement) {
            const passwordInput = $(inputId);
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            
            // Toggle icon
            if (type === 'text') {
                iconElement.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />');
            } else {
                iconElement.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />');
            }
        }

        // ============================================
        // FORM VALIDATION & SUBMISSION
        // ============================================
        $('#signup-form').on('submit', function(e) {
            e.preventDefault();
            createRestaurantAccount();
        });

        async function createRestaurantAccount() {
            // Get form values
            var userFirstName = $('#first-name').val().trim();
            var userLastName = $('#last-name').val().trim();
            var email = $('#email').val().trim().toLowerCase();
            var password = $('#password').val();
            var confirmPassword = $('#confirm-password').val();
            var countryCode = '+' + $('#country-selector').val();
            var userPhone = $('#phone').val().trim();
            var termsAccepted = $('#terms').is(':checked');

            // Clear previous errors
            $('.error-message').removeClass('show').html('');
            $('#error-message').addClass('hidden');
            $('#success-message').addClass('hidden');

            // Validation
            var hasError = false;
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (userFirstName == '') {
                $('#first-name-error').addClass('show').html('Please enter your first name');
                hasError = true;
            }

            if (userLastName == '') {
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

            if (userPhone == '') {
                $('#phone-error').addClass('show').html('Please enter your phone number');
                hasError = true;
            }

            if (password == '') {
                $('#password-error').addClass('show').html('Please enter a password');
                hasError = true;
            } else if (password.length < 6) {
                $('#password-error').addClass('show').html('Password must be at least 6 characters');
                hasError = true;
            }

            if (confirmPassword == '') {
                $('#confirm-password-error').addClass('show').html('Please confirm your password');
                hasError = true;
            } else if (password !== confirmPassword) {
                $('#confirm-password-error').addClass('show').html('Passwords do not match');
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
                // Check auto-approve setting
                const snapshots = await autoAprroveRestaurant.get();
                var restaurantSettingdata = snapshots.data();
                var restaurant_active = restaurantSettingdata && restaurantSettingdata.auto_approve_restaurant === true;

                // Create Firebase user
                const firebaseUser = await firebase.auth().createUserWithEmailAndPassword(email, password);
                var user_id = firebaseUser.user.uid;

                // Create user document in Firestore
                await database.collection('users').doc(user_id).set({
                    'appIdentifier': "web",
                    'isDocumentVerify': false,
                    'firstName': userFirstName,
                    'lastName': userLastName,
                    'email': email,
                    'countryCode': countryCode,
                    'phoneNumber': userPhone,
                    'role': 'vendor',
                    'id': user_id,
                    'active': restaurant_active,
                    'createdAt': createdAt,
                    'provider': "email"
                });

                // Send email notification
                if (emailTemplatesData && adminEmail) {
                    var formattedDate = new Date();
                    var month = formattedDate.getMonth() + 1;
                    var day = formattedDate.getDate();
                    var year = formattedDate.getFullYear();
                    month = month < 10 ? '0' + month : month;
                    day = day < 10 ? '0' + day : day;
                    formattedDate = day + '-' + month + '-' + year;

                    var message = emailTemplatesData.message;
                    message = message.replace(/{userid}/g, user_id);
                    message = message.replace(/{username}/g, userFirstName + ' ' + userLastName);
                    message = message.replace(/{useremail}/g, email);
                    message = message.replace(/{userphone}/g, userPhone);
                    message = message.replace(/{date}/g, formattedDate);

                    await sendEmail(
                        "{{ url('send-email') }}",
                        emailTemplatesData.subject,
                        message,
                        [adminEmail]
                    );
                }

                // Hide loading
                $('#loading-overlay').addClass('hidden');
                $('#signup-btn').prop('disabled', false);

                // Show success message and redirect
                if (restaurant_active) {
                    $('#success-text').html('{{ trans("lang.thank_you_signup_msg") }}');
                    $('#success-message').removeClass('hidden');
                    setTimeout(function() {
                        window.location.href = "{{ route('subscription-plan.show') }}";
                    }, 3000);
                } else {
                    $('#success-text').html('{{ trans("lang.signup_waiting_approval") }}');
                    $('#success-message').removeClass('hidden');
                    setTimeout(function() {
                        window.location.href = "{{ route('login') }}";
                    }, 3000);
                }

            } catch (error) {
                $('#loading-overlay').addClass('hidden');
                $('#signup-btn').prop('disabled', false);
                $('#error-text').html(error.message || 'An error occurred during signup');
                $('#error-message').removeClass('hidden');
                window.scrollTo(0, 0);
            }
        }

        async function sendEmail(url, subject, message, recipients) {
            try {
                await $.ajax({
                    type: 'POST',
                    data: {
                        subject: subject,
                        message: message,
                        recipients: recipients
                    },
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                return true;
            } catch (error) {
                console.error('Email send error:', error);
                return false;
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
        // APPLE SIGNUP (Placeholder)
        // ============================================
        $('#apple-signup-btn').click(function() {
            alert("Apple Sign-Up will be implemented based on your Apple Developer configuration.");
        });

        async function handleSocialSignup(user, provider) {
            $('#loading-overlay').removeClass('hidden');

            try {
                // Check if user already exists
                const userDoc = await database.collection("users").doc(user.uid).get();
                
                if (userDoc.exists) {
                    var userData = userDoc.data();
                    if (userData.role === 'vendor') {
                        // User already exists, redirect to login
                        alert('Account already exists. Redirecting to login...');
                        window.location.href = "{{ route('login') }}";
                        return;
                    }
                }

                // Create new vendor account
                var phoneNumber = user.phoneNumber || '';
                var firstName = user.displayName ? user.displayName.split(' ')[0] : '';
                var lastName = user.displayName ? user.displayName.split(' ').slice(1).join(' ') : '';
                var email = user.email || '';
                var photoURL = user.photoURL || '';

                // Check auto-approve setting
                const snapshots = await autoAprroveRestaurant.get();
                var restaurantSettingdata = snapshots.data();
                var restaurant_active = restaurantSettingdata && restaurantSettingdata.auto_approve_restaurant === true;

                await database.collection('users').doc(user.uid).set({
                    'appIdentifier': "web",
                    'isDocumentVerify': false,
                    'firstName': firstName,
                    'lastName': lastName,
                    'email': email,
                    'phoneNumber': phoneNumber,
                    'profilePictureURL': photoURL,
                    'role': 'vendor',
                    'id': user.uid,
                    'active': restaurant_active,
                    'createdAt': createdAt,
                    'provider': provider
                });

                $('#loading-overlay').addClass('hidden');

                if (restaurant_active) {
                    window.location.href = "{{ route('subscription-plan.show') }}";
                } else {
                    $('#success-text').html('{{ trans("lang.signup_waiting_approval") }}');
                    $('#success-message').removeClass('hidden');
                    setTimeout(function() {
                        window.location.href = "{{ route('login') }}";
                    }, 3000);
                }

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
    </script>
</body>
</html>
