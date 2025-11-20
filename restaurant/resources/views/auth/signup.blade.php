@include('auth.default')
<?php
$countries = file_get_contents(public_path('countriesdata.json'));
$countries = json_decode($countries);
$countries = (array) $countries;
$newcountries = array();
$newcountriesjs = array();
foreach ($countries as $keycountry => $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
    $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
}
$phoneNumber = request('phoneNumber');
$selectedCountryCode = '';
$phone = '';
if ($phoneNumber) {
    foreach ($newcountries as $code => $country) {
        if (strpos($phoneNumber, '+' . $country->phoneCode) === 0) {
            $selectedCountryCode = $code;
            $phone = substr($phoneNumber, strlen('+' . $country->phoneCode));
            break;
        }
    }
}
?>
<div class="container">
    <div class="row page-titles ">
        <div class="col-md-12 align-self-center text-center">
            <h3 class="text-themecolor">{{ trans('lang.sign_up_with_us') }}</h3>
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
                    <fieldset>
                        <legend>{{ trans('lang.owner_details') }}</legend>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.first_name') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_first_name" required placeholder="{{ trans('lang.user_first_name_help') }}"
                                    value="{{ request('firstName') ? request('firstName') : '' }}">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.last_name') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_last_name" placeholder="{{ trans('lang.user_last_name_help') }}"
                                    value="{{ request('lastName') && request('lastName') != 'undefined' ? request('lastName') : '' }}">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.email') }}</label>
                            <div class="col-7">
                                <input type="email" class="form-control user_email" required placeholder="{{ trans('lang.user_email_help') }}"
                                       value="{{ request('email') ? request('email') : '' }}"  @if(request('loginType') === 'social') disabled @endif>
                            </div>
                        </div>
                        <div class="form-group form-material row width-50">
                            <label class="col-3 control-label">{{ trans('lang.user_phone') }}</label>
                            <div class="col-12">
                                <div class="phone-box position-relative" id="phone-box"> 
                                <select name="country" id="country_selector" class="form-control" @if(request('loginType') === 'phone') disabled @endif>
                                    @foreach($newcountries as $keycy => $valuecy)
                                        <option code="{{ $valuecy->code }}" value="{{ $keycy }}"
                                            @if($selectedCountryCode == $keycy) selected @endif>
                                            +{{ $valuecy->phoneCode }} {{ $valuecy->countryName }}
                                        </option>
                                    @endforeach
                                </select>
                                <input class="form-control mt-2" placeholder="{{ trans('lang.user_phone') }}" id="phone" type="text"
                                    name="phone" value="{{ $phone }}" required autocomplete="phone" @if(request('loginType') === 'phone') disabled @endif autofocus>
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
            <button type="button" class="btn btn-primary create_restaurant_btn"><i class="fa fa-save"></i>
                {{ trans('lang.save') }}
            </button>
            <div class="or-line mb-4">
                <span>OR</span>
            </div>
            <a href="{{ route('login') }}">
                <p class="text-center m-0">{{ trans('lang.already_an_account') }} {{ trans('lang.sign_in') }}</p>
            </a>
        </div>
    </div>
</div>
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
<script>
    var database = firebase.firestore();
    var storageRef = firebase.storage().ref('images');
    var createdAt = firebase.firestore.Timestamp.fromDate(new Date());
    var autoAprroveRestaurant = database.collection('settings').doc("restaurant");
    var adminEmail = '';
    var emailSetting = database.collection('settings').doc('emailSetting');
    var email_templates = database.collection('email_templates').where('type', '==', 'new_vendor_signup');
    var emailTemplatesData = null;
    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/');?>/flags/120/";
        var $state = $(
            '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
        }
    function formatState2(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/');?>/flags/120/"
        var $state = $(
            '<span><img class="img-flag" /> <span></span></span>'
        );
        $state.find("span").text(state.text);
        $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".png");
        return $state;
    }
    $(document).ready(async function () {
        jQuery("#data-table_processing").show();
        await email_templates.get().then(async function (snapshots) {
            emailTemplatesData = snapshots.docs[0].data();
        });
        await emailSetting.get().then(async function (snapshots) {
            var emailSettingData = snapshots.data();
            adminEmail = emailSettingData.userName;
        });
        jQuery("#country_selector").select2({
            templateResult: formatState,
            templateSelection: formatState2,
            placeholder: "Select Country",
            allowClear: true
        });
        jQuery("#data-table_processing").hide();
    });
    $(".create_restaurant_btn").click( async function () {
        $(".error_top").hide();
        var userFirstName = $(".user_first_name").val();
        var userLastName = $(".user_last_name").val();
        var email = $(".user_email").val();
        email = email.toLowerCase();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var phone = $('#phone').val().trim();
        var countryCode = $('#country_selector').val().trim();
        var userPhone = '+' + countryCode + phone;
        var documentVerificationEnable=false;
        database.collection('settings').doc("document_verification_settings").get().then( async function(snapshots){
            var documentVerification = snapshots.data();
            if (documentVerification.isRestaurantVerification) {
                documentVerificationEnable=true;
            }
        })
        var restaurant_active = false;
        const snapshots = await autoAprroveRestaurant.get();
        var restaurantSettingdata = snapshots.data();
        if (restaurantSettingdata.auto_approve_restaurant === true) {
            restaurant_active = true;
        }
        var user_id = "{{request('uuid')}}";
        var name = userFirstName + " " + userLastName;
        var loginType = "{{request('loginType','')}}";
        var provider = ' ';
        if (loginType == "google"){
            provider = "google"; 
        }
        else
        {
            provider = "email";
        }
        if (userFirstName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_name_error')}}</p>");
            window.scrollTo(0, 0);
        }else if (userLastName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_last_name_error')}}</p>");
            window.scrollTo(0, 0);
        }else if (email == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_email')}}</p>");
            window.scrollTo(0, 0);
        }else if (!emailRegex.test(email)) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_email_error')}}</p>");
            window.scrollTo(0, 0);
        } else if (userPhone == '' || phone == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_phone')}}</p>");
            window.scrollTo(0, 0);
        } else {
                    jQuery("#data-table_processing").show();
                    if (user_id == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>User not found.</p>");
                        window.scrollTo(0, 0);
                        return false;
                    }
                    database.collection('users').doc(user_id).set({
                        'appIdentifier':"web",
                        'isDocumentVerify':false,
                        'firstName': userFirstName,
                        'lastName': userLastName,
                        'email': email,
                        'countryCode':countryCode,
                        'phoneNumber': phone,
                        'role': 'vendor',
                        'id': user_id,
                        'active': restaurant_active,
                        'createdAt': createdAt,
                        'provider': provider
                    }).then(function (result) {
                        autoAprroveRestaurant.get().then(async function (snapshots) {
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
                                emailTemplatesData.message = message;
                                var url = "{{url('send-email')}}";
                                var sendEmailStatus = await sendEmail(url, emailTemplatesData.subject, emailTemplatesData.message, [adminEmail]);
                                if (sendEmailStatus) {
                                    var restaurantdata = snapshots.data();
                                    console.log(restaurantdata.auto_approve_restaurant);
                                    if (restaurantdata.auto_approve_restaurant == false) {
                                       
                                        $(".alert-success").show();
                                        $(".alert-success").html("");
                                        $(".alert-success").append("<p>{{trans('lang.signup_waiting_approval')}}</p>");
                                        jQuery("#data-table_processing").hide();
                                        window.scrollTo(0, 0);
                                        setTimeout(function () {
                                            window.location.href = '{{ route("login")}}';
                                        }, 8000);
                                    } else {
                                      
                                        $.ajax({
                                            type: 'POST',
                                            url: "{{route('setToken')}}",
                                            data: {
                                                id: user_id,
                                                userId: user_id,
                                                email: email,
                                                password: '',
                                                firstName: userFirstName,
                                                lastName: userLastName,
                                                profilePicture: ''
                                            },
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            success: function (data) {
                                                if (data.access) {
                                                    window.location = "{{ route('subscription-plan.show') }}";
                                                } else {
                                                    jQuery('#data-table_processing').hide();
                                                    $(".error_top").show();
                                                    $(".error_top").html("");
                                                    $(".error_top").append("<p>Failed to set token.</p>");
                                                }
                                            },
                                            error: function () {
                                                jQuery('#data-table_processing').hide();
                                                $(".error_top").show();
                                                $(".error_top").html("");
                                                $(".error_top").append("<p>An error occurred while setting the token.</p>");
                                            }
                                        });
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
            success: function (data) {
                checkFlag = true;
            },
            error: function (xhr, status, error) {
                checkFlag = true;
            }
        });
        return checkFlag;
    }
    $('#phone').on('keypress',function(event){
              if (!(event.which >= 48 && event.which <= 57)) {
                document.getElementById('error2').innerHTML = "Accept only Number";
                return false;
              } else {
                document.getElementById('error2').innerHTML = "";
                return true;
              }
        });
</script>
