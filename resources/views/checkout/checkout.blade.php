@include('layouts.app')
@include('layouts.header')
@php
    $cityToCountry = file_get_contents(public_path('tz-cities-to-countries.json'));
    $cityToCountry = json_decode($cityToCountry, true);
    $countriesJs = [];
    foreach ($cityToCountry as $key => $value) {
        $countriesJs[$key] = $value;
    }
@endphp
<div class="siddhi-checkout">
    <div class="container position-relative">
        <div class="py-5 row">
            <div class="col-md-8 mb-3 checkout-left">
                <div class="checkout-left-inner">
                    <?php if (('takeawayOption') == "true") { ?>
                    <div class="siddhi-cart-item mb-4 rounded shadow-sm bg-white checkout-left-box border" style="display:none;">
                        <?php } else { ?>
                        <div class="siddhi-cart-item mb-4 rounded shadow-sm bg-white checkout-left-box border">
                            <?php } ?>
                            <div class="siddhi-cart-item-profile p-3">
                                <div class="d-flex flex-column">
                                    <div class="chec-out-header d-flex mb-3">
                                        <div class="chec-out-title">
                                            <h6 class="mb-0 font-weight-bold pb-1">{{ trans('lang.delivery_address') }}
                                            </h6>
                                            <span>{{ trans('lang.save_address_location') }}</span>
                                        </div>
                                        <a href="{{ route('delivery-address.index') }}" class="ml-auto font-weight-bold">{{ trans('lang.change') }}</a>
                                    </div>
                                    <div class="row">
                                        <div class="custom-control col-lg-12 mb-3 position-relative" id="address_box" style="display: none;">
                                            <div class="addres-innerbox">
                                                <div class="p-3 w-100">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <h6 class="mb-0 pb-1">{{ trans('lang.address') }}</h6>
                                                    </div>
                                                    <p class="text-dark m-0" id="line_1"></p>
                                                    <p class="text-dark m-0" id="line_2">
                                                        {{ trans('lang.rewood_city') }}</p>
                                                    <input type="text" id="addressId" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a id="add_address" class="btn btn-primary" href="#" data-toggle="modal" data-target="#locationModalAddress" style="display: none;">
                                        {{ trans('lang.add_new_address') }} </a>
                                </div>
                            </div>
                        </div>
                        <div class="accordion mb-3 rounded shadow-sm bg-white checkout-left-box border" id="accordionExample">
                            <div class="siddhi-card border-bottom overflow-hidden">
                                <div class="siddhi-card-header" id="headingTwo">
                                    <h6 class="mb-2 ml-3 mt-3">{{ trans('lang.select_payment_option') }}</h6>
                                </div>
                            </div>
                            <div class="siddhi-card overflow-hidden checkout-payment-options">
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="paystack_box">
                                    <input type="radio" name="payment_method" id="paystack" value="paystack" class="custom-control-input">
                                    <label class="custom-control-label" for="paystack">{{ trans('lang.pay_stack') }}</label>
                                    <input type="hidden" id="paystack_isEnabled">
                                    <input type="hidden" id="paystack_isSandbox">
                                    <input type="hidden" id="paystack_public_key">
                                    <input type="hidden" id="paystack_secret_key">
                                </div>
                            </div>
                        </div>
                        <div class="add-note">
                            <h3>{{ trans('lang.add_note') }}</h3>
                            <textarea name="add-note" id="add-note" onchange="changeNote();"><?php echo @$cart['order-note']; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="siddhi-cart-item rounded rounded shadow-sm overflow-hidden bg-white sticky_sidebar" id="cart_list">
                        @include('restaurant.cart_item')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@include('layouts.nav')
<div class="modal fade" id="exampleModalAddress" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('lang.delivery_address') }}</h5>
                <button type="button" id="close_button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="">
                    <div class="form-row">
                        <div class="col-md-12 form-group">
                            <label class="form-label">{{ trans('lang.street_1') }}</label>
                            <div class="input-group">
                                <input placeholder="Delivery Area" type="text" id="address_line1" class="form-control">
                                <div class="input-group-append">
                                    <button onclick="getCurrentLocationAddress1()" type="button" class="btn btn-outline-secondary"><i class="feather-map-pin"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.landmark') }}</label><input placeholder="Complete Address e.g. house number, street name, landmark" value="" id="address_line2" type="text" class="form-control"></div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.zip_code') }}</label><input placeholder="Zip Code" id="address_zipcode" type="text" class="form-control">
                        </div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.city') }}</label><input placeholder="City" id="address_city" type="text" class="form-control"></div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.country') }}</label><input placeholder="Country" id="address_country" type="text" class="form-control">
                        </div>
                        <input type="hidden" name="address_lat" id="address_lat">
                        <input type="hidden" name="address_lng" id="address_lng">
                    </div>
                </form>
            </div>
            <div class="modal-footer p-0 border-0">
                <div class="col-6 m-0 p-0">
                    <button type="button" class="btn border-top btn-lg btn-block" data-dismiss="modal">{{ trans('lang.close') }}</button>
                </div>
                <div class="col-6 m-0 p-0">
                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="saveShippingAddress()">{{ trans('lang.save_changes') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="siddhi-menu-fotter fixed-bottom bg-white px-3 py-2 text-center d-none">
    <div class="row">
        <div class="col selected">
            <a href="home.html" class="text-danger small font-weight-bold text-decoration-none">
                <p class="h4 m-0"><i class="feather-home text-danger"></i></p>
                {{ trans('lang.home') }}
            </a>
        </div>
        <div class="col">
            <a href="most_popular.html" class="text-dark small font-weight-bold text-decoration-none">
                <p class="h4 m-0"><i class="feather-map-pin"></i></p>
                {{ trans('lang.trending') }}
            </a>
        </div>
        <div class="col bg-white rounded-circle mt-n4 px-3 py-2">
            <div class="bg-danger rounded-circle mt-n0 shadow">
                <a href="checkout.html" class="text-white small font-weight-bold text-decoration-none">
                    <i class="feather-shopping-cart"></i>
                </a>
            </div>
        </div>
        <div class="col">
            <a href="favorites.html" class="text-dark small font-weight-bold text-decoration-none">
                <p class="h4 m-0"><i class="feather-heart"></i></p>
                {{ trans('lang.favorites') }}
            </a>
        </div>
        <div class="col">
            <a href="profile.html" class="text-dark small font-weight-bold text-decoration-none">
                <p class="h4 m-0"><i class="feather-user"></i></p>
                {{ trans('lang.profile') }}
            </a>
        </div>
    </div>
</div>
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script type="text/javascript">
    cityToCountry = '<?php echo json_encode($countriesJs); ?>';
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function(snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });
    cityToCountry = JSON.parse(cityToCountry);
    var userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    userCity = userTimeZone.split('/')[1];
    userCountry = cityToCountry[userCity];
    var wallet_amount = 0;
    var fcmToken = '';
    var id_order = database.collection('tmp').doc().id;
    var userId = "<?php echo $id; ?>";
    var userDetailsRef = database.collection('users').where('id', "==", userId);
    var uservendorDetailsRef = database.collection('users');
    var vendorDetailsRef = database.collection('vendors');
    var main_restaurant_id = $("#main_vendor_id").val();
    
    async function getAdminCommission() {
        database.collection('settings').doc('AdminCommission').get().then(async function(AdminCommissionSnapshots) {
            if (AdminCommissionSnapshots.exists) {
                var AdminCommissionRes = AdminCommissionSnapshots.data();
                var AdminCommissionValueBase = AdminCommissionRes.fix_commission;
                var AdminCommissionTypeBase = AdminCommissionRes.commissionType;
                if (AdminCommissionRes.isEnabled && main_restaurant_id) {
                    await database.collection('vendors').where('id', '==', main_restaurant_id).get()
                        .then(async function(
                            snapshot) {
                            var data = snapshot.docs[0].data();
                            if (data.hasOwnProperty('adminCommission') && data
                                .adminCommission != null &&
                                data.adminCommission != '') {
                                $("#adminCommission").val(data.adminCommission.fix_commission);
                                $("#adminCommissionType").val(data.adminCommission
                                    .commissionType);
                            } else {
                                $("#adminCommission").val(AdminCommissionValueBase);
                                $("#adminCommissionType").val(AdminCommissionTypeBase);
                            }
                        })
                } else {
                    $("#adminCommission").val(0);
                    $("#adminCommissionType").val('Fixed');
                }
            } else {
                $("#adminCommission").val(0);
                $("#adminCommissionType").val('Fixed');
            }
        });
    }
    var payStackSettings = database.collection('settings').doc('payStack');
    taxSetting = [];
    var reftaxSetting = database.collection('tax').where('country', '==', userCountry).where('enable', '==', true);
    reftaxSetting.get().then(async function(snapshots) {
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
    var currentCurrency = '';
    var currencyAtRight = false;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    var currencyData = '';
    refCurrency.get().then(async function(snapshots) {
        currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        loadcurrencynew();
    });

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
    }
    var orderPlacedSubject = '';
    var orderPlacedMsg = '';
    var scheduleOrderPlacedSubject = '';
    var scheduleOrderPlacedMsg = '';
    database.collection('dynamic_notification').get().then(async function(snapshot) {
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
    $(document).ready(function() {
        var today = new Date().toISOString().slice(0, 16);
        if (document.getElementsByName("scheduleTime").length > 0) {
            document.getElementsByName("scheduleTime")[0].min = today;
        }
        getUserDetails();
        getAdminCommission();
        $(document).on("click", '.remove_item', function(event) {
            var id = $(this).attr('data-id');
            var restaurant_id = $(this).attr('data-vendor-id');
            $.ajax({
                type: 'POST',
                url: "<?php echo route('remove-from-cart'); ?>",
                data: {
                    _token: '<?php echo csrf_token(); ?>',
                    restaurant_id: restaurant_id,
                    id: id,
                    is_checkout: 1
                },
                success: function(data) {
                    data = JSON.parse(data);
                    $('#cart_list').html(data.html);
                    loadcurrencynew();
                    var today = new Date().toISOString().slice(0, 16);
                    if (document.getElementsByName("scheduleTime").length > 0) {
                        document.getElementsByName("scheduleTime")[0].min = today;
                    }
                    getAdminCommission();
                }
            });
        });
        $(document).on("click", '.count-number-input-cart', function(event) {

            var id = $(this).attr('data-id');
            var restaurant_id = $(this).attr('data-vendor-id');
            var quantity = $('.count_number_' + id).val();
            var stock_quantity = $(this).attr('data-vqty');

            if (stock_quantity != "" && stock_quantity != undefined && parseInt(stock_quantity) != -1) {

                if (parseInt(quantity) > parseInt(stock_quantity)) {
                    Swal.fire({
                        text: "{{ trans('lang.invalid_stock_qty') }}",
                        icon: "error"
                    });
                    $('.count_number_' + id).val(quantity - 1);
                    return false;
                }

            }

            $.ajax({
                type: 'POST',
                url: "<?php echo route('change-quantity-cart'); ?>",
                data: {
                    _token: '<?php echo csrf_token(); ?>',
                    restaurant_id: restaurant_id,
                    id: id,
                    quantity: quantity,
                    is_checkout: 1
                },
                success: function(data) {
                    data = JSON.parse(data);
                    $('#cart_list').html(data.html);
                    loadcurrencynew();
                    var today = new Date().toISOString().slice(0, 16);
                    if (document.getElementsByName("scheduleTime").length > 0) {
                        document.getElementsByName("scheduleTime")[0].min = today;
                    }
                    getAdminCommission();
                }
            });

        });
        $(document).on("click", '#apply-coupon-code', function(event) {
            var coupon_code = $("#coupon_code").val();
            var restaurant_id = $(this).attr('data-vendor-id');
            var couponCodeRef = database.collection('coupons').where('code', "==", coupon_code).where(
                'isEnabled', '==', true).where('expiresAt', '>=', new Date());
            couponCodeRef.get().then(async function(couponSnapshots) {
                if (couponSnapshots.docs.length) {
                    var coupondata = couponSnapshots.docs[0].data();
                    if (coupondata.resturant_id != undefined && coupondata.resturant_id !=
                        '') {
                        if (coupondata.resturant_id == restaurant_id) {
                            discount = coupondata.discount;
                            discountType = coupondata.discountType;
                            $.ajax({
                                type: 'POST',
                                url: "<?php echo route('apply-coupon'); ?>",
                                data: {
                                    _token: '<?php echo csrf_token(); ?>',
                                    coupon_code: coupon_code,
                                    discount: discount,
                                    discountType: discountType,
                                    is_checkout: 1,
                                    coupon_id: coupondata.id
                                },
                                success: function(data) {
                                    data = JSON.parse(data);
                                    $('#cart_list').html(data.html);
                                    $("#coupon_code").val('');
                                    loadcurrencynew();
                                    var today = new Date().toISOString().slice(
                                        0, 16);
                                    if (document.getElementsByName(
                                            "scheduleTime").length > 0) {
                                        document.getElementsByName(
                                            "scheduleTime")[0].min = today;
                                    }
                                    getAdminCommission();
                                }
                            });
                        } else {
                            Swal.fire({
                                text: "{{ trans('lang.coupon_not_valid') }}",
                                icon: "error"
                            });
                            $("#coupon_code").val('');
                        }
                    } else {
                        Swal.fire({
                            text: "{{ trans('lang.coupon_not_valid') }}",
                            icon: "error"
                        });
                        $("#coupon_code").val('');
                    }
                } else {
                    Swal.fire({
                        text: "{{ trans('lang.coupon_not_valid') }}",
                        icon: "error"
                    });
                    $("#coupon_code").val('');
                }
            });
        });
    });
    async function getUserDetails() {
        payStackSettings.get().then(async function(payStackSettingsSnapshots) {
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
        userDetailsRef.get().then(async function(userSnapshots) {
            var userDetails = userSnapshots.docs[0].data();
            var sessionAdrsId = sessionStorage.getItem('addressId');
            var full_address = '';
            var takeaway_options = '<?php echo Session::get('takeawayOption'); ?>';
            if (userDetails.hasOwnProperty('shippingAddress') && Array.isArray(userDetails.shippingAddress)) {
                shippingAddress = userDetails.shippingAddress;
                var isShipping = false;
                shippingAddress.forEach((listval) => {
                    if (sessionAdrsId != '' && sessionAdrsId != null) {
                        if (listval.id == sessionAdrsId) {
                            $("#line_1").html(listval.address);
                            $('#line_2').html(listval.locality + " " + listval.landmark);
                            $('#addressId').val(listval.id);
                            $("#address_box").show();
                            isShipping = true;
                        }
                    }

                });
                if (isShipping == false) {
                    if (takeaway_options == "false" || takeaway_options == false) {
                        window.location.href = "{{ route('delivery-address.index') }}";
                    }
                }
            } else {
                if (takeaway_options == "false" || takeaway_options == false) {
                    window.location.href = "{{ route('delivery-address.index') }}";
                }
            }
            if (userDetails.wallet_amount != undefined && userDetails.wallet_amount != '' && !isNaN(
                    userDetails.wallet_amount)) {
                wallet_amount = parseFloat(userDetails.wallet_amount);
                $("#wallet").attr('disabled', false);
                $("#user_wallet_amount").val(wallet_amount);
            }
            var wallet_balance = 0;
            if (currencyAtRight) {
                wallet_balance = parseFloat(wallet_amount).toFixed(decimal_degits) + "" +
                    currentCurrency;
            } else {
                wallet_balance = currentCurrency + "" + parseFloat(wallet_amount).toFixed(
                    decimal_degits);
            }
            $("#wallet_amount").html(wallet_balance);
        });

        if (main_restaurant_id) {
            uservendorDetailsRef.where('vendorID', "==", main_restaurant_id).get().then(async function(
                uservendorSnapshots) {
                if (uservendorSnapshots.docs.length) {
                    var userVendorDetails = uservendorSnapshots.docs[0].data();
                    if (userVendorDetails && userVendorDetails.fcmToken) {
                        fcmToken = userVendorDetails.fcmToken;
                    }
                   
                }
            });
        }
  
    }
    async function manageInventory(products) {
        for (let i = 0; i < products.length; i++) {
            var item = products[i];
            var product_id = item.id;
            var quantity = item.quantity;
            var variant_info = item.variant_info;
            var productDoc = await database.collection('vendor_products').doc(product_id).get();
            var productInfo = productDoc.data();
            if (variant_info) {
                var new_varients = [];
                $.each(productInfo.item_attribute.variants, function(key, value) {
                    if (value.variant_sku == variant_info.variant_sku && value.variant_quantity !=
                        undefined && value.variant_quantity != '-1') {
                        value.variant_quantity = value.variant_quantity - quantity;
                        value.variant_quantity = (value.variant_quantity <= 0) ? 0 : value.variant_quantity;
                        value.variant_quantity = value.variant_quantity.toString();
                        new_varients.push(value);
                    } else {
                        new_varients.push(value);
                    }
                });
                database.collection('vendor_products').doc(product_id).update({
                    'item_attribute.variants': new_varients
                });
            } else {
                if (productInfo.quantity != undefined && productInfo.quantity != '-1') {
                    var new_quantity = productInfo.quantity - quantity;
                    new_quantity = (new_quantity <= 0) ? 0 : new_quantity;
                    database.collection('vendor_products').doc(product_id).update({
                        'quantity': new_quantity
                    });
                }
            }
        }
    }
    async function getVendorUser(vendorUserId) {
        var vendorUSerData = '';
        await database.collection('users').where('id', "==", vendorUserId).get().then(async function(
            uservendorSnapshots) {
            if (uservendorSnapshots.docs.length) {
                vendorUSerData = uservendorSnapshots.docs[0].data();
            }
        });
        return vendorUSerData;
    }
    async function finalCheckout() {
        var payment_method = $('input[name="payment_method"]:checked').val();
        if (payment_method == false || payment_method == undefined || payment_method == '') {
            Swal.fire({
                text: "{{ trans('lang.select_payment_option') }}",
                icon: "error"
            });
            return false;
        }
        payableAmount = $('#total_pay').val();
        if (payableAmount == 0 || payableAmount == '' || payableAmount == "0") {
            return false;
        }
        var id = $('.count-number-input-cart').attr('data-id');
        var quantity = $('.count_number_' + id).val();
        var stock_quantity = $('.count-number-input-cart').attr('data-vqty');
        userDetailsRef.get().then(async function(userSnapshots) {
            var vendorID = $("#main_vendor_id").val();
            var userDetails = userSnapshots.docs[0].data();
            vendorDetailsRef.where('id', "==", vendorID).get().then(async function(vendorSnapshots) {
                var vendorDetails = vendorSnapshots.docs[0].data();
                if (vendorDetails) {
                    var vendorUser = await getVendorUser(vendorDetails.author);
                    var products = [];
                    $(".product-item").each(function(index) {
                        product_id = $(this).attr("data-id");
                        price = $("#price_" + product_id).val();
                        item_price = $("#item_price_" + product_id).val();
                        photo = $("#photo_" + product_id).val();
                        total_pay = $("#total_pay").val();
                        extras_price = $("#extras_price_" + product_id).val();
                        size = $("#size_" + product_id).val();
                        name = $("#name_" + product_id).val();
                        quantity = $("#quantity_" + product_id).val();
                        extras = [];
                        $(".extras_" + product_id).each(function(index) {
                            val = $(this).val();
                            if (val) {
                                extras.push(val);
                            }
                        })
                        var category_id = $("#category_id_" + product_id).val();
                        var variant_info = $("#variant_info_" + product_id).val();
                        if (variant_info) {
                            variant_info = $.parseJSON(atob(variant_info));
                            product_id = product_id.split("PV")[0];
                        } else {
                            var variant_info = null;
                        }
                        products.push({
                            'id': product_id,
                            'name': name,
                            'photo': photo,
                            'price': item_price,
                            'quantity': parseInt(quantity),
                            'vendorID': vendorDetails.id,
                            'extras_price': extras_price,
                            'extras': extras,
                            'size': size,
                            'variant_info': variant_info,
                            'category_id': category_id
                        })
                    });
                    manageInventory(products);
                    var address = '';
                    shippingAdrs = userDetails.shippingAddress;
                    addressId = $('#addressId').val();
                    shippingAdrs.forEach((listval) => {
                        if (listval.id == addressId) {
                            address = listval;
                        }
                    })
                    var author = userDetails;
                    var authorID = userId;
                    var authorName = userDetails.firstName;
                    var authorEmail = userDetails.email;
                    var couponCode = $("#coupon_code_main").val();
                    var couponId = $("#coupon_id").val();
                    var createdAt = firebase.firestore.FieldValue.serverTimestamp();
                    var discount = $("#discount_amount").val();
                    var driver = [];
                    var vendor = vendorDetails;
                    var status = 'Order Placed';
                    var deliveryCharge = $("#deliveryCharge").val();
                    var tip_amount = $("#tip_amount").val();
                    var adminCommission = $("#adminCommission").val();
                    var adminCommissionType = $("#adminCommissionType").val();
                    var tax_label = $("#tax_label").val();
                    var tax = $("#tax").val();
                    var delivery_option = $('input[name="delivery_option"]').val();
                    var take_away = false;
                    if (delivery_option == "takeaway") {
                        take_away = true;
                    }
                    var notes = $("#add-note").val();
                    var specialOfferDiscountAmount = $('#specialOfferDiscountAmount').val();
                    var specialOfferType = $('#specialOfferType').val();
                    var specialOfferDiscountVal = $('#specialOfferDiscountVal').val();
                    var specialDiscount = [];
                    var specialDiscount = {
                        'special_discount': specialOfferDiscountAmount,
                        'specialType': specialOfferType,
                        'special_discount_label': specialOfferDiscountVal,
                    }
                    var subject = orderPlacedSubject;
                    var message = orderPlacedMsg;
                    var scheduleTime = $('#scheduleTime').val();
                    var scheduleTime = "";
                    if ($('#scheduleTime').val() && $('#scheduleTime').val() != undefined) {
                        scheduleTime = new Date($('#scheduleTime').val());
                        subject = scheduleOrderPlacedSubject;
                        message = scheduleOrderPlacedMsg;
                    }
                    var now = new Date();
                    if (scheduleTime != '' && now.getTime() > scheduleTime.getTime()) {
                        Swal.fire({
                            text: "{{ trans('lang.invalid_schedule_current_time') }}",
                            icon: "error"
                        });
                        return false;
                    } else if (scheduleTime != '' && scheduleTime < new Date()) {
                        Swal.fire({
                            text: "{{ trans('lang.invalid_schedule_today_date') }}",
                            icon: "error"
                        });
                        return false;
                    }
                    //specialDiscount = object;
                }
            });
        });
    }
    //tip_amount
    $(document).on("click", '#Other_tip', function(event) {
        $("#tip_amount").val('');
        $("#add_tip_box").show();
    });
    $(document).on("click", '.this_tip', function(event) {
        var this_tip = $(this).val();
        var data = $(this);
        $("#tip_amount").val(this_tip);
        $("#add_tip_box").hide();
        if ((data).is('.tip_checked')) {
            data.removeClass('tip_checked');
            $(this).prop('checked', false);
            $("#tip_amount").val('');
            tipAmountChange('minus');
        } else {
            $(this).addClass('tip_checked');
            tipAmountChange('plus');
        }
    });
    $(document).on("onchange", '#tip_amount', function(event) {
        tipAmountChange();
    });
    $(document).on("change", '#scheduleTime', function(event) {
        var scheduleTime = $(this).val();
        $.ajax({
            type: 'POST',
            url: "<?php echo route('order-schedule-time-add'); ?>",
            data: {
                _token: '<?php echo csrf_token(); ?>',
                is_checkout: 1,
                scheduleTime: scheduleTime
            },
            success: function(data) {
                data = JSON.parse(data);
                $('#cart_list').html(data.html);
                loadcurrencynew();
                var today = new Date().toISOString().slice(0, 16);
                if (document.getElementsByName("scheduleTime").length > 0) {
                    document.getElementsByName("scheduleTime")[0].min = today;
                }
                getAdminCommission();
            }
        });
    });

    function tipAmountChange() {
        var this_tip = $("#tip_amount").val();
        $.ajax({
            type: 'POST',
            url: "<?php echo route('order-tip-add'); ?>",
            data: {
                _token: '<?php echo csrf_token(); ?>',
                is_checkout: 1,
                tip: this_tip
            },
            success: function(data) {
                data = JSON.parse(data);
                $('#cart_list').html(data.html);
                loadcurrencynew();
                var today = new Date().toISOString().slice(0, 16);
                if (document.getElementsByName("scheduleTime").length > 0) {
                    document.getElementsByName("scheduleTime")[0].min = today;
                }
                getAdminCommission();
            }
        });
    }

    function changeNote() {
        var addnote = $("#add-note").val();
        $.ajax({
            type: 'POST',
            url: "<?php echo route('add-cart-note'); ?>",
            data: {
                _token: '<?php echo csrf_token(); ?>',
                addnote: addnote
            },
            success: function(data) {}
        });
    }

    function noNegative(e) {
        // Prevent typing the minus key
        if (e.key === '-' || e.key === 'e') {
            e.preventDefault();
        }
    }
</script>
