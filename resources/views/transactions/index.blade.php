@include('layouts.app')
@include('layouts.header')
<div class="siddhi-popular">
    <div class="container">
        <div class="transactions-banner p-4 rounded">
            <div class="row align-items-center text-center">
                <div class="col-md-12">
                    <h3 class="font-weight-bold h4 text-light" id="total_payment"></h3>
                </div>
                <div class="col-md-12">

                    <button class="btn btn-light">

                        <a data-toggle="modal" data-target="#add_wallet_money"

                            class="d-flex w-100 align-items-center border-bottom p-3">

                            <div class="left mr-3 text-green">

                                <h6 class="font-weight-bold mb-1 text-dark">{{trans('lang.add_wallet_money')}}</h6>

                            </div>

                            <div class="right ml-auto">

                                <h6 class="font-weight-bold m-0"><i class="feather-chevron-right"></i></h6>

                            </div>

                        </a>

                    </button>

                    </div>
            </div>
        </div>
        <div class="text-center py-5 not_found_div" style="display:none">
            <p class="h4 mb-4"><i class="feather-search bg-primary rounded p-2"></i></p>
            <p class="font-weight-bold text-dark h5">{{trans('lang.nothing_found')}}</p>
            <p>{{trans('lang.please_try_again')}}</p>
        </div>
        <div id="append_list1" class="res-search-list"></div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="container mt-4 mb-4 p-0">
                    <div class="data-table_paginate">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                <li class="page-item ">
                                    <a class="page-link" href="javascript:void(0);" id="users_table_previous_btn"
                                       onclick="prev()" data-dt-idx="0" tabindex="0">{{trans('lang.previous')}}</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0);" id="users_table_next_btn"
                                       onclick="next()" data-dt-idx="2" tabindex="0">{{trans('lang.next')}}</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add_wallet_money" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('lang.add_wallet_money') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="col-md-12 error_top_pass"></div>
                    <div class="col-md-12 form-group">
                        <label class="form-label">{{ trans('lang.amount') }}</label>
                        <div class="control-inner">
                            <!-- <div class="currentCurrency"></div> -->
                            <input type="number" class="form-control wallet_amount">
                        </div>
                    </div>
                    <div class="accordion col-md-12 mb-3 rounded shadow-sm bg-white border" id="accordionExample">
                        <div class="siddhi-card overflow-hidden checkout-payment-options">
                            <!-- Paystack -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="paystack_box">
                                <input type="radio" name="payment_method" id="paystack" value="paystack" class="custom-control-input" checked>
                                <label class="custom-control-label" for="paystack">{{ trans('lang.pay_stack') }}</label>
                                <input type="hidden" id="paystack_isEnabled">
                                <input type="hidden" id="paystack_isSandbox">
                                <input type="hidden" id="paystack_public_key">
                                <input type="hidden" id="paystack_secret_key">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-0 border-0">
                <div class="col-6 m-0 p-0">
                    <button type="button" class="btn border-top btn-lg btn-block" data-dismiss="modal">
                        {{ trans('lang.close') }}
                    </button>
                </div>
                <div class="col-6 m-0 p-0">
                    <button type="button" onclick="finalCheckout()" class="btn btn-primary btn-lg btn-block change_user_password remove_hover">
                        {{ trans('lang.next') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
@include('layouts.nav')
<script type="text/javascript">
    var ref = database.collection('wallet').where('user_id', '==', user_uuid).orderBy('date', 'desc');
    var pagesize = 10;
    var offest = 1;
    var end = null;
    var endarray = [];
    var start = null;
    var append_list = '';
    var totalPayment = 0;
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_digits = 0;
    var currencyData = '';
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        if(snapshots.docs.length > 0){
            currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_digits = currencyData.decimal_degits;
            }
            $('.currentCurrency').html(currentCurrency);
        }
    });

    var refMinDeposite = database.collection('settings').doc('globalSettings');
    var minimumAmountToDeposit = 0;

    var PaytmSettings = database.collection('settings').doc('PaytmSettings');
    var flutterWave = database.collection('settings').doc('flutterWave');
    var midtrans_settings = database.collection('settings').doc('midtrans_settings');
    var orange_money_settings = database.collection('settings').doc('orange_money_settings');
    var payFastSettings = database.collection('settings').doc('payFastSettings');
    var payStack = database.collection('settings').doc('payStack');
    var paypalSettings = database.collection('settings').doc('paypalSettings');
    var razorpaySettings = database.collection('settings').doc('razorpaySettings');

    var xendit_settings= database.collection('settings').doc('xendit_settings');    

    $(document).ready(async function () {
        getPaymentGateways();
        $("#data-table_processing").show();
        var totalPayment = 0;
        await refMinDeposite.get().then(async function (snapshot) {
            var data = snapshot.data();
            if (data.hasOwnProperty('minimumAmountToDeposit') && data.minimumAmountToDeposit != null && data.minimumAmountToDeposit != '') {
                minimumAmountToDeposit = data.minimumAmountToDeposit;
            }
        });
        await database.collection('users').where("id", "==", user_uuid).get().then(
            (amountsnapshot) => {
                var paymentDatas = amountsnapshot.docs[0].data();
                if (paymentDatas.hasOwnProperty('wallet_amount') && paymentDatas.wallet_amount != null && !isNaN(paymentDatas.wallet_amount)) {
                    totalPayment = parseFloat(paymentDatas.wallet_amount);
                }
                if (currencyAtRight) {
                    totalPayment = totalPayment.toFixed(decimal_digits) + '' + currentCurrency;
                } else {
                    totalPayment = currentCurrency + '' + totalPayment.toFixed(decimal_digits);
                }
        });

        jQuery("#total_payment").html('{{trans("lang.total")}} {{trans("lang.Payment")}} : ' + totalPayment);
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        ref.limit(pagesize).get().then(async function (snapshots) {
            if (snapshots != undefined) {
                var html = '';
                html = buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);
                    $("#data-table_processing").hide();
                }
            }
        });
    });
    function getPaymentGateways() {
        // PayStack Settings
        payStack.get().then(async function (payStackSettingsSnapshots) {
            let payStackSetting = payStackSettingsSnapshots.data();

            if (payStackSetting.isEnable) {
                $("#paystack_isEnabled").val(payStackSetting.isEnable);
                $("#paystack_isSandbox").val(payStackSetting.isSandbox);
                $("#paystack_public_key").val(payStackSetting.publicKey);
                $("#paystack_secret_key").val(payStackSetting.secretKey);
                $("#paystack_box").show();
            }
        });
    }

    function buildHTML(snapshots) {
        var html = '';
        var alldata = [];
        var number = [];
        var vendorIDS = [];
        snapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
        alldata.forEach((listval) => {
            var val = listval;
            var date = val.date.toDate().toDateString();
            var time = val.date.toDate().toLocaleTimeString('en-US');
            var price_val = '';
            if (currencyAtRight) {
                price_val = parseFloat(val.amount).toFixed(decimal_digits) + '' + currentCurrency;
            } else {
                price_val = currentCurrency + '' + parseFloat(val.amount).toFixed(decimal_digits);
            }
            html = html + '<div class="transactions-list-wrap mt-4"><div class="bg-white px-4 py-3 border rounded-lg mb-3 transactions-list-view shadow-sm"><div class="gold-members d-flex align-items-center transactions-list">';
            var desc = '';
            if ((val.hasOwnProperty('isTopUp') && val.isTopUp) || (val.payment_method == "Cancelled Order Payment")) {
                price_val = '<div class="float-right ml-auto"><span class="price font-weight-bold h4">+ ' + price_val + '</span>';
                desc = "Wallet Topup";
            } else if (val.hasOwnProperty('isTopUp') && !val.isTopUp) {
                price_val = '<div class="float-right ml-auto"><span class="font-weight-bold h4" style="color: red">- ' + price_val + '</span>';
                desc = "Wallet Amount Deducted";
            } else {
                price_val = '<div class="float-right ml-auto"><span class="font-weight-bold h4">' + price_val + '</span>';
            }
            html = html + '<div class="media transactions-list-left"><div class="mr-3 font-weight-bold card-icon"><span><i class="fa fa-credit-card"></i></span></div><div class="media-body"><h6 class="date">' + desc + '</h6><h6>' + date + ' ' + time + '</h6><p class="text-muted mb-0">' + val.payment_method + '</p><p class="mb-0 badge badge-success text-light">' + val.payment_status + '</p></div></div>';
            html = html + price_val;
            if (val.hasOwnProperty('order_id') && val.order_id != "" && val.order_id != null) {
                if(val.status == "Order Completed"){
                    var view_details = "{{ route('completed_order',':id')}}";
                    view_details = view_details.replace(':id', 'id=' + val.order_id);
                    html = html + '<a href="' + view_details + '"><span class="go-arror text-dark btn-block text-right mt-2"><i class="fa fa-angle-right"></i></span></a>';
                }
                
            }
            html = html + '</div> </div></div></div>';
        });
        return html;
    }
    async function next() {
        if (start != undefined || start != null) {
            jQuery("#data-table_processing").hide();
            listener = ref.startAfter(start).limit(pagesize).get();
            listener.then(async (snapshots) => {
                html = '';
                html = await buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    if (endarray.indexOf(snapshots.docs[0]) != -1) {
                        endarray.splice(endarray.indexOf(snapshots.docs[0]), 1);
                    }
                    endarray.push(snapshots.docs[0]);
                }
            });
        }
    }
    async function prev() {
        if (endarray.length == 1) {
            return false;
        }
        end = endarray[endarray.length - 2];
        if (end != undefined || end != null) {
            jQuery("#data-table_processing").show();
            listener = ref.startAt(end).limit(pagesize).get();
            listener.then(async (snapshots) => {
                html = '';
                html = await buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.splice(endarray.indexOf(endarray[endarray.length - 1]), 1);
                    if (snapshots.docs.length < pagesize) {
                        jQuery("#users_table_previous_btn").hide();
                    }
                }
            });
        }
    }

    async function finalCheckout() {
        var amount = parseFloat($('.wallet_amount').val());
        
        if (amount < 0 || amount == 0 || amount == null || amount == '' || isNaN(amount)) {
            amount = 0;
            amount = currencyAtRight
                ? `${parseFloat(amount).toFixed(decimal_digits)}${currentCurrency}`
                : `${currentCurrency}${parseFloat(amount).toFixed(decimal_digits)}`;
            Swal.fire({ text: `{{trans('lang.min_deposite_amount_err')}}`, icon: "error" });
            return false;
        }

        const userSnapshots = await database.collection('users').where("id", "==", user_uuid).get();
        const userDetails = userSnapshots.docs[0]?.data();
        const userDetailsJSON = JSON.stringify(userDetails);
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        const routeUrl = "<?php echo route('wallet-proccessing'); ?>";
        const redirectUrl = "<?php echo route('pay-wallet'); ?>";

        if (!paymentMethod) return Swal.fire({ text: "Please select a payment method.", icon: "error" });

        const ajaxData = {
            _token: '<?php echo csrf_token(); ?>',
            amount,
            payment_method: paymentMethod,
            currencyData: currencyData,
            author: userDetailsJSON 
        };

        if (paymentMethod === "paystack") {
            Object.assign(ajaxData, {
                paystack_public_key: $("#paystack_public_key").val(),
                paystack_secret_key: $("#paystack_secret_key").val(),
                paystack_isSandbox: $("#paystack_isSandbox").val(),
                email: userDetails.email
            });
        } else return;

        $.ajax({
            type: 'POST',
            url: routeUrl,
            data: ajaxData,
            success: () => window.location.href = redirectUrl,
            error: (error) => Swal.fire({ text: "Payment processing failed.", icon: "error" })
        });
    }

</script>