@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.accepted_orders')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item "><a href="{{url('orders')}}">{{trans('lang.order_plural')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.accepted_orders')}}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="orderTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                        <th class="delete-all"><input type="checkbox" id="is_active"><label
                                            class="col-3 control-label" for="is_active">
                                            <a id="deleteAll" class="do_not_delete" href="javascript:void(0)">
                                            <i class="fa fa-trash"></i> {{trans('lang.all')}}</a></label></th>
                                            <th>{{trans('lang.order_id')}}</th>
                                            <th>{{trans('lang.order_user_id')}}</th>
                                            <th>{{trans('lang.amount')}}</th>
                                            <th>{{trans('lang.order_type')}}</th>
                                            <th>{{trans('lang.date')}}</th>   
                                            <th>{{trans('lang.actions')}}</th>
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
<script type="text/javascript">
var database = firebase.firestore();
var offest=1;
var pagesize=50; 
var end = null;
var endarray=[];
var start = null;
var user_id = "<?php echo $id; ?>";
var refData = database.collection('restaurant_orders').where('vendor.author',"==",user_id).where('status','==','Order Accepted');
var ref = database.collection('restaurant_orders').orderBy('createdAt', 'desc').where('vendor.author',"==",user_id).where('status','==','Order Accepted');
var append_list = '';
var user_number = [];
var currentCurrency ='';
var currencyAtRight = false;
var decimal_degits = 0;
var refCurrency = database.collection('currencies').where('isActive', '==' , true);
refCurrency.get().then( async function(snapshots){
    var currencyData = snapshots.docs[0].data();
    currentCurrency = currencyData.symbol;
    currencyAtRight = currencyData.symbolAtRight;
    if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
});
$(document).ready(function() {
    $(document.body).on('click', '.redirecttopage' ,function(){    
        var url=$(this).attr('data-url');
        window.location.href = url;
    });
    jQuery("#data-table_processing").show();  
            const table = $('#orderTable').DataTable({
                pageLength: 10,
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: async function (data, callback, settings) {
                    const start = data.start;
                    const length = data.length;
                    const searchValue = data.search.value.toLowerCase();
                    const orderColumnIndex = data.order[0].column;
                    const orderDirection = data.order[0].dir;
                    const orderableColumns = ['','id','client','price','takeAway','createdAt',''];
                    const orderByField = orderableColumns[orderColumnIndex];
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    try {
                        const querySnapshot = await ref.get();
                        if (!querySnapshot || querySnapshot.empty) {
                            $('#data-table_processing').hide();
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                data: []
                            });
                            return;
                        }
                        let records = [];
                        let filteredRecords = [];
                        await Promise.all(querySnapshot.docs.map(async (doc) => {
                            let childData = doc.data();
                            childData.id = doc.id;
                            if(childData.hasOwnProperty('author') && childData.author != null && childData.author != ''){
                                childData.afirstName = childData.author.firstName;
                                childData.alastName = childData.author.lastName;
                            }
                            else{
                                childData.afirstName = '';
                                childData.alastName = '';
                            }
                            var client = '';
                            if (childData.afirstName != ' ' || childData.alastName != '') {
                                client = childData.afirstName + ' ' + childData.alastName;
                            }
                            childData.client = client ? client : ' ';
                            var price = 0;
                            childData.price =  buildHTMLProductstotal(childData);
                            var takeAway = '';
                            if (childData.hasOwnProperty('takeAway') && childData.takeAway) {
                                takeAway = '<td>{{trans("lang.order_takeaway")}}</td>';
                            } else {
                                takeAway =  '<td>{{trans("lang.order_delivery")}}</td>';
                            }
                            childData.takeAway = takeAway ? takeAway : ' ';
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("createdAt") && childData.expiresAt != '') {
                                try {
                                    date = childData.createdAt.toDate().toDateString();
                                    time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                                } catch (err) {
                                }
                            }
                            var createdAt = date + ' ' + time ;
                            if (searchValue) {
                                if (
                                    (childData.id && childData.id.toLowerCase().includes(searchValue)) ||
                                    (childData.client && childData.client.toLowerCase().includes(searchValue)) ||
                                    (childData.price && childData.price.toLowerCase().includes(searchValue)) ||
                                    (childData.takeAway && childData.takeAway.toLowerCase().includes(searchValue)) ||
                                    (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1)
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
                            if (orderByField === 'createdAt' && a[orderByField] != '' && b[orderByField] != '') {
                                try {
                                    aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                    bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                                } catch (err) {
                                }
                            }
                            if (orderByField === 'price') {
                                aValue = a[orderByField] ? parseFloat(a[orderByField].replace(/[^0-9.]/g, '')) || 0 : 0;
                                bValue = b[orderByField] ? parseFloat(b[orderByField].replace(/[^0-9.]/g, '')) || 0 : 0;
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
                        $('#data-table_processing').hide();
                        callback({
                            draw: data.draw,
                            recordsTotal: totalRecords,
                            recordsFiltered: totalRecords,
                            data: formattedRecords
                        });
                    } catch (error) {
                        console.error("Error fetching data from Firestore:", error);
                        $('#data-table_processing').hide();
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                    }
                },
                columnDefs: [
                {
                    targets: 5,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
                    {orderable: false, targets: [0, 6]},
                ],
                order: [['5', 'desc']],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}",
                    "processing": "" // Remove default loader
                },
            });
            function debounce(func, wait) {
                let timeout;
                const context = this;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }
    });
    async function buildHTML(val){
        var html=[];
        var id = val.id;
        var route1 =  '{{route("orders.edit",":id")}}';
        route1 = route1.replace(':id', id);
        var printRoute =  '{{route("vendors.orderprint",":id")}}';
        printRoute = printRoute.replace(':id', id);
        html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
        'for="is_open_' + id + '" ></label></td>');
        html.push('<td><div data-url="'+route1+'" class="redirecttopage" style="cursor: pointer;">'+val.id+'</div></td>');
        html.push('<td>'+val.client + '</td>');
        var price = 0;
        var price = buildHTMLProductstotal(val);    
        html.push('<td class="text-green">'+val.price+'</td>');
        html.push(val.takeAway);
        var createdAt_val='';
        if (val.createdAt) {
            var date1 = val.createdAt.toDate().toDateString();
            createdAt_val = date1;
            var time = val.createdAt.toDate().toLocaleTimeString('en-US');
            createdAt_val = createdAt_val + ' ' + time;
        }
        html.push('<td>'+createdAt_val+'</td>');
        html.push('<span class="action-btn"><a href="'+printRoute+'"><i class="fa fa-print" style="font-size:20px;"></i></a><a href="'+route1+'"><i class="fa fa-edit"></i></a><a id="'+val.id+'" class="do_not_delete" name="order-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a></span>');
        return html;      
        }
    $("#is_active").click(function () {
        $("#orderTable .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(function () {
        if ($('#orderTable .is_open:checked').length) {
            if (confirm('Are You Sure want to Delete Selected Data ?')) {
                jQuery("#data-table_processing").show();
                $('#orderTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    database.collection('restaurant_orders').doc(dataId).delete().then(function () {
                        window.location.reload();
                    });
                });
            }
        } else {
            alert('Please Select Any One Record .');
        }
    });
    $(document).on("click","a[name='order-delete']", function (e) {
            var id = this.id;
        database.collection('restaurant_orders').doc(id).delete().then(function(result){
            window.location.href = '{{ url()->current() }}';
        });
    });
    function buildHTMLProductstotal(snapshotsProducts){
        var adminCommission = snapshotsProducts.adminCommission;
        var discount = snapshotsProducts.discount;
        var couponCode = snapshotsProducts.couponCode;
        var extras = snapshotsProducts.extras;
        var extras_price = snapshotsProducts.extras_price;
        var rejectedByDrivers = snapshotsProducts.rejectedByDrivers;
        var takeAway = snapshotsProducts.takeAway;
        var tip_amount = snapshotsProducts.tip_amount;
        var status = snapshotsProducts.status;
        var products = snapshotsProducts.products;
        var deliveryCharge = snapshotsProducts.deliveryCharge;
        var totalProductPrice=0;
        var total_price = 0;
        var specialDiscount = snapshotsProducts.specialDiscount;
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
        if (products) {
            products.forEach((product) => {
                    var val=product;
                    price_item=parseFloat(val.price).toFixed(2);  
                extras_price_item=(parseFloat(val.extras_price)*parseInt(val.quantity)).toFixed(2);
                totalProductPrice =  parseFloat(price_item)*parseInt(val.quantity);
                var extras_price=0;
                if(parseFloat(extras_price_item)!=NaN && val.extras_price!=undefined){
                    extras_price=extras_price_item;
                }
                totalProductPrice =parseFloat(extras_price)+parseFloat(totalProductPrice);
                totalProductPrice=parseFloat(totalProductPrice).toFixed(2);
                total_price +=parseFloat(totalProductPrice);
            });
        }
            if(intRegex.test(discount) || floatRegex.test(discount)) {
            discount=parseFloat(discount).toFixed(decimal_degits);
            total_price -=parseFloat(discount);
            if(currencyAtRight){
                discount_val = discount+""+currentCurrency;
            }else{
                discount_val = currentCurrency+""+discount;
            }
            }
            var special_discount = 0;
            if(specialDiscount != undefined){
            special_discount = parseFloat(specialDiscount.special_discount).toFixed(2);
            total_price = total_price - special_discount;
            }
                var tax = 0;
                taxlabel = '';
                taxlabeltype = '';
                if (snapshotsProducts.hasOwnProperty('taxSetting')) {
                    var total_tax_amount = 0;
                    for (var i = 0; i < snapshotsProducts.taxSetting.length; i++) {
                        var data = snapshotsProducts.taxSetting[i];
                        if (data.type && data.tax) {
                            if (data.type == "percentage") {
                                tax = (data.tax * total_price) / 100;
                                taxlabeltype = "%";
                            } else {
                                tax = data.tax;
                                taxlabeltype = "fix";
                            }
                            taxlabel = data.title;
                        }
                        total_tax_amount += parseFloat(tax);
                    }
                    total_price = parseFloat(total_price) + parseFloat(total_tax_amount);
                }
            if((intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge)) && !isNaN(deliveryCharge)) {
                deliveryCharge=parseFloat(deliveryCharge).toFixed(decimal_degits);
                total_price +=parseFloat(deliveryCharge);
                if(currencyAtRight){
                    deliveryCharge_val = deliveryCharge+""+currentCurrency;
                }else{
                    deliveryCharge_val = currentCurrency+""+deliveryCharge;
                }
            }
            if(intRegex.test(tip_amount) || floatRegex.test(tip_amount) && !isNaN(tip_amount)) {
                tip_amount=parseFloat(tip_amount).toFixed(decimal_degits);
                total_price +=parseFloat(tip_amount);
                total_price=parseFloat(total_price).toFixed(decimal_degits);
                if(currencyAtRight){
                    tip_amount_val = tip_amount+""+currentCurrency;
                }else{
                    tip_amount_val = currentCurrency+""+tip_amount;
                }
            }
            if (currencyAtRight) {
                var total_price_val = parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                var total_price_val = currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits);
            }
            return total_price_val;
    }
</script>
@endsection
