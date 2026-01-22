<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restaurant Panel - Reset Password</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    <link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body class="auth-body">
    <div class="auth-overlay"></div>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 450px;">
            <div class="auth-logo">
                <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats Logo">
            </div>

            <h4>Reset Password</h4>
            <span class="auth-subtitle">Enter your restaurant email to receive a reset link.</span>
            
            <div id="error_message" class="alert-danger" style="display:none;"></div>
            <div id="success_message" class="alert alert-success" style="display:none; background: #ECFDF5; color: #065F46; padding: 1rem; border-radius: 16px; margin-bottom: 1.5rem; font-size: 14px;"></div>

            <form id="forgot-password-form" autocomplete="off">
                <div class="form-group-auth">
                    <input type="email" id="email" class="form-control-auth" placeholder="Email Address" required>
                </div>

                <button type="submit" class="btn-auth-primary" id="btn-submit">
                    <span>Send Reset Link</span>
                </button>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="forgot-password-link">Back to Login</a>
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

        $('#forgot-password-form').on('submit', async function(e) {
            e.preventDefault();
            const email = $("#email").val();
            const $btn = $('#btn-submit');

            $btn.prop('disabled', true).find('span').text('Sending...');
            $('#error_message').hide();
            $('#success_message').hide();

            try {
                // Check if restaurant user exists in Firestore
                const snapshots = await database.collection("users").where("email", "==", email).where('role', '==', 'vendor').get();
                
                if (snapshots.empty) {
                    throw new Error("No restaurant account found with this email.");
                }

                // Send Reset Email
                await firebase.auth().sendPasswordResetEmail(email);
                
                $('#success_message').text("Password reset link sent to your email.").show();
                $btn.prop('disabled', false).find('span').text('Send Reset Link');
                
            } catch (error) {
                $('#error_message').text(error.message).show();
                $btn.prop('disabled', false).find('span').text('Send Reset Link');
            }
        });
    </script>
</body>
</html>
