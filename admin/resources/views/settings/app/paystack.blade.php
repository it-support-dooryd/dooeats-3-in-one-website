@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="card">
            <div class="payment-top-tab mt-3 mb-3">
                <ul class="nav nav-tabs card-header-tabs align-items-end">
                    <li class="nav-item">
                        <a class="nav-link active paystack_active_label"
                           href="{!! url('settings/payment/paystack') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_paystack_lable')}}<span
                                    class="badge ml-2"></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.app_setting_paystack')}}</legend>
                            <div class="form-check width-100">
                                <input type="checkbox" class=" enable_paystack" id="enable_paystack">
                                <label class="col-3 control-label"
                                       for="enable_paystack">{{trans('lang.app_setting_enable_paystack')}}</label>
                            </div>
                            <div class="form-check width-100">
                                <input type="checkbox" class="sand_box_mode" id="sand_box_mode">
                                <label class="col-3 control-label"
                                       for="sand_box_mode">{{trans('lang.app_setting_enable_sandbox_mode')}}</label>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.app_setting_secretKey')}}</label>
                                <div class="col-7">
                                    <input type="password" class="form-control secretKey">
                                    <div class="form-text text-muted">
                                        {!! trans('lang.app_setting_secretKey_help') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.app_setting_publicKey')}}</label>
                                <div class="col-7">
                                    <input type="password" class=" form-control publicKey">
                                    <div class="form-text text-muted">
                                        {!! trans('lang.app_setting_publicKey_help') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.app_setting_callbackURL')}}</label>
                                <div class="col-7">
                                    <input type="text" class=" form-control callbackURL">
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.app_setting_webhookURL')}}</label>
                                <div class="col-7">
                                    <input type="text" class=" form-control webhookURL">
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>{{trans('lang.withdraw_setting')}}</legend>
                            <div class="form-check width-100">
                                <div class="form-text text-muted">
                                    {!! trans('lang.withdraw_setting_not_available_help') !!}
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary edit-setting-btn"><i
                            class="fa fa-save"></i> {{trans('lang.save')}}</button>
                <a href="{{url('/dashboard')}}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var database = firebase.firestore();
        var ref = database.collection('settings').doc('payStack');
        $(document).ready(function () {
            jQuery("#data-table_processing").show();
            ref.get().then(async function (snapshots) {
                var paystack = snapshots.data();
                if (paystack == undefined) {
                    database.collection('settings').doc('payStack').set({});
                }
                try {
                    if (paystack.isEnable) {
                        $(".enable_paystack").prop('checked', true);
                        jQuery(".paystack_active_label span").addClass('badge-success');
                        jQuery(".paystack_active_label span").text('Active');
                    }
                    if (paystack.isSandbox) {
                        $(".sand_box_mode").prop('checked', true);
                    }
                    $(".secretKey").val(paystack.secretKey);
                    $(".publicKey").val(paystack.publicKey);
                    $(".callbackURL").val(paystack.callbackURL);
                    $(".webhookURL").val(paystack.webhookURL);
                } catch (error) {
                }
                jQuery("#data-table_processing").hide();
            })
            $(".edit-setting-btn").click(function () {
                var secretKey = $(".secretKey").val();
                var publicKey = $(".publicKey").val();
                var isEnable = $(".enable_paystack").is(":checked");
                var isSandbox = $(".sand_box_mode").is(":checked");
                var callbackURL = $(".callbackURL").val();
                var webhookURL = $(".webhookURL").val();
                database.collection('settings').doc("payStack").update({
                    'secretKey': secretKey,
                    'publicKey': publicKey,
                    'isEnable': isEnable,
                    'isSandbox': isSandbox,
                    'callbackURL': callbackURL,
                    'webhookURL': webhookURL
                }).then(function (result) {
                    window.location.href = '{{ url("settings/payment/paystack")}}';
                });
            })
        })
    </script>
@endsection
