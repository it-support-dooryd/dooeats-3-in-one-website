@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.food_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.food_plural') }}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>
        <div class="row px-5 mb-2">
            <div class="col-12">
                <span class="font-weight-bold text-danger food-limit-note"></span>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                                <li class="nav-item active">
                                    <a class="nav-link" href="{!! route('foods') !!}"><i
                                            class="fa fa-list mr-2"></i>{{ trans('lang.food_table') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{!! route('foods.create') !!}"><i
                                            class="fa fa-plus mr-2"></i>{{ trans('lang.food_create') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="example24"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                    class="col-3 control-label" for="is_active">
                                                    <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i
                                                            class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label>
                                            </th>
                                            <th>{{ trans('lang.food_image') }}</th>
                                            <th>{{ trans('lang.food_name') }}</th>
                                            <th>{{ trans('lang.food_price') }}</th>
                                            <th>{{ trans('lang.food_category_id') }}</th>
                                            <th>{{ trans('lang.date') }}</th>
                                            <th>{{ trans('lang.food_publish') }}</th>
                                            <th>{{ trans('lang.actions') }}</th>
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
        var offest = 1;
        var pagesize = 10;
        var end = null;
        var endarray = [];
        var start = null;
        var user_number = [];
        var vendorUserId = "<?php echo $id; ?>";
        var vendorId;
        var ref;
        var append_list = '';
        var placeholderImage = '';
        ref = database.collection('vendor_products');
        var activeCurrencyref = database.collection('currencies').where('isActive', "==", true);
        var activeCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        activeCurrencyref.get().then(async function(currencySnapshots) {
            currencySnapshotsdata = currencySnapshots.docs[0].data();
            activeCurrency = currencySnapshotsdata.symbol;
            currencyAtRight = currencySnapshotsdata.symbolAtRight;
            if (currencySnapshotsdata.decimal_degits) {
                decimal_degits = currencySnapshotsdata.decimal_degits;
            }
        })
        $(document).ready(function() {
            $('#category_search_dropdown').hide();
            $(document.body).on('click', '.redirecttopage', function() {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            var placeholder = database.collection('settings').doc('placeHolderImage');
            placeholder.get().then(async function(snapshotsimage) {
                var placeholderImageData = snapshotsimage.data();
                placeholderImage = placeholderImageData.image;
            })
            const table = $('#example24').DataTable({
                pageLength: 10, // Number of rows per page
                processing: false, // Show processing indicator
                serverSide: true, // Enable server-side processing
                responsive: true,
                ajax: async function(data, callback, settings) {
                    const start = data.start;
                    const length = data.length;
                    const searchValue = data.search.value.toLowerCase();
                    const orderColumnIndex = data.order[0].column;
                    const orderDirection = data.order[0].dir;
                    const orderableColumns = ['', '', 'name', 'finalPrice', 'categoryName', 'createdAt',
                        '', ''
                    ]; // Ensure this matches the actual column names
                    const orderByField = orderableColumns[orderColumnIndex];
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    try {
                        const Vendor = await getVendorId(vendorUserId);
                        const querySnapshot = await ref.where('vendorID', "==", Vendor).get();
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
                            childData.id = doc
                                .id; // Ensure the document ID is included in the data
                            var finalPrice = 0;
                            if (childData.hasOwnProperty('disPrice') && childData
                                .disPrice != '' && childData.disPrice != '0') {
                                finalPrice = childData.disPrice;
                            } else {
                                finalPrice = childData.price;
                            }
                            childData.finalPrice = parseInt(finalPrice);
                            childData.categoryName = await productCategory(childData
                                .categoryID);
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("createdAt") && childData
                                .expiresAt != '') {
                                try {
                                    date = childData.createdAt.toDate().toDateString();
                                    time = childData.createdAt.toDate()
                                        .toLocaleTimeString('en-US');
                                } catch (err) {}
                            }
                            var createdAt = date + ' ' + time;
                            childData.createDateTime=createdAt;
                            if (searchValue) {
                                if (
                                    (childData.name && childData.name.toString()
                                        .toLowerCase().includes(searchValue)) ||
                                    (childData.finalPrice && childData.finalPrice
                                        .toString().toLowerCase().includes(searchValue)
                                    ) ||
                                    (childData.categoryName && childData.categoryName
                                        .toString().toLowerCase().includes(
                                            searchValue) ||
                                        (createdAt && createdAt.toString().toLowerCase()
                                            .indexOf(searchValue) > -1)
                                    )
                                ) {
                                    filteredRecords.push(childData);
                                }
                            } else {
                                filteredRecords.push(childData);
                            }
                        }));
                        filteredRecords.sort((a, b) => {
                            let aValue = a[orderByField];
                            let bValue = b[orderByField];
                            if (orderByField === 'createdAt' && a[orderByField] != '' && b[
                                    orderByField] != '') {
                                try {
                                    aValue = a[orderByField] ? new Date(a[orderByField]
                                    .toDate()).getTime() : 0;
                                    bValue = b[orderByField] ? new Date(b[orderByField]
                                    .toDate()).getTime() : 0;
                                } catch (err) {}
                            }
                            if (orderByField === 'finalPrice') {
                                aValue = a[orderByField] ? parseInt(a[orderByField]) : 0;
                                bValue = b[orderByField] ? parseInt(b[orderByField]) : 0;
                            } else {
                                aValue = a[orderByField] ? a[orderByField].toString()
                                    .toLowerCase().trim() : '';
                                bValue = b[orderByField] ? b[orderByField].toString()
                                    .toLowerCase().trim() : '';
                            }
                            if (orderDirection === 'asc') {
                                return (aValue > bValue) ? 1 : -1;
                            } else {
                                return (aValue < bValue) ? 1 : -1;
                            }
                        });
                        const totalRecords = filteredRecords.length;
                        const paginatedRecords = filteredRecords.slice(start, start + length);
                        const formattedRecords = await Promise.all(paginatedRecords.map(async (
                            childData) => {
                            return await buildHTML(childData);
                        }));
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: totalRecords,
                            recordsFiltered: totalRecords,
                            data: formattedRecords
                        });
                    } catch (error) {
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
                order: [5, 'asc'],
                columnDefs: [{
                        orderable: false,
                        targets: [0, 1, 6, 7]
                    },
                    {
                        targets: 5,
                        type: 'date',
                        render: function(data) {
                            return data;
                        }
                    },
                ],
                "language": {
                    "zeroRecords": "{{ trans('lang.no_record_found') }}",
                    "emptyTable": "{{ trans('lang.no_record_found') }}"
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
        $(document.body).on('change', '#selected_search', function() {
            if (jQuery(this).val() == 'category') {
                var ref_category = database.collection('vendor_categories');
                ref_category.get().then(async function(snapshots) {
                    snapshots.docs.forEach((listval) => {
                        var data = listval.data();
                        $('#category_search_dropdown').append($("<option></option").attr(
                            "value", data.id).text(data.title));
                    });
                });
                jQuery('#search').hide();
                jQuery('#category_search_dropdown').show();
            } else {
                jQuery('#search').show();
                jQuery('#category_search_dropdown').hide();
            }
        });
        async function buildHTML(val) {
            var html = [];
            var id = val.id;
            var route1 = '{{ route('foods.edit', ':id') }}';
            route1 = route1.replace(':id', id);
            html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' +
                id + '"><label class="col-3 control-label"\n' +
                'for="is_open_' + id + '" ></label></td>');
            if (val.photo == '' && val.photo == null) {
                html.push('<td><img class="rounded" style="width:50px" src="' + placeholderImage +
                    '" alt="image"></td>');
            } else {
                html.push('<td><img onerror="this.onerror=null;this.src=\'' + placeholderImage +
                    '\'" class="rounded" style="width:50px" src="' + val.photo + '" alt="image"></td>');
            }
            html.push('<td data-url="' + route1 + '" class="redirecttopage">' + val.name + '</td>');
            if (val.hasOwnProperty('disPrice') && val.disPrice != '' && val.disPrice != '0') {
                if (currencyAtRight) {
                    html.push('<td class="text-green">' + parseFloat(val.disPrice).toFixed(decimal_degits) + '' +
                        activeCurrency + ' <s>' + parseFloat(val.price).toFixed(decimal_degits) + '' +
                        activeCurrency + '</s></td>');
                } else {
                    html.push('<td class="text-green">' + activeCurrency + '' + parseFloat(val.disPrice).toFixed(
                        decimal_degits) + ' <s>' + activeCurrency + '' + parseFloat(val.price).toFixed(
                        decimal_degits) + '</s></td>');
                }
            } else {
                if (currencyAtRight) {
                    html.push('<td class="text-green">' + parseFloat(val.price).toFixed(decimal_degits) + '' +
                        activeCurrency + '</td>');
                } else {
                    html.push('<td class="text-green">' + activeCurrency + '' + parseFloat(val.price).toFixed(
                        decimal_degits) + '</td>');
                }
            }
            
            html.push('<td class="category_' + val.categoryID + '">' + val.categoryName + '</td>');
            html.push('<td>'+val.createDateTime+'</td>');
            if (val.publish) {
                html.push('<td><label class="switch"><input type="checkbox" checked id="' + val.id +
                    '" name="publish"><span class="slider round"></span></label></td>');
            } else {
                html.push('<td><label class="switch"><input type="checkbox" id="' + val.id +
                    '" name="publish"><span class="slider round"></span></label></td>');
            }
            var action = '';
            action = action + '<span class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a>';
            action = action + '<a id="' + val.id +
                '" class="do_not_delete" name="food-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>';
            action = action + '</span>';
            html.push(action);
            return html;
        }
        $(document).on("click", "input[name='publish']", function(e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            if (ischeck) {
                database.collection('vendor_products').doc(id).update({
                    'publish': true
                }).then(function(result) {});
            } else {
                database.collection('vendor_products').doc(id).update({
                    'publish': false
                }).then(function(result) {});
            }
        });
        $("#is_active").click(function() {
            $("#example24 .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function() {
            if ($('#example24 .is_open:checked').length) {
                if (confirm('Are You Sure want to Delete Selected Data ?')) {
                    jQuery("#data-table_processing").show();
                    $('#example24 .is_open:checked').each(async function() {
                        var dataId = $(this).attr('dataId');
                        await deleteDocumentWithImage('vendor_products', dataId, 'photo', 'photos');
                        window.location.reload();
                    });
                }
            } else {
                alert('Please Select Any One Record .');
            }
        });
        async function productCategory(category) {
            var productCategory = '';
            await database.collection('vendor_categories').where("id", "==", category).get().then(async function(
                snapshotss) {
                if (snapshotss.docs[0]) {
                    var category_data = snapshotss.docs[0].data();
                    productCategory = category_data.title;
                }
            });
            return productCategory;
        }
        $(document).on("click", "a[name='food-delete']", async function(e) {
            var id = this.id;
            await deleteDocumentWithImage('vendor_products', id, 'photo', 'photos');
            window.location.reload();
        });
        async function getVendorId(vendorUser) {
            var vendorId = '';
            var ref;
            await database.collection('vendors').where('author', "==", vendorUser).get().then(async function(
                vendorSnapshots) {
                var vendorData = vendorSnapshots.docs[0].data();
                vendorId = vendorData.id;
                var subscriptionModel = false;
                var subscriptionBusinessModel = database.collection('settings').doc("restaurant");
                await subscriptionBusinessModel.get().then(async function(snapshots) {
                    var subscriptionSetting = snapshots.data();
                    if (subscriptionSetting.subscription_model == true) {
                        subscriptionModel = true;
                    }
                });
                if (subscriptionModel) {
                    if (vendorData.hasOwnProperty('subscription_plan') && vendorData.subscription_plan !=
                        null && vendorData.subscription_plan != '') {
                        itemLimit = vendorData.subscription_plan.itemLimit;
                        if (itemLimit != '-1') {
                            $('.food-limit-note').html(
                                '{{ trans('lang.note') }} : {{ trans('lang.your_food_limit_is') }} ' +
                                itemLimit + ' {{ trans('lang.so_only_first') }} ' + itemLimit +
                                ' {{ trans('lang.foods_will_visible_to_customer') }}')
                        }
                    }
                }

            })
            return vendorId;
        }
    </script>
@endsection
