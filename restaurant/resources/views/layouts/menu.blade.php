<nav class="sidebar-nav">
    <ul id="sidebarnav">
        <li><a class="waves-effect waves-dark" href="{!! url('dashboard') !!}" aria-expanded="false">
                <i class="mdi mdi-home"></i>
                <span class="hide-menu">{{ trans('lang.dashboard') }}</span>
            </a>
        </li>
    </ul>
    <p class="web_version"></p>
</nav>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
<script src="{{ asset('js/geofirestore.js') }}"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>

<script type="text/javascript">
    var database = firebase.firestore();
    var vendorUserId = "<?php echo $id; ?>";
    var documentVerificationEnable = false;
    var commisionModel = false;
    var subscriptionModel = false;
    var vendorId = null;
    var dineIn = false;
    var enableAdvertisement = false;
    var enableSelfDelivery = false;

    if (vendorUserId) {
        database.collection('settings').doc("DineinForRestaurant").get().then(async function(
            settingSnapshots) {
            if (settingSnapshots.data()) {
                var settingData = settingSnapshots.data();
                if (settingData.isEnabled) {
                    dineIn = true;
                }
            }
        })
        database.collection('settings').doc("globalSettings").get().then(async function(
            settingSnapshots) {
            if (settingSnapshots.data()) {
                var settingData = settingSnapshots.data();
                if (settingData.isEnableAdsFeature) {
                    enableAdvertisement = true;
                }
                if (settingData.isSelfDelivery) {
                    enableSelfDelivery = true;
                }
            }
        })
    }


    var commissionBusinessModel = database.collection('settings').doc("AdminCommission");
    commissionBusinessModel.get().then(async function(snapshots) {
        var commissionSetting = snapshots.data();
        if (commissionSetting.isEnabled == true) {
            commisionModel = true;
        }
        document.dispatchEvent(new Event('commissionModelReady'));
    });

    var subscriptionBusinessModel = database.collection('settings').doc("restaurant");
    subscriptionBusinessModel.get().then(async function(snapshots) {
        var subscriptionSetting = snapshots.data();
        if (subscriptionSetting.subscription_model == true) {
            subscriptionModel = true;
        }
    });
    database.collection('settings').doc("document_verification_settings").get().then(async function(snapshots) {
        var documentVerification = snapshots.data();
        if (documentVerification.isRestaurantVerification) {
            documentVerificationEnable = true;
            var newLi = `
                <li>
                <a class="waves-effect waves-dark" href="{!! url('document-list') !!}" aria-expanded="false">
                    <i class="mdi mdi-file-document"></i>
                    <span class="hide-menu">{{ trans('lang.document_plural') }}</span>
                </a>
            </li>`;
            $('#sidebarnav').append(newLi);

        }
    })

    database.collection('users').where('id', '==', vendorUserId).get().then(async function(snapshots) {
        var userData = snapshots.docs[0].data();
        var checkVendor = null;
        if (userData.hasOwnProperty('vendorID') && userData.vendorID != '' && userData.vendorID != null) {
            vendorId = userData.vendorID;
            checkVendor = userData.vendorID;
        }
        var newLi = '';
        if (subscriptionModel == true || commisionModel == true) {
            newLi += `<li>
                            <a class="waves-effect waves-dark" href="{!! route('subscription-plan.show') !!}" aria-expanded="false">
                                <i class="mdi mdi-crown"></i>
                                <span class="hide-menu">{{ trans('lang.change_subscription') }}</span>
                            </a>
                        </li>`;

        }
        newLi += `<li>
                <a class="waves-effect waves-dark" href="{!! url('my-subscriptions') !!}" aria-expanded="false">
                    <i class="mdi mdi-wallet-membership"></i>
                    <span class="hide-menu">{{ trans('lang.my_subscriptions') }}</span>
                </a>
            </li>`;
        if ((userData.hasOwnProperty('isDocumentVerify') && userData.isDocumentVerify == true) || documentVerificationEnable == false) {
            newLi += `
           <li>
            <a class="waves-effect waves-dark" href="{!! url('restaurant') !!}" aria-expanded="false">
                <i class="mdi mdi-store"></i>
                <span class="hide-menu">{{ trans('lang.myrestaurant_plural') }}</span>
            </a>
        </li>`;

            if (checkVendor != null) {
                newLi += `
            <li>
            <a class="waves-effect waves-dark" href="{!! url('foods') !!}" aria-expanded="false">
                <i class="mdi mdi-food"></i>
                <span class="hide-menu">{{ trans('lang.food_plural') }}</span>
            </a>
        </li>
          <li><a class="has-arrow waves-effect waves-dark" href="#"
                 data-toggle="collapse" data-target="#orderDropdown">
                <i class="mdi mdi-reorder-horizontal"></i>
                <span class="hide-menu">{{ trans('lang.order_plural') }}</span>
            </a>
            <ul id="orderDropdown" aria-expanded="false" class="collapse">
                <li><a href="{!! url('orders') !!}">{{ trans('lang.order_plural') }}</a></li>
                <li><a href="{!! url('placedOrders') !!}">{{ trans('lang.placed_orders') }}</a></li>
                <li><a href="{!! url('acceptedOrders') !!}">{{ trans('lang.accepted_orders') }}</a></li>
                <li><a href="{!! url('rejectedOrders') !!}">{{ trans('lang.rejected_orders') }}</a></li>

            </ul>
        </li> `;
                if (dineIn) {
                    newLi +=
                        `<li>
                    <a class="waves-effect waves-dark"
                        href="{!! url('booktable') !!}" aria-expanded="false">
                        <i class="fa fa-table "></i>
                        <span class="hide-menu">{{ trans('lang.book_table') }} / DINE IN History</span>
                    </a>
                </li>`;

                }
                newLi += `<li><a class="waves-effect waves-dark"
                href="{!! url('coupons') !!}" aria-expanded="false">
                <i class="mdi mdi-sale"></i>
                <span class="hide-menu">{{ trans('lang.coupon_plural') }}</span>
            </a>
        </li>`;
                if (enableAdvertisement) {
                    newLi += `<li><a class="has-arrow waves-effect waves-dark" href="#"
                 data-toggle="collapse" data-target="#adsDropdown">
                <i class="mdi mdi-newspaper"></i>
                <span class="hide-menu">{{ trans('lang.advertisement_plural') }}</span>
            </a>
            <ul id="adsDropdown" aria-expanded="false" class="collapse">
                <li><a href="{!! url('advertisements/pending') !!}">{{ trans('lang.pending') }}</a></li>
                <li><a href="{!! url('advertisements') !!}">{{ trans('lang.ads_list') }}</a></li>
            </ul>
          </li>`;
                }
                if (enableSelfDelivery) {
                    newLi += `<li><a class="waves-effect waves-dark"
                href="{!! url('deliveryman') !!}" aria-expanded="false">
                <i class="mdi mdi-run"></i>
                <span class="hide-menu">{{ trans('lang.delivery_man') }}</span>
            </a>
        </li>`;
                }

                newLi += `<li><a class="waves-effect waves-dark" href="{!! url('payments') !!}"
                aria-expanded="false">
                <i class="mdi mdi-wallet"></i>
                <span class="hide-menu">{{ trans('lang.payment_plural') }}</span>
            </a>
        </li>`;
            }
            newLi += `<li><a class=" waves-effect waves-dark" href="{!! url('withdraw-method') !!}"
                aria-expanded="false">
                <i class="fa fa-credit-card "></i>
                <span class="hide-menu">{{ trans('lang.withdrawal_method') }}</span>
            </a>
        </li>
       
        <li><a class="waves-effect waves-dark"
                href="{!! url('wallettransaction') !!}" aria-expanded="false">
                <i class="mdi mdi-swap-horizontal"></i>
                <span class="hide-menu">{{ trans('lang.wallet_transaction_plural') }}</span>
            </a>
        </li>`

        if(enableAdvertisement){
        newLi+=`<li class="waves-effect waves-dark p-2">
                        <div class="promo-card">
                            <div class="position-relative">
                                <img src="{{asset('images/advertisement_promo.png')}}" class="mw-100" alt="">
                                <h4 class="mb-2 mt-3">Want to get highlighted?</h4>
                                <p class="mb-4">
                                    Create ads to get highlighted on the app and web browser
                                </p>
                                <a href="{{route('advertisements.create')}}" class="btn btn-primary">Create Ads</a>
                            </div>
                        </div>
                    </li>`
        }

        }
        $('#sidebarnav').append(newLi);


    });

    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
</script>
