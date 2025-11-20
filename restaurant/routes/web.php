<?php



use Illuminate\Support\Facades\Route;




Route::get('lang/change', [App\Http\Controllers\LangController::class, 'change'])->name('changeLang');



Route::post('setToken', [App\Http\Controllers\Auth\AjaxController::class, 'setToken'])->name('setToken');
Route::post('setSubcriptionFlag', [App\Http\Controllers\Auth\AjaxController::class, 'setSubcriptionFlag'])->name('setSubcriptionFlag');



Route::get('register', function () {

    return view('auth.register');

})->name('register');



Route::get('signup', function () {

    return view('auth.signup');

})->name('signup');



Route::get('register/phone', function () {

    return view('auth.phone_register');

})->name('register.phone');





Route::get('subscription-plan', [App\Http\Controllers\SubscriptionController::class, 'show'])->name('subscription-plan.show');

Route::get('subscription-plan/checkout/{id}', [App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscription-plans.checkout');

Route::post('payment-proccessing', [App\Http\Controllers\SubscriptionController::class, 'orderProccessing'])->name('payment-proccessing');



Route::get('pay-subscription', [App\Http\Controllers\SubscriptionController::class, 'proccesstopay'])->name('pay-subscription');

Route::post('order-complete', [App\Http\Controllers\SubscriptionController::class, 'orderComplete'])->name('order-complete');

Route::post('process-stripe', [App\Http\Controllers\SubscriptionController::class, 'processStripePayment'])->name('process-stripe');

Route::post('process-paypal', [App\Http\Controllers\SubscriptionController::class, 'processPaypalPayment'])->name('process-paypal');

Route::post('razorpaypayment', [App\Http\Controllers\SubscriptionController::class, 'razorpaypayment'])->name('razorpaypayment');

Route::post('process-mercadopago', [App\Http\Controllers\SubscriptionController::class, 'processMercadoPagoPayment'])->name('process-mercadopago');



Route::get('success', [App\Http\Controllers\SubscriptionController::class, 'success'])->name('success');

Route::get('failed', [App\Http\Controllers\SubscriptionController::class, 'failed'])->name('failed');

Route::get('notify', [App\Http\Controllers\SubscriptionController::class, 'notify'])->name('notify');






Auth::routes();

Route::get('forgot-password', [App\Http\Controllers\Auth\LoginController::class, 'forgotPassword'])->name('forgot-password');
Route::post('store-firebase-service', [App\Http\Controllers\HomeController::class, 'storeFirebaseService'])->name('store-firebase-service');

Route::middleware(['check.subscription'])->group(function () {
    
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('my-subscription/show/{id}', [App\Http\Controllers\MySubscriptionsController::class, 'show'])->name('my-subscription.show');

    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');


    Route::get('my-subscriptions', [App\Http\Controllers\MySubscriptionsController::class, 'index'])->name('my-subscriptions');


    Route::get('/users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');

    Route::get('/restaurant', [App\Http\Controllers\UserController::class, 'restaurant'])->name('restaurant');

    Route::get('/foods', [App\Http\Controllers\FoodController::class, 'index'])->name('foods');

    Route::get('/foods/edit/{id}', [App\Http\Controllers\FoodController::class, 'edit'])->name('foods.edit');

    Route::get('/foods/create', [App\Http\Controllers\FoodController::class, 'create'])->name('foods.create');

    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders');

    Route::get('/orders/edit/{id}', [App\Http\Controllers\OrderController::class, 'edit'])->name('orders.edit');

    Route::get('/placedOrders', [App\Http\Controllers\OrderController::class, 'placedOrders'])->name('placedOrders');



    Route::get('/acceptedOrders', [App\Http\Controllers\OrderController::class, 'acceptedOrders'])->name('acceptedOrders');



    Route::get('/rejectedOrders', [App\Http\Controllers\OrderController::class, 'rejectedOrders'])->name('rejectedOrders');



    Route::get('/payments', [App\Http\Controllers\PayoutsController::class, 'index'])->name('payments');



    Route::get('/payments/create', [App\Http\Controllers\PayoutsController::class, 'create'])->name('payments.create');



    Route::get('/payments/edit/{id}', [App\Http\Controllers\PaymentController::class, 'edit'])->name('payments.edit');



    Route::get('/earnings', [App\Http\Controllers\EarningController::class, 'index'])->name('earnings');



    Route::get('/earnings/edit/{id}', [App\Http\Controllers\EarningController::class, 'edit'])->name('earnings.edit');



    Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('coupons');



    Route::get('/coupons/edit/{id}', [App\Http\Controllers\CouponController::class, 'edit'])->name('coupons.edit');



    Route::get('/coupons/create', [App\Http\Controllers\CouponController::class, 'create'])->name('coupons.create');



    Route::post('order-status-notification', [App\Http\Controllers\OrderController::class, 'sendNotification'])->name('order-status-notification');



    Route::post('/sendnotification', [App\Http\Controllers\BookTableController::class, 'sendnotification'])->name('sendnotification');



    Route::get('/booktable', [App\Http\Controllers\BookTableController::class, 'index'])->name('booktable');



    Route::get('/booktable/edit/{id}', [App\Http\Controllers\BookTableController::class, 'edit'])->name('booktable.edit');



    Route::get('/orders/print/{id}', [App\Http\Controllers\OrderController::class, 'orderprint'])->name('vendors.orderprint');

    Route::get('/wallettransaction', [App\Http\Controllers\TransactionController::class, 'index'])->name('wallettransaction.index');



    Route::post('send-email', [App\Http\Controllers\SendEmailController::class, 'sendMail'])->name('sendMail');




    Route::get('document-list', [App\Http\Controllers\DocumentController::class, 'DocumentList'])->name('vendors.document');

    Route::get('document/upload/{id}', [App\Http\Controllers\DocumentController::class, 'DocumentUpload'])->name('document.upload');



    Route::get('withdraw-method', [App\Http\Controllers\WithdrawMethodController::class, 'index'])->name('withdraw-method');

    Route::get('withdraw-method/add', [App\Http\Controllers\WithdrawMethodController::class, 'create'])->name('withdraw-method.create');
});

Route::get('advertisements', [App\Http\Controllers\AdvertisementsController::class, 'index'])->name('advertisements');
Route::get('/advertisements/pending', [App\Http\Controllers\AdvertisementsController::class, 'index'])->name('advertisements.pending');
Route::get('/advertisements/create', [App\Http\Controllers\AdvertisementsController::class, 'create'])->name('advertisements.create');
Route::get('/advertisements/edit/{id}', [App\Http\Controllers\AdvertisementsController::class, 'edit'])->name('advertisements.edit');
Route::get('/advertisements/view/{id}', [App\Http\Controllers\AdvertisementsController::class, 'view'])->name('advertisements.view');
Route::get('/advertisement/chat/{id}', [App\Http\Controllers\AdvertisementsController::class, 'chat'])->name('advertisement.chat');

Route::get('deliveryman', [App\Http\Controllers\DeliverymanController::class, 'index'])->name('deliveryman');
Route::get('deliveryman/create', [App\Http\Controllers\DeliverymanController::class, 'create'])->name('deliveryman.create');
Route::get('deliveryman/edit/{id}', [App\Http\Controllers\DeliverymanController::class, 'edit'])->name('deliveryman.edit');
