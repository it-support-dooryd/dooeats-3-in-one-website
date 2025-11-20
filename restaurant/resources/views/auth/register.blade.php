@include('auth.default')
<?php
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
<div class="container">
    <div class="row page-titles ">
        <div class="col-md-12 align-self-center text-center">
            <h3 class="text-themecolor  ">{{ trans('lang.sign_up_with_us') }}</h3>
        </div>
        <div class="card-body">
            <div id="data-table_processing" class="page-overlay" style="display:none;">
                <div class="overlay-text">
                    <img src="{{asset('images/spinner.gif')}}">
                </div>
            </div>
            <div class="error_top"></div>
            <div class="alert alert-success" style="display:none;"></div>
            <div class="row restaurant_payout_create">
                <div class="restaurant_payout_create-inner">
                    <fieldset class="form-material">
                        <legend>{{ trans('lang.owner_details') }}</legend>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.first_name') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_first_name"
                                    placeholder='{{ trans('lang.user_first_name_help') }}' required>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.last_name') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_last_name"
                                    placeholder='{{ trans('lang.user_last_name_help') }}'>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.email') }}</label>
                            <div class="col-7">
                                <input type="email" class="form-control user_email"
                                    placeholder='{{ trans('lang.user_email_help') }}' required>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.password') }}</label>
                            <div class="col-7">
                                <input type="password" class="form-control user_password"
                                    placeholder='{{ trans('lang.user_password_help') }}' required>
                            </div>
                        </div>
                        <div class="form-group form-material">
                            <label class="col-3 control-label">{{ trans('lang.user_phone') }}</label>
                            <div class="col-12">
                                <div class="phone-box position-relative" id="phone-box">
                                    <select name="country" id="country_selector">
                                        <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                        <?php    $selected = ''; ?>
                                        <option <?php    echo $selected; ?> code="<?php    echo $valuecy->code; ?>"
                                            value="<?php    echo $keycy; ?>">
                                            +<?php    echo $valuecy->phoneCode; ?> {{ $valuecy->countryName }}</option>
                                        <?php } ?>
                                    </select>
                                    <input class="form-control user_phone" placeholder="Phone" id="phone"
                                        type="phone" name="phone" value="{{ old('phone') }}" required
                                        autocomplete="phone" autofocus>
                                    <div id="error2" class="err"></div>
                                </div>
                            </div>
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <div class="form-group col-12 text-center">
            <button type="button" class="btn btn-primary  create_restaurant_btn"><i class="fa fa-save"></i>
                {{ trans('lang.save') }}
            </button>
            <div class="or-line mb-4 ">
                <span>OR</span>
            </div>
            <a href="{{ route('login') }}">
                <p class="text-center m-0"> {{ trans('lang.already_an_account') }} {{ trans('lang.sign_in') }}</p>
            </a>
        </div>
    </div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.1.1/compressor.min.js"
    integrity="sha512-VaRptAfSxXFAv+vx33XixtIVT9A/9unb1Q8fp63y1ljF+Sbka+eMJWoDAArdm7jOYuLQHVx5v60TQ+t3EA8weA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script>
    document.querySelector('.user_phone').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    var database = firebase.firestore();
    var geoFirestore = new GeoFirestore(database);
    var storageRef = firebase.storage().ref('images');
    var createdAt = firebase.firestore.Timestamp.fromDate(new Date());
    var autoAprroveRestaurant = database.collection('settings').doc("restaurant");
    var adminEmail = '';
    var emailSetting = database.collection('settings').doc('emailSetting');
    var email_templates = database.collection('email_templates').where('type', '==', 'new_vendor_signup');
    var emailTemplatesData = null;
    var commissionBusinessModel = database.collection('settings').doc("AdminCommission");
    var subscriptionBusinessModel = database.collection('settings').doc("restaurant");
    $(document).ready(async function() {
        jQuery("#data-table_processing").show();
        jQuery("#country_selector").select2({
            templateResult: formatState,
            templateSelection: formatState2,
            placeholder: "Select Country",
            allowClear: true
        });
        await email_templates.get().then(async function(snapshots) {
            emailTemplatesData = snapshots.docs[0].data();
        });
        await emailSetting.get().then(async function(snapshots) {
            var emailSettingData = snapshots.data();
            adminEmail = emailSettingData.userName;
        });
        jQuery("#data-table_processing").hide();
    });
    $(".create_restaurant_btn").click(async function() {
        $(".error_top").hide();
        var userFirstName = $(".user_first_name").val();
        var userLastName = $(".user_last_name").val();
        var email = $(".user_email").val();
        email = email.toLowerCase();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var password = $(".user_password").val();
        var countryCode = '+' + jQuery("#country_selector").val();
        var userPhone = $(".user_phone").val();
        var restaurant_active = false;
        const snapshots = await autoAprroveRestaurant.get();
        var restaurantSettingdata = snapshots.data();
        if (restaurantSettingdata.auto_approve_restaurant === true) {
            restaurant_active = true;
        }
        var user_id = "<?php echo uniqid(); ?>";
        var name = userFirstName + " " + userLastName;
        if (userFirstName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{ trans('lang.enter_owners_name_error') }}</p>");
            window.scrollTo(0, 0);
        } else if (userLastName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{ trans('lang.enter_owners_last_name_error') }}</p>");
            window.scrollTo(0, 0);
        } else if (email == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{ trans('lang.enter_owners_email') }}</p>");
            window.scrollTo(0, 0);
        } else if (!emailRegex.test(email)) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{ trans('lang.enter_owners_email_error') }}</p>");
            window.scrollTo(0, 0);
        } else if (password == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{ trans('lang.enter_owners_password_error') }}</p>");
            window.scrollTo(0, 0);
        } else if (userPhone == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{ trans('lang.enter_owners_phone') }}</p>");
            window.scrollTo(0, 0);
        } else {
            jQuery("#data-table_processing").show();
            firebase.auth().createUserWithEmailAndPassword(email, password)
                .then(async function(firebaseUser) {
                    user_id = firebaseUser.user.uid;
                    database.collection('users').doc(user_id).set({
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
                        'provider':"email"
                    }).then(function(result) {
                        autoAprroveRestaurant.get().then(async function(snapshots) {
                            var formattedDate = new Date();
                            var month = formattedDate.getMonth() + 1;
                            var day = formattedDate.getDate();
                            var year = formattedDate.getFullYear();
                            month = month < 10 ? '0' + month : month;
                            day = day < 10 ? '0' + day : day;
                            formattedDate = day + '-' + month + '-' + year;
                            var message = emailTemplatesData.message;
                            message = message.replace(/{userid}/g, user_id);
                            message = message.replace(/{username}/g,
                                userFirstName + ' ' + userLastName);
                            message = message.replace(/{useremail}/g, email);
                            message = message.replace(/{userphone}/g,
                                userPhone);
                            message = message.replace(/{date}/g, formattedDate);
                            emailTemplatesData.message = message;
                            var url = "{{ url('send-email') }}";
                            var sendEmailStatus = await sendEmail(url,
                                emailTemplatesData.subject,
                                emailTemplatesData.message, [adminEmail]);
                            if (sendEmailStatus) {
                                var restaurantdata = snapshots.data();
                                if (restaurantdata.auto_approve_restaurant ==
                                    false) {
                                    $(".alert-success").show();
                                    $(".alert-success").html("");
                                    $(".alert-success").append(
                                        "<p>{{ trans('lang.signup_waiting_approval') }}</p>"
                                    );
                                    window.scrollTo(0, 0);
                                    setTimeout(function() {
                                        window.location.href =
                                            '{{ route('login') }}';
                                    }, 5000);
                                } else {
                                    $(".alert-success").show();
                                    $(".alert-success").html("");
                                    $(".alert-success").append(
                                        "<p>{{ trans('lang.thank_you_signup_msg') }}</p>"
                                    );
                                    window.scrollTo(0, 0);
                                    setTimeout(function() {
                                        window.location.href ="{{ route('subscription-plan.show') }}";
                                    }, 5000);
                                }
                            }
                        });
                    }).catch(err => {
                        jQuery("#data-table_processing").hide();
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>" + err + "</p>");
                        window.scrollTo(0, 0);
                    });
                }).catch(function(error) {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + error + "</p>");
                    window.scrollTo(0, 0);
                });
        }
    })
    async function sendEmail(url, subject, message, recipients) {
        var checkFlag = false;
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
            },
            success: function(data) {
                checkFlag = true;
            },
            error: function(xhr, status, error) {
                checkFlag = true;
            }
        });
        return checkFlag;
    }
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";
        var $state = $(
            '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() +
            '.png" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    }
    function formatState2(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/"
        var $state = $(
            '<span><img class="img-flag" /> <span></span></span>'
        );
        $state.find("span").text(state.text);
        $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".png");
        return $state;
    }
    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);
    $('#phone').on('keypress', function(event) {
        if (!(event.which >= 48 && event.which <= 57)) {
            document.getElementById('error2').innerHTML = "Accept only Number";
            return false;
        } else {
            document.getElementById('error2').innerHTML = "";
            return true;
        }
    });
</script>
