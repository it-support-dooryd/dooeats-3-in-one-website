<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"rel="stylesheet">
    <!-- @yield('style') -->
</head>

<body>

    <div class="page-wrapper pl-0 p-5">

        <div class="subscription-checkout">

            <div class="container position-relative">

                <div id="data-table_processing" class="page-overlay" style="display:none;">
                    <div class="overlay-text">
                        <img src="{{asset('images/spinner.gif')}}">
                    </div>
                </div>

                <div class="subscription-section">
                    <div class="subscription-section-inner">
                        <div class="card border">
                            <div class="card-header border-0">
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('subscription-plan.show') }}">
                                    <span class="mdi mdi-arrow-left mr-3 text-dark-2"></span>
                                    </a>
                                    <h6 class="text-dark-2 h6 mb-0">{{ trans('lang.shift_to_plan') }}</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row" id="plan-details"></div>
                                <div class="pay-method-section pt-4">
                                    <h6 class="text-dark-2 h6 mb-3 pb-3">{{ trans('lang.pay_via_online') }}</h6>
                                    <div class="row">

                                    </div>
                                </div>

                            </div>

                            <div class="card-footer border-top">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="text-dark-2 h6 mb-0 font-weight-semibold">{{ trans('lang.pay') }} <span id="subtotal"></span></h6>
                                    <div class="edit-form-group btm-btn text-right">
                                        <div class="card-block-active-plan d-none">
                                            <a href="{{ route('home') }}" class="btn btn-default rounded-full mr-2">{{ trans('lang.cancel_plan') }}</a>
                                            <button type="button" class="btn-primary btn rounded-full" onclick="finalCheckout()">{{ trans('lang.proceed_to_pay') }}</button>
                                        </div>
                                        <div class="card-block-new-plan d-none">
                                            <a href="{{ route('subscription-plan.show') }}" class="btn btn-default rounded-full mr-2">{{ trans('lang.cancel') }}</a>
                                            <button type="button" class="btn-primary btn rounded-full" onclick="finalCheckout()">{{ trans('lang.choose_plan') }}</button>
                                        </div>
                                        <div class="input-box">
                                            <input type="hidden" id="sub_total">
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

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

    <script type="text/javascript">
    
        jQuery('#data-table_processing').show();

        var database = firebase.firestore();
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var currencyData = '';
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        refCurrency.get().then(async function(snapshots) {
            currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });

        var wallet_amount = 0;
        var fcmToken = '';
        var id_order = database.collection('tmp').doc().id;
        var userId = "<?php echo $userId; ?>";
        var planId = "<?php echo $planId; ?>";
        var planData = '';
        var expiryDay = '';
        var vendorId=null;
        var commisionModel = false;
        var AdminCommission = '';
        var businessModel = database.collection('settings').doc("AdminCommission");
        businessModel.get().then(async function(snapshots) {
            var commissionSetting = snapshots.data();
            if (commissionSetting.isEnabled == true) {
                commisionModel = true;
            }
            if (commissionSetting.commissionType == "Percent") {
                AdminCommission = commissionSetting.fix_commission + '' + '%';
            } else {
                if (currencyAtRight) {
                    AdminCommission = commissionSetting.fix_commission.toFixed(decimal_degits) + currentCurrency;
                } else {
                    AdminCommission = currentCurrency + commissionSetting.fix_commission.toFixed(decimal_degits);
                }
            }
        });
        database.collection('users').where('id','==',userId).get().then(async function(snapshot) {
                var userData=snapshot.docs[0].data();
                if(userData.hasOwnProperty('vendorID')&&userData.vendorID!=''&&userData.vendorID!=null) {
                    vendorId=userData.vendorID;
                }
            });
        var activeSubscriptionData = '';
        var activeSubscriptionId = '';
        var subscriptionHistory = database.collection('subscription_history').where('user_id', '==', userId).orderBy('createdAt', 'desc');
        subscriptionHistory.get().then(async function(snapshot) {
            if (snapshot.docs.length > 0) {
                var data = snapshot.docs[0].data();
                activeSubscriptionId = data.subscription_plan.id;
                activeSubscriptionData = data.subscription_plan;                
            }
        });

        var planRef = database.collection('subscription_plans').where('id', '==', planId);
        planRef.get().then(async function(snapshot) {
            planData = snapshot.docs[0].data();
            if(planData.expiryDay!='-1') {
            var currentDate = new Date();
            currentDate.setDate(currentDate.getDate() + parseInt(planData.expiryDay));
            expiryDay = firebase.firestore.Timestamp.fromDate(currentDate);
            }else{
                expiryDay=null
            }
            if (currencyAtRight) {
                var html = parseFloat(planData.price).toFixed(decimal_degits) + currentCurrency;
            } else {
                var html = currentCurrency + parseFloat(planData.price).toFixed(decimal_degits);
            }
            $('#subtotal').html(html);
            $('#sub_total').val(planData.price);
            showPlanDetail(activeSubscriptionData, planData);
        });

        async function showPlanDetail(activePlan='', choosedPlan) {
            
            let html = '';
            
            let choosedPlan_price = currencyAtRight ? parseFloat(choosedPlan.price).toFixed(decimal_degits) + currentCurrency
            : currentCurrency + parseFloat(choosedPlan.price).toFixed(decimal_degits);

            if(activePlan){

                $(".card-block-active-plan").removeClass('d-none');

                let activePlan_price = currencyAtRight ? parseFloat(activePlan.price).toFixed(decimal_degits) + currentCurrency
                : currentCurrency + parseFloat(activePlan.price).toFixed(decimal_degits);

                html += ` 
                <div class="col-md-8">
                    <div class="subscription-card-left"> 
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <div class="subscription-card text-center">
                                    <div class="d-flex align-items-center pb-3 justify-content-center">
                                        <span class="pricing-card-icon mr-4"><img src="${activePlan.image}"></span>
                                        <h2 class="text-dark-2 mb-0 font-weight-semibold">${activePlan.id == "J0RwvxCWhZzQQD7Kc2Ll" ? "{{trans('lang.commission')}}" : activePlan.name}</h2>
                                    </div>
                                    <h3 class="text-dark-2">${activePlan.id == "J0RwvxCWhZzQQD7Kc2Ll" ? AdminCommission + " {{trans('lang.plan')}}" : activePlan_price}</h3>
                                    <p>${activePlan.id == "J0RwvxCWhZzQQD7Kc2Ll" ? "{{ trans('lang.free') }}" : activePlan.expiryDay==-1? "{{ trans('lang.unlimited') }}": activePlan.expiryDay + "{{trans('lang.days')}}" }   </p>
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <img src="{{asset('images/left-right-arrow.png')}}">
                            </div>
                            <div class="col-md-5">
                                <div class="subscription-card text-center">
                                    <div class="d-flex align-items-center pb-3 justify-content-center">
                                        <span class="pricing-card-icon mr-4"><img src="${choosedPlan.image}"></span>
                                        <h2 class="text-dark-2 mb-0 font-weight-semibold">${choosedPlan.name}
                                        </h2>
                                    </div>
                                    <h3 class="text-dark-2">${choosedPlan_price}</h3>
                                    <p>${choosedPlan.expiryDay==-1 ? "{{ trans('lang.unlimited') }}" : choosedPlan.expiryDay} {{trans('lang.days')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="subscription-card-right">
                        <div
                            class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                            <span class="font-weight-medium">Validity</span>
                            <span class="font-weight-semibold">${choosedPlan.expiryDay==-1 ? "{{ trans('lang.unlimited') }}" : choosedPlan.expiryDay} {{trans('lang.days')}}</span>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                            <span class="font-weight-medium">Price</span>
                            <span class="font-weight-semibold">${choosedPlan_price}</span>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                            <span class="font-weight-medium">{{trans("lang.bill_status")}}</span>
                            <span class="font-weight-semibold">{{trans("lang.migrate_to_new_plan")}}</span>
                        </div>
                    </div>
                </div>`;

            }else{

                $(".card-block-new-plan").removeClass('d-none');

                html += ` 
                <div class="col-md-8">
                    <div class="subscription-card-left"> 
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="subscription-card text-center">
                                    <div class="d-flex align-items-center pb-3 justify-content-center">
                                        <span class="pricing-card-icon mr-4"><img src="${choosedPlan.image}"></span>
                                        <h2 class="text-dark-2 mb-0 font-weight-semibold">${choosedPlan.name}
                                        </h2>
                                    </div>
                                    <h3 class="text-dark-2">${choosedPlan_price}</h3>
                                    <p>${choosedPlan.expiryDay==-1 ? "{{ trans('lang.unlimited') }}" : choosedPlan.expiryDay} {{trans('lang.days')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="subscription-card-right">
                        <div
                            class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                            <span class="font-weight-medium">Validity</span>
                            <span class="font-weight-semibold">${choosedPlan.expiryDay==-1 ? "{{ trans('lang.unlimited') }}" : choosedPlan.expiryDay} {{trans('lang.days')}}</span>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                            <span class="font-weight-medium">Price</span>
                            <span class="font-weight-semibold">${choosedPlan_price}</span>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                            <span class="font-weight-medium">{{trans("lang.bill_status")}}</span>
                            <span class="font-weight-semibold">{{trans("lang.migrate_to_new_plan")}}</span>
                        </div>
                    </div>
                </div>`;
            }
            $("#plan-details").html(html);
            jQuery('#data-table_processing').hide();
        }

        var userDetailsRef = database.collection('users').where('id', "==", userId);
        var uservendorDetailsRef = database.collection('users');
        var payStackSettings = database.collection('settings').doc('payStack');

        var authorName = '';
        var authorEmail = '';

        $(document).ready(function() {
            var today = new Date().toISOString().slice(0, 16);
            getUserDetails();
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
                    if(isEnable){
                        $("#paystack_box").removeClass('d-none');
                    }
                }
            });

            userDetailsRef.get().then(async function(userSnapshots) {
                var userDetails = userSnapshots.docs[0].data();
                authorName = userDetails.firstName + ' ' + userDetails.lastName;
                authorEmail = userDetails.email;
            });
        }

        async function finalCheckout() {

            var payment_method = $('input[name="payment_method"]:checked').val();
            if (payment_method == false || payment_method == undefined || payment_method == '') {
                alert("{{ trans('lang.select_payment_option') }}");
                return false;
            }

            var total_pay = $('#sub_total').val();
            if (total_pay == 0 || total_pay == '' || total_pay == "0") {
                return false;
            }

            var createdAt = firebase.firestore.FieldValue.serverTimestamp();
            var now = new Date();
            var order_json = {
                id: id_order,
                userId: userId,
                planId: planId,
                authorName: authorName,
                authorEmail: authorEmail
            };

            if (payment_method == "paystack") {
                var paystack_public_key = $("#paystack_public_key").val();
                var paystack_secret_key = $("#paystack_secret_key").val();
                var paystack_isSandbox = $("#paystack_isSandbox").val();

                $.ajax({
                    type: 'POST',
                    url: "<?php echo route('payment-proccessing'); ?>",
                    data: {
                        _token: '<?php echo csrf_token(); ?>',
                        order_json: order_json,
                        payment_method: payment_method,
                        total_pay: total_pay,
                        paystack_isSandbox: paystack_isSandbox,
                        paystack_public_key: paystack_public_key,
                        paystack_secret_key: paystack_secret_key,
                        currencyData: currencyData
                    },
                    success: function(data) {
                        data = JSON.parse(data);
                        $('#cart_list').html(data.html);
                        window.location.href = "<?php echo route('pay-subscription'); ?>";
                    }
                });
            }
        }
    </script>

</body>
