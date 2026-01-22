<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restaurant Registration - Dooeats</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    <link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body class="auth-body">
    <div class="auth-overlay"></div>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 550px;">
            <div class="auth-logo">
                <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats Logo">
            </div>

            <div class="auth-tabs">
                <a href="{{ url('/signup') }}" class="auth-tab-link">Customer</a>
                <a href="{{ route('register') }}" class="auth-tab-link active">Restaurant</a>
            </div>

            <h4>Partner with us</h4>
            <span class="auth-subtitle">Grow your business by joining the Dooeats network.</span>
            
            <div id="error_message" class="alert-danger" style="display:none;"></div>
            <div id="success_message" class="alert alert-success" style="display:none; background: #ECFDF5; color: #065F46; padding: 1rem; border-radius: 16px; margin-bottom: 1.5rem; font-size: 14px;"></div>

            <form id="signup-form" autocomplete="off">
                <div style="display: flex; gap: 15px; margin-bottom: 1.25rem;">
                    <div class="form-group-auth" style="flex: 1; margin-bottom: 0;">
                        <input type="text" id="first-name" class="form-control-auth" placeholder="First Name" required>
                    </div>
                    <div class="form-group-auth" style="flex: 1; margin-bottom: 0;">
                        <input type="text" id="last-name" class="form-control-auth" placeholder="Last Name" required>
                    </div>
                </div>

                <div class="form-group-auth">
                    <input type="email" id="email" class="form-control-auth" placeholder="Email Address" required>
                </div>

                <div class="form-group-auth">
                    <div class="phone-input-group">
                        <div class="phone-prefix">
                            <img src="{{ asset('flags/120/ng.png') }}" alt="Nigeria" width="20">
                            <span>+234</span>
                        </div>
                        <input type="tel" id="getphone" class="form-control-auth" placeholder="Phone Number" required maxlength="11">
                        <input type="hidden" id="phone">
                    </div>
                </div>

                <div class="form-group-auth">
                    <div class="password-input-group">
                        <input type="password" id="password" class="form-control-auth" placeholder="Password (Min 6 chars)" required minlength="6">
                        <div class="password-toggle-icon" onclick="togglePassword('password', 'password-icon')">
                            <i class="fa fa-eye" id="password-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group-auth">
                    <div class="password-input-group">
                        <input type="password" id="confirm-password" class="form-control-auth" placeholder="Confirm Password" required minlength="6">
                        <div class="password-toggle-icon" onclick="togglePassword('confirm-password', 'confirm-password-icon')">
                            <i class="fa fa-eye" id="confirm-password-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="remember-me-group" style="justify-content: flex-start; gap: 10px;">
                    <label class="custom-switch-auth">
                        <input type="checkbox" id="terms" required>
                        <span class="slider-auth"></span>
                        I agree to the <a href="{{ url('/terms') }}" class="forgot-password-link">Terms & Conditions</a> and <a href="{{ url('/privacy') }}" class="forgot-password-link">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn-auth-primary" id="signup-btn">
                    <span>Register Restaurant</span>
                </button>

                <div class="text-center mt-4">
                    <span style="color:var(--text-secondary); font-size:14px;">Already have an account? </span>
                    <a href="{{ route('login') }}" class="forgot-password-link">Sign In</a>
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

        function togglePassword(inputId, iconId) {
            var x = document.getElementById(inputId);
            var icon = document.getElementById(iconId);
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

        $('#signup-form').on('submit', async function(e) {
            e.preventDefault();
            const email = $("#email").val();
            const password = $("#password").val();
            const confirm_password = $("#confirm-password").val();
            const firstName = $("#first-name").val();
            const lastName = $("#last-name").val();
            
            // Format phone number with Nigerian code
            let rawPhone = $("#getphone").val();
            // Remove leading zero if present
            if(rawPhone.startsWith('0')) {
                rawPhone = rawPhone.substring(1);
            }
            const phone = "+234" + rawPhone;
            $("#phone").val(phone);

            const $btn = $('#signup-btn');

            if (password !== confirm_password) {
                $('#error_message').text("Passwords do not match.").show();
                return;
            }

            $btn.prop('disabled', true).find('span').text('Registering...');
            $('#error_message').hide();

            try {
                // Check auto-approve setting
                const snapshots = await database.collection('settings').doc("restaurant").get();
                var restaurantSettingdata = snapshots.data();
                var restaurant_active = restaurantSettingdata && restaurantSettingdata.auto_approve_restaurant === true;

                // Create Firebase user
                const firebaseUser = await firebase.auth().createUserWithEmailAndPassword(email, password);
                var user_id = firebaseUser.user.uid;

                // Create user document in Firestore
                await database.collection('users').doc(user_id).set({
                    'appIdentifier': "web",
                    'isDocumentVerify': false,
                    'firstName': firstName,
                    'lastName': lastName,
                    'email': email,
                    'phoneNumber': phone,
                    'role': 'vendor',
                    'id': user_id,
                    'active': restaurant_active,
                    'createdAt': firebase.firestore.FieldValue.serverTimestamp(),
                    'provider': "email"
                });

                // Success handling
                if (restaurant_active) {
                        // Set Laravel Token
                    await $.ajax({
                        type: 'POST',
                        url: "{{ route('setToken') }}",
                        data: { id: user_id, email: email, password: password, isSubscribed: '' },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    });
                    window.location.href = "{{ route('subscription-plan.show') }}";
                } else {
                    $('#success_message').text("Registration successful! Your account is waiting for admin approval.").show();
                    $btn.hide();
                }

            } catch (error) {
                $('#error_message').text(error.message).show();
                $btn.prop('disabled', false).find('span').text('Register Restaurant');
            }
        });
    </script>
</body>
</html>
