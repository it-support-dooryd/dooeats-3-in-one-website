<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - Dooeats</title>
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

            <h4>Join Us Today</h4>
            <span class="auth-subtitle">Create an account to start ordering your favorite food.</span>
            
            <div id="error_message" class="alert-danger" style="display:none;"></div>

            <form id="signup-form" autocomplete="off">
                <div style="display: flex; gap: 15px; margin-bottom: 1.25rem;">
                    <div class="form-group-auth" style="flex: 1; margin-bottom: 0;">
                        <input type="text" id="firstName" class="form-control-auth" placeholder="First Name" required>
                    </div>
                    <div class="form-group-auth" style="flex: 1; margin-bottom: 0;">
                        <input type="text" id="lastName" class="form-control-auth" placeholder="Last Name" required>
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
                        <input type="password" id="confirm_password" class="form-control-auth" placeholder="Confirm Password" required minlength="6">
                        <div class="password-toggle-icon" onclick="togglePassword('confirm_password', 'confirm-password-icon')">
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

                <button type="submit" class="btn-auth-primary" id="btn-signup">
                    <span>Create Account</span>
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
            const confirm_password = $("#confirm_password").val();
            const firstName = $("#firstName").val();
            const lastName = $("#lastName").val();
            
            // Format phone
            let rawPhone = $("#getphone").val();
            if(rawPhone.startsWith('0')) {
                rawPhone = rawPhone.substring(1);
            }
            const phone = "+234" + rawPhone;
            
            const $btn = $('#btn-signup');

            if (password !== confirm_password) {
                $('#error_message').text("Passwords do not match.").show();
                return;
            }

            $btn.prop('disabled', true).find('span').text('Creating Account...');
            $('#error_message').hide();

            try {
                // Create User in Firebase
                const userCredential = await firebase.auth().createUserWithEmailAndPassword(email, password);
                const uuid = userCredential.user.uid;

                // Save to Firestore
                await database.collection("users").doc(uuid).set({
                    firstName: firstName,
                    lastName: lastName,
                    email: email,
                    phoneNumber: rawPhone,
                    countryCode: '+234',
                    role: "customer",
                    id: uuid,
                    active: true,
                    createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                    appIdentifier: "web"
                });

                // Create Laravel Session
                const sessionResponse = await $.ajax({
                    type: 'POST',
                    url: "{{ route('newRegister') }}",
                    data: { userId: uuid, email: email, password: password, firstName: firstName, lastName: lastName },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                });

                if (sessionResponse.access) {
                    window.location.href = "{{ url('/') }}";
                } else {
                    throw new Error("Account created but session failed.");
                }

            } catch (error) {
                $('#error_message').text(error.message).show();
                $btn.prop('disabled', false).find('span').text('Create Account');
            }
        });
    </script>
</body>
</html>
