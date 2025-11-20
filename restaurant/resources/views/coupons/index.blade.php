@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.coupon_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.coupon_table')}}</li>
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
                                <a class="nav-link" href="{!! route('coupons') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.coupon_table')}}</a>
                            </li>
                            <?php if ($id != '') { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="{!! route('coupons.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.coupon_create')}}</a>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="{!! route('coupons.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.coupon_create')}}</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="card-body">
                        
                    <div class="table-responsive m-t-10">
                        <table id="example24"
                            class="display nowrap table table-hover table-striped table-bordered table table-striped"
                            cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i> {{trans('lang.all')}}</a></label></th>
                                    <th>{{trans('lang.coupon_code')}}</th>
                                    <th>{{trans('lang.coupon_discount')}}</th>
                                    <th>{{trans('lang.coupon_description')}}</th>
                                    <th>{{trans('lang.coupon_expires_at')}}</th>
                                    <th>{{trans('lang.coupon_enabled')}}</th>
                                    <th>{{trans('lang.coupon_privacy')}}</th>
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
    var vendorUserId = "<?php echo $id; ?>";
    var vendorId = '';
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
    var ref = '';
    ref = database.collection('coupons');
        $(document).ready(function () {
            $(document.body).on('click', '.redirecttopage', function () {
                var url = $(this).attr('data-url');
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
                    const orderableColumns =  ['', 'code','discount_price', 'description','expiresAt','isEnabled','privacy','']; // Ensure this matches the actual column names
                    const orderByField = orderableColumns[orderColumnIndex];
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    try{
                        const Vendor = await getVendorId(vendorUserId);
                        const querySnapshot = await ref.where('resturant_id', '==', Vendor).get();
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
                            if (currencyAtRight) {
                                if (childData.discountType == 'Percentage' || childData.discountType == 'Percent') {
                                    discount_price = childData.discount + "%";
                                } else {
                                    discount_price = parseFloat(childData.discount).toFixed(decimal_degits) + "" + currentCurrency;
                                }
                            } else {
                                if (childData.discountType == 'Percentage' || childData.discountType == 'Percent') {
                                    discount_price = childData.discount + "%";
                                } else {
                                    discount_price = currentCurrency + "" + parseFloat(childData.discount).toFixed(decimal_degits);
                                }
                            }
                            childData.discount_price = discount_price ? discount_price : 0.00;
                            var privacy = '';
                            if (childData.hasOwnProperty('isPublic') && childData.isPublic) {
                                privacy = "{{trans("lang.public")}}";
                            } else {
                                privacy = "{{trans("lang.private")}}";
                            }
                            childData.privacy = privacy ? privacy : 0.00;
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("expiresAt") && childData.expiresAt != '') {
                                try {
                                    date = childData.expiresAt.toDate().toDateString();
                                    time = childData.expiresAt.toDate().toLocaleTimeString('en-US');
                                } catch (err) {
                                }
                            }
                            var expiresAt = date + ' ' + time ;
                            if (searchValue) {
                                if (
                                    (childData.code && childData.code.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.discount_price && childData.discount_price.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.description && childData.description.toString().toLowerCase().includes(searchValue)) ||
                                    (expiresAt && expiresAt.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                    (childData.privacy && childData.privacy.toString().toLowerCase().includes(searchValue)) 
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
                            if (orderByField === 'expiresAt' && a[orderByField] != '' && b[orderByField] != '') {
                                try {
                                    aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                    bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                                } catch (err) {
                                }
                            }
                            if (orderByField === 'discount') {
                                aValue = a[orderByField] ? parseInt(a[orderByField]) : 0;
                                bValue = b[orderByField] ? parseInt(b[orderByField]) : 0;
                            }
                            else {
                                aValue = a[orderByField] ? a[orderByField].toString().toLowerCase().trim() : '';
                                bValue = b[orderByField] ? b[orderByField].toString().toLowerCase().trim() : '';
                            }
                            if (orderByField === 'discount_price') {
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
                order: [['4', 'desc']],
                columnDefs: [
                    {
                        targets: 4,
                        type: 'date',
                        render: function (data) {
                            return data;
                        }
                    },
                    { orderable: false, targets: [0, 5, 6, 7] },
                ],
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
    async function buildHTML(val) {
        var html = [];
        var id = val.id;
        var route1 = '{{route("coupons.edit",":id")}}';
        const expireDate = new Date(val.expiresAt.toDate()); 
        const currentDate = new Date();
        const isExpired = expireDate < currentDate;
        route1 = route1.replace(':id', id);
        html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
            'for="is_open_' + id + '" ></label></td>');
        html.push('<td  data-url="' + route1 + '" class="redirecttopage">' + val.code + '</td>');
        html.push('<td>' + val.discount_price + '</td>');
        html.push('<td>' + val.description + '</td>');
        var date = '';
        var time = '';
        if (val.hasOwnProperty("expiresAt")) {
            try {
                date = val.expiresAt.toDate().toDateString();
                time = val.expiresAt.toDate().toLocaleTimeString('en-US');
            } catch (err) {
            }
            html.push('<td>' + date + ' ' + time + '</td>');
        } else {
            html.push('<td></td>');
        } 
        if (val.isEnabled) {  
            html.push('<td><label class="switch"><input type="checkbox"' + (isExpired ? 'disabled' : 'checked') + ' id="' + val.id + '" name="isEnabled"><span class="slider round"></span></label></td>');
        } else { 
            html.push('<td><label class="switch"><input type="checkbox"' + (isExpired ? 'disabled' : 'checked') + ' id="' + val.id + '" name="isEnabled"><span class="slider round"></span></label></td>');
        }
        if (val.hasOwnProperty('isPublic') && val.isPublic) {
            html.push('<td class="success"><span class="badge badge-success py-2 px-3">{{trans("lang.public")}}</sapn></td>');
        } else {
            html.push('<td class="danger"><span class="badge badge-danger py-2 px-3">{{trans("lang.private")}}</sapn></td>');
        }
        html.push('<span class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a><a id="' + val.id + '" name="coupon_delete_btn" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a></span>');
        return html;
    }
    $(document).on("click", "input[name='isEnabled']", function (e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        if (ischeck) {
            database.collection('coupons').doc(id).update({ 'isEnabled': true }).then(function (result) {
            });
        } else {
            database.collection('coupons').doc(id).update({ 'isEnabled': false }).then(function (result) {
            });
        }
    });
    $("#is_active").click(function () {
        $("#example24 .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(function () {
        if ($('#example24 .is_open:checked').length) {
            if (confirm('Are You Sure want to Delete Selected Data ?')) {
                jQuery("#data-table_processing").show();
                $('#example24 .is_open:checked').each(async function () {
                    var dataId = $(this).attr('dataId');
                    await deleteDocumentWithImage('coupons',dataId,'image');
                    window.location.reload();
                });
            }
        } else {
            alert('Please Select Any One Record .');
        }
    });
    $(document).on("click", "a[name='coupon_delete_btn']", async function (e) {
        var id = this.id;
        await deleteDocumentWithImage('coupons',id,'image');
        window.location.reload();
    });
    async function getVendorId(vendorUser) {
        var vendorId = '';
        var ref;
        await database.collection('vendors').where('author', "==", vendorUser).get().then(async function (vendorSnapshots) {
            if(vendorSnapshots.docs.exists){
                var vendorData = vendorSnapshots.docs[0].data();
                vendorId = vendorData.id;
            }
        })
        return vendorId;
    }
</script>
@endsection