@extends('layouts.app')
@section('content')
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
?>
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.user_profile')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{!! route('dashboard') !!}">{{trans('lang.dashboard')}}</a>
                    </li>
                    <li class="breadcrumb-item active">{{trans('lang.user_profile_edit')}}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="resttab-sec">
                        <div class="error_top"></div>
                        <div class="row restaurant_payout_create">
                            <div class="restaurant_payout_create-inner">
                                <fieldset>
                                    <legend>{{trans('lang.basic_details')}}</legend>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.first_name')}}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_first_name" required>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_first_name_help") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.last_name')}}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_last_name">
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_last_name_help") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.email')}}</label>
                                        <div class="col-7">
                                            <input type="email" class="form-control user_email" disabled required>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_email_help") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group form-material" >
                                            <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                                            <div class="col-12">
                                                <div class="phone-box position-relative" id="phone-box"> 
                                                    <select name="country" id="country_selector" disabled>
                                                        <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                                        <?php    $selected = ""; ?>
                                                        <option <?php    echo $selected; ?> code="<?php    echo $valuecy->code; ?>"
                                                                value="<?php    echo $keycy; ?>">
                                                            +<?php    echo $valuecy->phoneCode; ?> {{$valuecy->countryName}}</option>
                                                        <?php } ?>
                                                    </select>
                                                    <input class="form-control user_phone" disabled placeholder="Phone" id="phone" type="phone"
                                                            name="phone" value="{{ old('phone') }}" required
                                                        autocomplete="phone" autofocus>
                                                    <div id="error2" class="err"></div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-3 control-label">{{trans('lang.profile_pic')}}</label>
                                        <div class="col-9">
                                            <input type="file" onChange="handleFileSelectowner(event,'vendor')">
                                            <div id="uploding_image_owner"></div>
                                            <div class="uploaded_image_owner mt-3" style="display:none;"><img
                                                        id="uploaded_image_owner" src="" width="150px" height="150px;">
                                            </div>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.restaurant_image_help") }}
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="password_div" >
                                    <legend>{{trans('lang.password')}}</legend>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.old_password')}}</label>
                                        <div class="col-7">
                                            <input type="password" class="form-control user_old_password" required>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_password_help") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.new_password')}}</label>
                                        <div class="col-7">
                                            <input type="password" class="form-control user_new_password" required>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_password_help") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 text-center">
                                        <button type="button" class="btn btn-primary  change_user_password"><i
                                                    class="fa fa-save"></i>{{trans('lang.change_password')}}</button>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <legend>{{trans('lang.bankdetails')}}</legend>        
                                    <div class="form-group row">
                                                <div class="form-group row width-100">
                                                    <label class="col-4 control-label">{{trans('lang.bank_name')}}</label>
                                                    <div class="col-7">
                                                        <input type="text" name="bank_name" class="form-control" id="bankName">
                                                    </div>
                                                </div>
                                                <div class="form-group row width-100">
                                                    <label class="col-4 control-label">{{trans('lang.branch_name')}}</label>
                                                    <div class="col-7">
                                                        <input type="text" name="branch_name" class="form-control"
                                                            id="branchName">
                                                    </div>
                                                </div>
                                                <div class="form-group row width-100">
                                                    <label class="col-4 control-label">{{trans('lang.holer_name')}}</label>
                                                    <div class="col-7">
                                                        <input type="text" name="holer_name" class="form-control"
                                                            id="holderName">
                                                    </div>
                                                </div>
                                                <div class="form-group row width-100">
                                                    <label class="col-4 control-label">{{trans('lang.account_number')}}</label>
                                                    <div class="col-7">
                                                        <input type="text" name="account_number" class="form-control"
                                                            id="accountNumber">
                                                    </div>
                                                </div>
                                                <div class="form-group row width-100">
                                                    <label class="col-4 control-label">{{trans('lang.other_information')}}</label>
                                                    <div class="col-7">
                                                        <input type="text" name="other_information" class="form-control"
                                                            id="otherDetails">
                                                    </div>
                                                </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  save_restaurant_btn"><i
                            class="fa fa-save"></i> {{trans('lang.save')}}</button>
                <a href="{!! route('dashboard') !!}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.1.1/compressor.min.js"
            integrity="sha512-VaRptAfSxXFAv+vx33XixtIVT9A/9unb1Q8fp63y1ljF+Sbka+eMJWoDAArdm7jOYuLQHVx5v60TQ+t3EA8weA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script>
        var database = firebase.firestore();
        var geoFirestore = new GeoFirestore(database);
        var storageRef = firebase.storage().ref('images');
        var storage = firebase.storage();
        var photo = "";
        var ownerphoto = '';
        var ownerFileName = '';
        var ownerOldImageFile = '';
        var restaurant_id = "";
        var restaurantOwnerOnline = false;
        var ownerId = '';
        var vendorUserId = "<?php echo $id; ?>";
        var id = '';
        var restaurantOwnerPhoto = '';
        var placeholderImage = '';
        var createdAtman = firebase.firestore.Timestamp.fromDate(new Date());
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function (snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })
        var documentVerificationEnable=false;
        database.collection('settings').doc("document_verification_settings").get().then( async function(snapshots){
          var documentVerification = snapshots.data();
          if (documentVerification.isRestaurantVerification) {
                documentVerificationEnable=true;
          }
        })
        var userVerificationEnable=false;
        database.collection('users').doc(vendorUserId).get().then(async function(snapshots){
            var userData=snapshots.data();
            if(userData && userData.hasOwnProperty('isDocumentVerify') && userData.isDocumentVerify==true || documentVerificationEnable==false){
                userVerificationEnable=true;
            }
        })
        var loginType = getCookie("loginType");
        if(loginType == "phone" || loginType == "social"){
            $(".password_div").hide();
        }else{
            $(".password_div").show();
        }
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = '';
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        refCurrency.get().then(async function (snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            decimal_degits = currencyData.decimal_degits;
            currencyAtRight = currencyData.symbolAtRight;
        });
        $(document).ready(function () {
            $("#country_selector").select2({
                templateResult: formatState,
                templateSelection: formatState2,
                placeholder: "Select Country",
                allowClear: true
            });
            database.collection('users').doc(vendorUserId).get().then(async function (snapshots) {
                var user = snapshots.data();
                ownerId = user.id;
                if(user.vendorID && user.vendorID != ''){
                    restaurant_id = user.vendorID;
                }
                $(".user_first_name").val(user.firstName);
                $(".user_last_name").val(user.lastName);
                $(".user_email").val(user.email);
                $("#country_selector").val(user.countryCode.replace('+', '')).trigger('change');
                $(".user_phone").val(user.phoneNumber);
                restaurantOwnerPhoto = user.profilePictureURL;
                if (user.profilePictureURL != '' && user.profilePictureURL != null) {
                    ownerphoto = user.profilePictureURL
                    ownerOldImageFile = user.profilePictureURL;
                    $("#uploaded_image_owner").attr('src', user.profilePictureURL);
                } else {
                    $("#uploaded_image_owner").attr('src', placeholderImage);
                }
                $(".uploaded_image_owner").show();
                if (user.userBankDetails) {
                    if (user.userBankDetails.bankName != undefined) {
                        $("#bankName").val(user.userBankDetails.bankName);
                    }
                    if (user.userBankDetails.branchName != undefined) {
                        $("#branchName").val(user.userBankDetails.branchName);
                    }
                    if (user.userBankDetails.holderName != undefined) {
                        $("#holderName").val(user.userBankDetails.holderName);
                    }
                    if (user.userBankDetails.accountNumber != undefined) {
                        $("#accountNumber").val(user.userBankDetails.accountNumber);
                    }
                    if (user.userBankDetails.otherDetails != undefined) {
                        $("#otherDetails").val(user.userBankDetails.otherDetails);
                    }
                }
                if (user.wallet_amount != undefined) {
                    var wallet = user.wallet_amount;
                } else {
                    var wallet = 0;
                }
                if (currencyAtRight) {
                    var price_val = parseFloat(wallet).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    var price_val = currentCurrency + "" + parseFloat(wallet).toFixed(decimal_degits);
                }
                $('#wallet_balance').html(price_val);
            });
            $(".change_user_password").click(function () {
                var userOldPassword = $(".user_old_password").val();
                var userNewPassword = $(".user_new_password").val();
                var userEmail = $(".user_email").val();
                if (userOldPassword == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{trans('lang.old_password_error')}}</p>");
                        window.scrollTo(0, 0);
                } else if (userNewPassword == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{trans('lang.new_password_error')}}</p>");
                        window.scrollTo(0, 0);
                } else {
                        var user = firebase.auth().currentUser;
                        firebase.auth().signInWithEmailAndPassword(userEmail, userOldPassword)
                            .then((userCredential) => {
                                var user = userCredential.user;
                                user.updatePassword(userNewPassword).then(() => {
                                    $(".error_top").show();
                                    $(".error_top").html("");
                                    $(".error_top").append("<p>{{trans('lang.password_updated_successfully')}}</p>");
                                    window.scrollTo(0, 0);
                                }).catch((error) => {
                                    $(".error_top").show();
                                    $(".error_top").html("");
                                    $(".error_top").append("<p>" + error + "</p>");
                                    window.scrollTo(0, 0);
                                });
                            })
                            .catch((error) => {
                                var errorCode = error.code;
                                var errorMessage = error.message;
                                $(".error_top").show();
                                $(".error_top").html("");
                                $(".error_top").append("<p>" + errorMessage + "</p>");
                                window.scrollTo(0, 0);
                        });
                }
            });
        });
        var input = document.getElementById('accountNumber');
        input.addEventListener('keypress', function (event) {
        let keycode = event.which || event.keyCode;
        // Check if key pressed is a special character
        if (keycode < 48 ||
            (keycode > 57 && keycode < 65) ||
            (keycode > 90 && keycode < 97) ||
            keycode > 122
        ) {
            // Restrict the special characters
            event.preventDefault();
            return false;
        }
        });
         $(".save_restaurant_btn").click(async function () {
                var userFirstName = $(".user_first_name").val();
                var userLastName = $(".user_last_name").val();
                var email = $(".user_email").val();
                var countryCode = '+' + jQuery("#country_selector").val();
                var userPhone = $(".user_phone").val();
                var reststatus = false;
                if (userFirstName == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.enter_owners_name_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if (email == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.enter_owners_email')}}</p>");
                    window.scrollTo(0, 0);
                } else if (userPhone == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.enter_owners_phone')}}</p>");
                    window.scrollTo(0, 0);
                } else {
                    jQuery("#data-table_processing").show();
                    var bankName = $("#bankName").val();
                    var branchName = $("#branchName").val();
                    var holderName = $("#holderName").val();
                    var accountNumber = $("#accountNumber").val();
                    var otherDetails = $("#otherDetails").val();
                    var userBankDetails = {
                        'bankName': bankName,
                        'branchName': branchName,
                        'holderName': holderName,
                        'accountNumber': accountNumber,
                        'accountNumber': accountNumber,
                        'otherDetails': otherDetails,
                    };
                    await storeImageData().then(async (IMG) =>
                    {
                        if(restaurant_id && restaurant_id != ''){
                            database.collection('vendors').doc(restaurant_id).update({
                                'author': ownerId,
                                'authorProfilePic': IMG && IMG.ownerImage ? IMG.ownerImage : ""
                            }).then(function (result) {}).catch((error) => {
                                console.error("Error writing document: ", error);
                                $("#field_error").html(error);
                            });
                         }
                        database.collection('users').doc(ownerId).update({
                            'firstName': userFirstName,
                            'lastName': userLastName,
                            'email': email,
                            'countryCode': countryCode,
                            'phoneNumber': userPhone,
                            'profilePictureURL': IMG && IMG.ownerImage ? IMG.ownerImage : "",
                            'userBankDetails': userBankDetails
                        }).then(function (result) {
                            window.location.href = '{{ route("user.profile")}}';
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
        async function storeImageData() {
            var newPhoto = [];
            newPhoto['ownerImage'] = ownerphoto;
            try {
                if (ownerphoto != '' && ownerphoto != null) {
                    if (ownerOldImageFile != "" && ownerphoto != ownerOldImageFile) {
                        var ownerOldImageUrlRef = await storage.refFromURL(ownerOldImageFile);
                        imageBucket = ownerOldImageUrlRef.bucket;
                        var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                        if (imageBucket == envBucket) {
                            await ownerOldImageUrlRef.delete().then(() => {
                                console.log("Old file deleted!")
                            }).catch((error) => {
                                console.log("ERR File delete ===", error);
                            });
                        } else {
                            console.log('Bucket not matched');
                        }
                    }
                    if (ownerphoto != ownerOldImageFile) {
                        ownerphoto = ownerphoto.replace(/^data:image\/[a-z]+;base64,/, "")
                        var uploadTask = await storageRef.child(ownerFileName).putString(ownerphoto, 'base64', {contentType: 'image/jpg'});
                        var downloadURL = await uploadTask.ref.getDownloadURL();
                        newPhoto['ownerImage'] = downloadURL;
                        ownerphoto = downloadURL;
                    }
                }
            } catch (error) {
                console.log("ERR ===", error);
            }
            return newPhoto;
        }
        function handleFileSelectowner(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            new Compressor(f, {
                quality: <?php echo env('IMAGE_COMPRESSOR_QUALITY', 0.8); ?>,
                success(result) {
                    f = result;
                    reader.onload = (function (theFile) {
                        return function (e) {
                            var filePayload = e.target.result;
                            var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                            var val = f.name;
                            var ext = val.split('.')[1];
                            var docName = val.split('fakepath')[1];
                            var filename = (f.name).replace(/C:\\fakepath\\/i, '');
                            var timestamp = Number(new Date());
                            var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                            ownerphoto = filePayload;
                            ownerFileName = filename;
                            $("#uploaded_image_owner").attr('src', ownerphoto);
                            $(".uploaded_image_owner").show();
                        };
                    })(f);
                    reader.readAsDataURL(f);
                },
                error(err) {
                    console.log(err.message);
                },
            });
        }
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
        var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
        var newcountriesjs = JSON.parse(newcountriesjs);
        function getCookie(name) {
                var nameEQ=name+"=";
                var ca=document.cookie.split(';');
                for(var i=0;i<ca.length;i++) {
                    var c=ca[i];
                    while(c.charAt(0)==' ') c=c.substring(1,c.length);
                    if(c.indexOf(nameEQ)==0) return c.substring(nameEQ.length,c.length);
                }
                return null;
            }
    </script>
@endsection
