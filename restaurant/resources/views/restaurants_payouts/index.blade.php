@extends('layouts.app')
@section('content')
        <div class="page-wrapper">
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-themecolor">{{trans('lang.restaurants_payout_plural')}}</h3>
                </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                        <li class="breadcrumb-item active">{{trans('lang.restaurants_payout_plural')}}</li>
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
                                        <li class="nav-item active">
                                            <a class="nav-link active" href="{!! url()->current() !!}"><i
                                                        class="fa fa-list mr-2"></i>{{trans('lang.vendors_payout_table')}}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{!! route('payments.create') !!}"><i
                                                        class="fa fa-plus mr-2"></i>{{trans('lang.vendors_payout_create')}}</a>
                                        </li>
                                    </ul>
                                </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>{{trans('lang.paid_amount')}}</th>
                                                <th>{{trans('lang.date')}}</th>
                                                <th>{{trans('lang.restaurants_payout_note')}}</th>
                                                <th>{{trans('lang.status')}}</th>
                                                <th>{{trans('lang.withdraw_method')}}</th>
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
    var offest=1;
    var pagesize=10; 
    var end = null;
    var endarray=[];
    var start = null;
    var user_number = [];
    var vendorUserId = "<?php echo $id; ?>";
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
                    const orderableColumns =  ['amount', 'paidDate','note', 'paymentStatus','withdrawMethod']; // Ensure this matches the actual column names
                    const orderByField = orderableColumns[orderColumnIndex];
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    try{
                        const Vendor = await getVendorId(vendorUserId);
                        const querySnapshot = await database.collection('payouts').where('vendorID','==',Vendor).orderBy('paidDate', 'desc').get();
                        if (!querySnapshot || querySnapshot.empty) {
                            console.error("No data found in Firestore.");
                                $('#data-table_processing').hide(); // Hide loader
                                callback({
                                    draw: data.draw,
                                    recordsTotal: 0,
                                    recordsFiltered: 0,
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
                            if (childData.hasOwnProperty("paidDate") && childData.paidDate != '') {
                                try {
                                    date = childData.paidDate.toDate().toDateString();
                                    time = childData.paidDate.toDate().toLocaleTimeString('en-US');
                                } catch (err) {
                                }
                            }
                            var paidDate = date + ' ' + time ;
                            if (searchValue) {
                                if (
                                    (childData.amount && childData.amount.toString().toLowerCase().includes(searchValue)) ||
                                    (paidDate && paidDate.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                    (childData.note && childData.note.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.paymentStatus && childData.paymentStatus.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.withdrawMethod && childData.withdrawMethod.toString().toLowerCase().includes(searchValue))
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
                            if (orderByField === 'paidDate' && a[orderByField] != '' && b[orderByField] != '') {
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
                    {orderable: false, targets: [3]},
                ],
                order: [['1', 'desc']],
                "language": {
                        "zeroRecords": "{{trans('lang.no_record_found')}}",
                        "emptyTable": "{{trans('lang.no_record_found')}}"
                },
            });
            function debounce(func, wait) {
                let timeout;
                const context = this;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }
        });
    async function buildHTML(val){
        var html=[];
        if (currencyAtRight) {
            price_val = parseFloat(val.amount).toFixed(decimal_degits) + "" + currentCurrency;
        } else {
            price_val = currentCurrency + "" + parseFloat(val.amount).toFixed(decimal_degits);
        }
        html.push('<td class="text-danger">('+price_val+')</td>');
        var date =  val.paidDate.toDate().toDateString();
        var time = val.paidDate.toDate().toLocaleTimeString('en-US');
        html.push('<td>'+date+' '+time+'</td>');
        if(val.note){
        html.push('<td>'+val.note+'</td>');
        }else{
            html.push('<td></td>');
        }
        if(val.paymentStatus == "Reject" || val.paymentStatus == "Failed"){
            html.push('<td><span class="badge badge-danger py-2 px-3">'+val.paymentStatus+'</sapn></td>');
        }else if(val.paymentStatus == "Pending" || val.paymentStatus == "In Process"){
            html.push('<td><span class="badge badge-warning py-2 px-3">'+val.paymentStatus+'</sapn></td>');
        }else if(val.paymentStatus == "Success"){
            html.push('<td><span class="badge badge-success py-2 px-3">'+val.paymentStatus+'</sapn></td>');
        } else {
            html.push('<td></td>');
        }
        if (val.withdrawMethod) {
            var selectedwithdrawMethod =  val.withdrawMethod == "bank" ? "Bank Transfer" : val.withdrawMethod;
            html.push('<span style="text-transform:capitalize">' + selectedwithdrawMethod + '</span>');
        } else {
            html.push('<td></td>');
        }
        return html;      
    }                
async function getVendorId(vendorUser){
    var vendorId = '';
    var ref;
    await database.collection('vendors').where('author',"==",vendorUser).get().then(async function(vendorSnapshots){
        var vendorData = vendorSnapshots.docs[0].data();    
        vendorId = vendorData.id;
    })
    return vendorId;
}
</script>
@endsection
