@include('layouts.app')
@include('layouts.header')
@php
    $filepath = public_path('tz-cities-to-countries.json'); 
    $cityToCountry = file_get_contents($filepath);
    $cityToCountry=json_decode($cityToCountry,true);
    $countriesJs=array();
    foreach($cityToCountry as $key=>$value){
        $countriesJs[$key]=$value;
    }
@endphp
<div class="siddhi-checkout">
    <div class="container position-relative">
        <div class="py-5 row">
            <div class="col-md-8 mb-3 checkout-left">
                <div id="gift_card_img"></div>
                <div class="checkout-left-inner">
                    <div class="siddhi-cart-item overflow-hidden bg-white mb-3 mt-3">
                        <div class="bg-white clearfix delevery-partner">
                            <div class="border-bottom p-3 add-note">
                                <h3>{{trans('lang.add_amount')}}</h3>
                                <div class="tip-box">
                                    <div class="custom-control custom-radio border-bottom py-2">
                                        <input type="radio" name="gift_amount" id="1000" value="1000"
                                               class="this_gift_amount custom-control-input">
                                        <label class="custom-control-label" for="1000">
                                            <span class="currency-symbol-left"></span>
                                            <span class="decimal_digit">1000</span>
                                            <span class="currency-symbol-right"></span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio border-bottom py-2">
                                        <input type="radio" name="gift_amount" id="2000" value="2000"
                                               class="this_gift_amount custom-control-input">
                                        <label class="custom-control-label" for="2000">
                                            <span class="currency-symbol-left"></span>
                                            <span class="decimal_digit">2000</span>
                                            <span class="currency-symbol-right"></span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio border-bottom py-2">
                                        <input type="radio" name="gift_amount" id="5000" value="5000"
                                               class="this_gift_amount custom-control-input">
                                        <label class="custom-control-label" for="5000">
                                            <span class="currency-symbol-left"></span>
                                            <span class="decimal_digit">5000</span>
                                            <span class="currency-symbol-right"></span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio border-bottom py-2">
                                        <input type="radio" name="gift_amount" id="other_amount" value="Other"
                                               class="custom-control-input">
                                        <label class="custom-control-label"
                                               for="other_amount">{{trans('lang.other')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio border-bottom py-2" style="display: none;"
                                         id="add_gift_amount_box">
                                        <h3 class="text-left">{{trans('lang.add_amount')}}</h3>
                                        <input type="number" name="giftAmount"
                                               id="giftAmount" onchange="changeGiftAmount()"
                                               value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="add-note">
                        <div class="p-3">
                            <h3>{{trans('lang.message')}}</h3>
                            <textarea name="gift-card-desc" id="gift-card-desc"></textarea>
                        </div>
                    </div>
                    <input type="text" name="giftId" id="giftId" hidden>
                    <div class="accordion mb-3 rounded shadow-sm bg-white checkout-left-box border"
                         id="accordionExample">
                        <div class="siddhi-card border-bottom overflow-hidden">
                            <div class="siddhi-card-header" id="headingTwo">
                                <h6 class="mb-2 ml-3 mt-3">{{trans('lang.select_payment_option')}}</h6>
                            </div>
                        </div>
                        <div class="siddhi-card overflow-hidden checkout-payment-options">
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;"
                                 id="paystack_box">
                                <input type="radio" name="payment_method" id="paystack" value="paystack"
                                       class="custom-control-input" checked>
                                <label class="custom-control-label"
                                       for="paystack">{{trans('lang.pay_stack')}}</label>
                                <input type="hidden" id="paystack_isEnabled">
                                <input type="hidden" id="paystack_isSandbox">
                                <input type="hidden" id="paystack_public_key">
                                <input type="hidden" id="paystack_secret_key">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="siddhi-cart-item rounded rounded shadow-sm overflow-hidden bg-white sticky_sidebar"
                     id="cart_list">
                    <div class="sidebar-header p-3">
                        <h3 class="font-weight-bold h6 w-100">{{trans('lang.billing_summary')}}</h3>
                        <p>{{trans('lang.billing_summary_title')}}</p>
                    </div>
                    <div class="bg-white p-3 clearfix btm-total">
                        <p class="mb-2">
                            {{trans('lang.sub_total')}}
                            <span class="float-right text-dark">
        	                <span class="currency-symbol-left"></span>
              <span id="sub_total">0</span>
           	<span class="currency-symbol-right"></span>
        </span>
                        </p>
                        <hr>
                        <h6 class="font-weight-bold mb-0">{{trans('lang.total')}}
                            <p class="float-right">
                                <span class="currency-symbol-left"></span>
                                <span id="total">0</span>
                                <span class="currency-symbol-right"></span>
                            </p>
                        </h6>
                    </div>
                    <div class="p-3">
                        <a class="btn btn-primary btn-block btn-lg" href="javascript:void(0)"
                           onclick="finalCheckout()">{{trans('lang.pay')}} <span
                                    class="currency-symbol-left"></span>
                            <span id="payableAmount">0</span>
                            <span class="currency-symbol-right"></span><i class="feather-arrow-right"></i></a>
                    </div>
                    <input type="text" id="total_pay" hidden>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@include('layouts.nav')
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script type="text/javascript" src="{{asset('vendor/slick/slick.min.js')}}"></script>
<script type="text/javascript">
    cityToCountry = '<?php echo json_encode($countriesJs);?>';
    var createdAt = firebase.firestore.FieldValue.serverTimestamp();
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    var currencyData = '';
    refCurrency.get().then(async function (snapshots) {
        currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
        loadcurrencynew();
    });
    cityToCountry = JSON.parse(cityToCountry);
    var userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    userCity = userTimeZone.split('/')[1];
    userCountry = cityToCountry[userCity];
    var wallet_amount = 0;
    var fcmToken = '';
    var id_gift = database.collection('tmp').doc().id;
    var userId = "<?php echo $id; ?>";
    var userDetailsRef = database.collection('users').where('id', "==", userId);
    var uservendorDetailsRef = database.collection('users');
    var vendorDetailsRef = database.collection('vendors');
    var AdminCommission = database.collection('settings').doc('AdminCommission');
    var razorpaySettings = database.collection('settings').doc('razorpaySettings');
    var codSettings = database.collection('settings').doc('CODSettings');
    var stripeSettings = database.collection('settings').doc('stripeSettings');
    var paypalSettings = database.collection('settings').doc('paypalSettings');
    var XenditSettings = database.collection('settings').doc('xendit_settings');
    var Midtrans_settings = database.collection('settings').doc('midtrans_settings');
    var OrangePaySettings = database.collection('settings').doc('orange_money_settings');
    var walletSettings = database.collection('settings').doc('walletSettings');
    taxSetting = [];
    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
    placeholder.get().then(async function (snapshotsimage) {
        var placeholderImageData = snapshotsimage.data();
        placeholderImage = placeholderImageData.image;
    })
    userDetailsRef.get().then(async function (userSnapshots) {
        var userDetails = userSnapshots && userSnapshots.docs && userSnapshots.docs[0] ? userSnapshots.docs[0].data() : '';
        if (userDetails.wallet_amount != undefined && userDetails.wallet_amount != '' && !isNaN(userDetails.wallet_amount)) {
            wallet_amount = parseFloat(userDetails.wallet_amount);
            $("#wallet").attr('disabled', false);
            $("#user_wallet_amount").val(wallet_amount);
        }
        var wallet_balance = 0;
        if (currencyAtRight) {
            wallet_balance = parseFloat(wallet_amount).toFixed(decimal_degits) + "" + currentCurrency;
        } else {
            wallet_balance = currentCurrency + "" + parseFloat(wallet_amount).toFixed(decimal_degits);
        }
        $("#wallet_amount").html(wallet_balance);
    });
    var reftaxSetting = database.collection('tax').where('country', '==', userCountry).where('enable', '==', true);
    reftaxSetting.get().then(async function (snapshots) {
        if (snapshots.docs.length > 0) {
            snapshots.docs.forEach((val) => {
                val = val.data();
                var obj = '';
                obj = {
                    'country': val.country,
                    'enable': val.enable,
                    'id': val.id,
                    'tax': val.tax,
                    'title': val.title,
                    'type': val.type,
                }
                taxSetting.push(obj);
            })
        }
    });
    var payStackSettings = database.collection('settings').doc('payStack');
    var giftRef = database.collection('gift_cards').where('isEnable', '==', true);
    function loadcurrencynew() {
        if (currencyAtRight) {
            jQuery('.currency-symbol-left').hide();
            jQuery('.currency-symbol-right').show();
            jQuery('.currency-symbol-right').text(currentCurrency);
        } else {
            jQuery('.currency-symbol-left').show();
            jQuery('.currency-symbol-right').hide();
            jQuery('.currency-symbol-left').text(currentCurrency);
        }
        $('.decimal_digit').each(function () {
            var amount = $(this).text();
            $(this).text(parseFloat(amount).toFixed(decimal_degits));
        });
        var amount = $('.decimal_digits').attr('data-val');
        jQuery('.decimal_digits').text(parseFloat(amount).toFixed(decimal_degits));
    }
    var orderPlacedSubject = '';
    var orderPlacedMsg = '';
    var scheduleOrderPlacedSubject = '';
    var scheduleOrderPlacedMsg = '';
    database.collection('dynamic_notification').get().then(async function (snapshot) {
        if (snapshot.docs.length > 0) {
            snapshot.docs.map(async (listval) => {
                val = listval.data();
                if (val.type == "order_placed") {
                    orderPlacedSubject = val.subject;
                    orderPlacedMsg = val.message;
                } else if (val.type == "schedule_order") {
                    scheduleOrderPlacedSubject = val.subject;
                    scheduleOrderPlacedMsg = val.message;
                }
            })
        }
    })
    $(document).ready(function () {
        getUserDetails();
    });
    async function getUserDetails() {
        payStackSettings.get().then(async function (payStackSettingsSnapshots) {
            payStackSetting = payStackSettingsSnapshots.data();
            if (payStackSetting.isEnable) {
                var isEnable = payStackSetting.isEnable;
                $("#paystack_isEnabled").val(isEnable);
                var isSandboxEnabled = payStackSetting.isSandbox;
                $("#paystack_isSandbox").val(isSandboxEnabled);
                var publicKey = payStackSetting.publicKey;
                $("#paystack_public_key").val(publicKey);
                var secretKey = payStackSetting.secretKey;
                $("#paystack_secret_key").val(secretKey);
                $("#paystack_box").show();
            }
        });
    }
    async function finalCheckout() {
        payableAmount = $('#giftAmount').val();
        if (payableAmount == 0 || payableAmount == '' || payableAmount == "0") {
            return false;
        }
        var giftId = $('#giftId').val();
        database.collection('gift_cards').where('id', '==', giftId).get().then(function (giftsnapshots) {
            var createdDate = firebase.firestore.FieldValue.serverTimestamp();
            var data = giftsnapshots.docs[0].data();
            var giftAmount = $('#giftAmount').val();
            var giftMessage = $('#gift-card-desc').val();
            var payment_method = $('input[name="payment_method"]:checked').val();
            var giftId = data.id;
            var giftTitle = data.title;
            var giftexpiry = data.expiryDay;
            var id = id_gift;
            var redeem = false;
            var user_id = userId;
            var giftPin = Math.floor(100000 + Math.random() * 900000);
            var date = new Date();
            var giftCode = date.getTime() + Math.floor(100 + Math.random() * 999);
            if (payment_method == "paystack") {
                var paystack_public_key = $("#paystack_public_key").val();
                var paystack_secret_key = $("#paystack_secret_key").val();
                var paystack_isSandbox = $("#paystack_isSandbox").val();
                var gift_json = {
                    'giftId': giftId,
                    'price': giftAmount,
                    'message': giftMessage,
                    'redeem': false,
                    'userid': user_id,
                    'id': id,
                    'giftTitle': giftTitle,
                    'giftPin': giftPin,
                    'giftCode': giftCode,
                    'expiryDay': giftexpiry
                }
                $.ajax({
                    type: 'POST',
                    url: "<?php echo route('giftcard.processing'); ?>",
                    data: {
                        _token: '<?php echo csrf_token() ?>',
                        order_json: gift_json,
                        payment_method: payment_method,
                        authorName: '',
                        total_pay: giftAmount,
                        paystack_isSandbox: paystack_isSandbox,
                        paystack_public_key: paystack_public_key,
                        paystack_secret_key: paystack_secret_key,
                        address_line1: '',
                        address_line2: '',
                        address_zipcode: '',
                        address_city: '',
                        address_country: '',
                        currencyData: currencyData
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        $('#cart_list').html(data.html);
                        loadcurrencynew();
                        window.location.href = "<?php echo route('giftcard.pay'); ?>";
                    }
                });
            }
        })
    }
    $(document).on("click", '#other_amount', function (event) {
        $("#giftAmount").val('');
        $("#add_gift_amount_box").show();
    });
    $(document).on("click", '.this_gift_amount', function (event) {
        $('.this_gift_amount').removeClass('tip_checked');
        var this_gift_amount = $(this).val();
        var data = $(this);
        $("#add_gift_amount_box").hide();
        if ((data).is('.tip_checked')) {
            data.removeClass('tip_checked');
            $(this).prop('checked', false);
            $("#giftAmount").val('');
            $('#sub_total').text(0);
            $('#total').text(0);
        } else {
            if (decimal_degits) {
                amount = parseFloat(this_gift_amount).toFixed(decimal_degits);
            } else {
                amount = parseFloat(this_gift_amount).toFixed(2);
            }
            $(this).addClass('tip_checked');
            $("#giftAmount").val(amount);
            $('#sub_total').text(amount);
            $('#total').text(amount);
            $('#payableAmount').text(amount);
        }
    });
    function changeGiftAmount() {
        var gift_amount = $('#giftAmount').val();
        if (gift_amount == '') {
            gift_amount = 0;
        }
        gift_amount = parseFloat(gift_amount).toFixed(decimal_degits);
        $('#sub_total').text(gift_amount);
        $('#total').text(gift_amount);
        $('#payableAmount').text(gift_amount);
    }
    giftRef.get().then(async function (giftSnapshots) {
        if (giftSnapshots.docs.length > 0) {
            var html = '';
            giftSnapshots.docs.forEach((val) => {
                var giftCardData = val.data();
                html += '<div class="banner-item">';
                html += '<div class="banner-img">';
                html += '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" src="' + giftCardData.image + '" id="' + giftCardData.id + '">';
                html = html + '</div></div>';
            });
            $('#gift_card_img').html(html);
        } else {
            $('.checkout-left-inner').text('{{trans('lang.no_gift_card_available')}}');
        }
        slickcatCarousel();
    })
    function slickcatCarousel() {
        if ($("#gift_card_img").html() != "") {
            $('#gift_card_img').slick({
                slidesToShow: 1,
                dots: true,
                arrows: true
            });
        }
        var activeGiftCardId = $('.slick-active .banner-img').find("img").attr('id');
        $('#giftId').val(activeGiftCardId);
        giftRef.where('id', '==', activeGiftCardId).get().then(async function (Snapshots) {
            var data = Snapshots.docs[0].data();
            $('#gift-card-desc').val(data.message);
        })
    }
    $('#gift_card_img').on('afterChange', function (event, slick, currentSlide, nextSlide) {
        var currentSlide = $(slick.$slides[currentSlide]);
        var activeGiftCardId = currentSlide.find("img").attr('id');
        $('#giftId').val(activeGiftCardId);
        giftRef.where('id', '==', activeGiftCardId).get().then(async function (Snapshots) {
            var data = Snapshots.docs[0].data();
            $('#gift-card-desc').val(data.message);
        })
    });
</script>
