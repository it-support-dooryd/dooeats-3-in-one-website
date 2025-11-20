@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.wallet_transaction_plural')}} <span class="userTitle"></span>
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.wallet_transaction_plural')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active" href="{!! url()->current() !!}"><i
                                        class="fa fa-list mr-2"></i>{{trans('lang.wallet_transaction_table')}}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                    <div class="title-head d-flex align-items-center mb-4 border-bottom pb-4 justify-content-between"> 
                        <h3 class="mb-0">{{trans('lang.wallet_transaction_table')}}</h3>
                        <div class="select-box pl-3">
                            <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;
                                <span></span>&nbsp; <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive m-t-10">
                        <table id="example24"
                            class="display nowrap table table-hover table-striped table-bordered table table-striped"
                            cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>{{trans('lang.amount')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.payment_methods')}}</th>
                                     <th>{{trans('lang.vendors_payout_note')}}</th>
                                    <th>{{trans('lang.payment_status')}}</th>
                                </tr>
                            </thead>
                            <tbody id="append_list1">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
@endsection
@section('scripts')
<script>
    var database = firebase.firestore();
    var offest = 1;
    var pagesize = 10;
    var end = null;
    var endarray = [];
    var start = null;
    var user_number = [];
    var vendorId="<?php echo $id;?>";
    var refData = database.collection('wallet').where('user_id','==',vendorId).orderBy('date','desc');
    var search = jQuery("#search").val();
    $(document.body).on('keyup', '#search', function () {
        search = jQuery(this).val();
    });
    var append_list = '';
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });
    function setDate() {
        $('#daterange span').html('{{trans("lang.select_date_range")}}');
        $('#daterange').daterangepicker({
            autoUpdateInput: false, 
        }, function (start, end) {
            $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('.filteredRecords').trigger('change'); 
        });
    }
    var inialRef = refData;
    async function filteredRecords() {
        var daterangepicker = $('#daterange').data('daterangepicker');
        refData = inialRef;
        if ($('#daterange span').html() != '{{trans("lang.select_date_range")}}' && daterangepicker) {
            var from = moment(daterangepicker.startDate).toDate();
            var to = moment(daterangepicker.endDate).toDate();
            if (from && to) { 
                var fromDate = firebase.firestore.Timestamp.fromDate(new Date(from));
                refData = refData.where('date', '>=', fromDate);
                var toDate = firebase.firestore.Timestamp.fromDate(new Date(to));
                refData = refData.where('date', '<=', toDate);
            }
        }
        $('#example24').DataTable().ajax.reload();
    };
    $(document).ready(function () {
        setDate();
        $('#daterange').on('apply.daterangepicker', function (ev, picker) {
            $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
            filteredRecords();
        });
        $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
            $('#daterange span').html('{{trans("lang.select_date_range")}}');
            filteredRecords();
        }); 
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        jQuery("#data-table_processing").show();
        $(document).on('click', '.dt-button-collection .dt-button', function () {
            $('.dt-button-collection').hide();
            $('.dt-button-background').hide();
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest('.dt-button-collection, .dt-buttons').length) {
                $('.dt-button-collection').hide();
                $('.dt-button-background').hide();
            }
        });
        var fieldConfig = {
            columns: [
                { key: 'transactionamount', header: '{{trans("lang.amount")}}' },
                { key: 'payment_method', header: '{{trans("lang.payment_methods")}}' },
                { key: 'payment_status', header: '{{trans("lang.payment_status")}}' },
                { key: 'note', header: '{{trans("lang.vendors_payout_note")}}' },
                { key: 'date', header: '{{trans("lang.date")}}' },
            ],
            fileName: '{{trans("lang.wallet_transaction_table")}}',
        };
        const table = $('#example24').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: async function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;
                const orderableColumns =  ['amount', 'date','payment_method', 'note','payment_status']; // Ensure this matches the actual column names
                const orderByField = orderableColumns[orderColumnIndex];
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }
                try{
                    const querySnapshot = await refData.get();
                    if (!querySnapshot || querySnapshot.empty) {
                        console.error("No data found in Firestore.");
                            $('#data-table_processing').hide(); // Hide loader
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                filteredData: [],
                                data: [] // No data
                            });
                            return;
                    }
                    let records = [];
                    let filteredRecords = [];
                    await Promise.all(querySnapshot.docs.map(async (doc) => {
                        let childData = doc.data();
                        childData.id = doc.id; // Ensure the document ID is included in the data
                        var date = '';
                        var time = '';
                        if (childData.hasOwnProperty("date") && childData.date != '') {
                            try {
                                date = childData.date.toDate().toDateString();
                                time = childData.date.toDate().toLocaleTimeString('en-US');
                            } catch (err) {
                            }
                        }
                        var Date = date + ' ' + time ;
                        childData.transactionamount = childData.amount;
                        if (!isNaN(childData.transactionamount)) {
                            childData.transactionamount = parseFloat(childData.transactionamount).toFixed(decimal_degits);
                        }
                        if ((childData.hasOwnProperty('isTopUp') && childData.isTopUp) || (childData.payment_method == "Cancelled Order Payment")) {
                            if (currencyAtRight) {
                                childData.transactionamount =  parseFloat(childData.transactionamount).toFixed(decimal_degits) + '' + currentCurrency;
                            } else {
                                childData.transactionamount =  currentCurrency + '' + parseFloat(childData.transactionamount).toFixed(decimal_degits);
                            }
                        } else if (childData.hasOwnProperty('isTopUp') && !childData.isTopUp) {
                            if (currencyAtRight) {
                                childData.transactionamount = '-' + parseFloat(childData.transactionamount).toFixed(decimal_degits) + '' + currentCurrency;
                            } else {
                                childData.transactionamount = '-' + currentCurrency + '' + parseFloat(childData.transactionamount).toFixed(decimal_degits);
                            }
                        } else {
                            if (currencyAtRight) {
                                childData.transactionamount =  parseFloat(childData.transactionamount).toFixed(decimal_degits) + '' + currentCurrency;
                            } else {
                                childData.transactionamount =  currentCurrency + '' + parseFloat(childData.transactionamount).toFixed(decimal_degits);
                            }
                        }
                        if (searchValue) {
                            if (
                                (childData.amount && childData.amount.toString().toLowerCase().includes(searchValue)) ||
                                (Date && Date.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                (childData.payment_method && childData.payment_method.toString().toLowerCase().includes(searchValue)) ||
                                (childData.note && childData.note.toString().toLowerCase().includes(searchValue)) ||
                                (childData.payment_status && childData.payment_status.toString().toLowerCase().includes(searchValue)) 
                            ) {
                                filteredRecords.push(childData);
                            }
                            } else {
                                filteredRecords.push(childData);
                        }
                    }));
                    filteredRecords.sort((a, b) => {
                        let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase().trim() : '';
                        let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase().trim() : '';
                            if (orderByField === 'date' && a[orderByField] != '' && b[orderByField] != '') {
                                try {
                                    aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                    bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                                } catch (err) {
                                }
                            }
                        if (orderByField === 'amount') {
                                aValue = a[orderByField] ? parseInt(a[orderByField]) : 0;
                                bValue = b[orderByField] ? parseInt(b[orderByField]) : 0;
                        }
                        if (orderDirection === 'asc') {
                            return (aValue > bValue) ? 1 : -1;
                        } else {
                            return (aValue < bValue) ? 1 : -1;
                        }
                    });
                    const totalRecords = filteredRecords.length;
                    const paginatedRecords = filteredRecords.slice(start, start + length);
                    const formattedRecords = await Promise.all(paginatedRecords.map(async (childData) => {
                        return await buildHTML(childData);
                    }));
                    console.log("Records fetched:", records.length);
                    $('#data-table_processing').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: totalRecords,
                        filteredData: filteredRecords,
                        data: formattedRecords
                    });
                } 
                catch (error) {
                    console.error("Error fetching data from Firestore:", error);
                    jQuery('#overlay').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        filteredData: [],
                        data: []
                    });
                }
            },
            columnDefs: [
                {
                    targets: 1,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
                {orderable: false, targets: [2, 4]},
            ],
            order: [['1', 'desc']],
            "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
            },
            dom: 'lfrtipB',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="mdi mdi-cloud-download"></i> Export as',
                    className: 'btn btn-info',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '{{trans("lang.export_excel")}}',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'excel',fieldConfig);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '{{trans("lang.export_pdf")}}',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'pdf',fieldConfig);
                            }
                        },   
                        {
                            extend: 'csvHtml5',
                            text: '{{trans("lang.export_csv")}}',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'csv',fieldConfig);
                            }
                        }
                    ]
                }
            ],
            initComplete: function() {
                $(".dataTables_filter").append($(".dt-buttons").detach());
                $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete','new-password').val('');
                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType === 3; 
                }).remove();
            }

        });
            
    });
    function debounce(func, wait) {
        let timeout;
        const context = this;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }
    async function buildHTML(val) {
        var html = [];
        var amount = '';
            amount = val.amount;
            if (!isNaN(amount)) {
                amount = parseFloat(amount).toFixed(decimal_degits);
            }
            if (val.hasOwnProperty('isTopUp') && val.isTopUp) {
                 note="{{trans('lang.order_amount_credited')}}";
                if (currencyAtRight) {
                    html.push('<td><span class="text-green">' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + '</span></td>');
                } else {
                    html.push('<td><span class="text-green">' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + '</span></td>');
                }
            } else if (val.hasOwnProperty('isTopUp') && !val.isTopUp) {
                    note="{{trans('lang.admin_commision_debited')}}"
                if (currencyAtRight) {
                    html.push('<td><span class="text-red">(' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + ')</span></td>');
                } else {
                    html.push('<td><span class="text-red">(' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + ')</span></td>');
                }
            } else {
                if (currencyAtRight) {
                    html.push('<td class="">' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + '</td>');
                } else {
                    html.push('<td class="">' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + '</td>');
                }
            }
            var date = "";
            var time = "";
            var dt= "";
            try {
                if (val.hasOwnProperty("date")) {
                    date = val.date.toDate().toDateString();
                    time = val.date.toDate().toLocaleTimeString('en-US');
                    dt = date + ' ' + time;
                }
            } catch (err) {
            }
            if(dt){
                html.push('<td>' + dt + '</td>');
            }
            else{
                html.push('<td></td>');
            }
            var payment_method = '';
            if (val.payment_method) {
                 if (val.payment_method == "Wallet" || val.payment_method=="wallet") {
                    image = '{{asset("images/foodie_wallet.png")}}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" >';
                 }else {
                    payment_method = val.payment_method;
                }
            }
            html.push('<td class="payment_images">' + payment_method + '</td>');
            if(val.note){
                html.push('<td>' + val.note + '</td>');
            }
            else
            {
                html.push('<td></td>');
            }
            if (val.payment_status == 'success') {
                html.push('<td class="success"><span>' + val.payment_status + '</span></td>');
            } else if (val.payment_status == 'undefined') {
                html.push('<td class="undefined"><span>' + val.payment_status + '</span></td>');
            } else if (val.payment_status == 'Refund success') {
                html.push('<td class="refund_success"><span>' + val.payment_status + '</span></td>');
            } else {
            }
        return html;
    }
</script>
@endsection