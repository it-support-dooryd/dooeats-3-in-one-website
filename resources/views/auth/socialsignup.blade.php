<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Dooeats') }} - Complete Profile</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    <link href="{{ asset('css/auth-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body class="auth-body">
    <div class="auth-overlay"></div>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 550px;">
            <h4>Complete Profile</h4>
            <span class="auth-subtitle">Please complete your profile to continue</span>
            
            <div id="error_message" class="alert-danger" style="display:none;"></div>

            <form id="social-signup-form" autocomplete="off">
                <div style="display: flex; gap: 15px; margin-bottom: 1.25rem;">
                    <div class="form-group-auth" style="flex: 1; margin-bottom: 0;">
                        <i class="fa fa-user-o input-icon"></i>
                        <input type="text" id="firstName" class="form-control-auth" placeholder="First Name" value="{{ old('firstName', $firstName) }}" required>
                    </div>
                    <div class="form-group-auth" style="flex: 1; margin-bottom: 0;">
                        <i class="fa fa-user-o input-icon"></i>
                        <input type="text" id="lastName" class="form-control-auth" placeholder="Last Name" value="{{ old('lastName', $lastName) }}" required>
                    </div>
                </div>

                <div class="form-group-auth">
                    <i class="fa fa-envelope-o input-icon"></i>
                    <input type="email" id="email" class="form-control-auth" placeholder="Email Address" value="{{ old('email', $email) }}" disabled required style="background-color: #f3f4f6; cursor: not-allowed;">
                </div>

                <div class="form-group-auth">
                    <div class="phone-input-group">
                        <div class="phone-prefix" style="left: 1rem; color: #102A1C; font-weight: 700;">
                            <img src="{{ asset('flags/120/ng.png') }}" alt="Nigeria" width="18">
                            <span style="font-size: 14px; margin-left: 4px;">+234</span>
                        </div>
                        <input type="tel" id="getphone" class="form-control-auth" placeholder="Phone Number" value="{{ old('phoneNumber', $phoneNumber ?? '') }}" required maxlength="11" style="padding-left: 5.5rem;">
                    </div>
                </div>

                <div class="form-group-auth">
                    <i class="fa fa-gift input-icon"></i>
                    <input type="text" id="referral_code" class="form-control-auth" placeholder="Referral Code (Optional)">
                </div>

                <div class="text-center mb-3">
                    <small style="color: var(--text-secondary);">
                        By continuing, you agree to our <a href="{{ url('/terms') }}" class="forgot-password-link">Terms</a> & <a href="{{ url('/privacy') }}" class="forgot-password-link">Privacy Policy</a>.
                    </small>
                </div>

                <button type="submit" class="btn-auth-primary" id="btn-signup">
                    <span>Proceed</span>
                    <div class="arrow-box">
                        <i class="fa fa-arrow-right"></i>
                    </div>
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

        // Helper to set cookie
        function setCookie(cname, cvalue, exdays) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        async function getReferralUserId(referralCode) {
            try {
                const snapshots = await database.collection('referral').where('referralCode', '==', referralCode).get();
                if (snapshots.docs.length > 0) {
                    return snapshots.docs[0].data().id;
                }
            } catch (e) {
                console.error("Referral check error", e);
            }
            return '';
        }

        $('#social-signup-form').on('submit', async function(e) {
            e.preventDefault();
            const $btn = $('#btn-signup');
            const firstName = $("#firstName").val();
            const lastName = $("#lastName").val();
            const email = $("#email").val();
            const referralCode = $("#referral_code").val();
            
            // Format phone
            let rawPhone = $("#getphone").val();
            if(rawPhone.startsWith('0')) {
                rawPhone = rawPhone.substring(1);
            }
            const phone = "+234" + rawPhone;

            $btn.prop('disabled', true).find('span').text('Processing...');
            $('#error_message').hide();

            try {
                // Handle Referral
                let referralBy = '';
                if (referralCode) {
                    referralBy = await getReferralUserId(referralCode);
                }
                
                const userReferralCode = Math.floor(Math.random() * 899999 + 100000).toString();
                const uuid = "{{$uuid}}"; // Passed from controller
                const photourl = "{{$photoURL}}"; // Passed from controller
                const createdAt = firebase.firestore.Timestamp.now();

                // Update/Set Referral Doc
                await database.collection("referral").doc(uuid).set({
                    'id': uuid,
                    'referralBy': referralBy,
                    'referralCode': userReferralCode,
                });

                // Update/Set User Doc
                await database.collection("users").doc(uuid).set({
                    'appIdentifier': "web",
                    'email': email,
                    'firstName': firstName,
                    'lastName': lastName,
                    'id': uuid,
                    'countryCode': '+234',
                    'phoneNumber': rawPhone, 
                    'countryCode': '+234',
                    'phoneNumber': rawPhone, 
                    'role': "customer",
                    'profilePictureURL': photourl,
                    'provider': 'google', // Assumption: social signup implies google/apple etc.
                    'createdAt': createdAt,
                    'active': true
                });

                // Create Session
                const sessionResponse = await $.ajax({
                    type: 'POST',
                    url: "{{ route('newRegister') }}", // Using newRegister logic for session creation
                    data: {
                        userId: uuid,
                        email: email,
                        password: '',
                        firstName: firstName,
                        lastName: lastName
                    },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                });

                if (sessionResponse.access) {
                    setCookie("loginType", "Social", 1);
                    window.location.href = "{{ url('/') }}";
                } else {
                    throw new Error("Session creation failed.");
                }

            } catch (error) {
                $('#error_message').text(error.message).show();
                $btn.prop('disabled', false).find('span').text('Complete Registration');
            }
        });
    </script>
</body>
</html>
