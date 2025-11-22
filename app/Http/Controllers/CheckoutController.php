<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;
use App\Models\User;
use Session;
class CheckoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!isset($_COOKIE['address_name'])) {
            \Redirect::to('set-location')->send();
        }
        $this->middleware('auth');
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function checkout()
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $cart = Session::get('cart', []);
        if (Session::get('takeawayOption') == 'true') {
        } else {
            $deliveryChargemain = @$_COOKIE['deliveryChargemain'];
            $address_lat = @$_COOKIE['address_lat'];
            $address_lng = @$_COOKIE['address_lng'];
            $restaurant_latitude = @$_COOKIE['restaurant_latitude'];
            $restaurant_longitude = @$_COOKIE['restaurant_longitude'];
            if (@$deliveryChargemain && @$address_lat && @$address_lng && @$restaurant_latitude && @$restaurant_longitude) {
                $deliveryChargemain = json_decode($deliveryChargemain);
                if (!empty($deliveryChargemain)) {
                    if (!empty($cart['distanceType'])) {
                        $distanceType = $cart['distanceType'];
                    } else {
                        $distanceType = 'km';
                    }
                    $delivery_charges_per_km = $deliveryChargemain->delivery_charges_per_km;
                    $minimum_delivery_charges = $deliveryChargemain->minimum_delivery_charges;
                    $minimum_delivery_charges_within_km = $deliveryChargemain->minimum_delivery_charges_within_km;
                    $kmradius = $this->distance($address_lat, $address_lng, $restaurant_latitude, $restaurant_longitude, $distanceType);
                    if ($minimum_delivery_charges_within_km > $kmradius) {
                        $cart['deliverychargemain'] = $minimum_delivery_charges;
                    } else {
                        $cart['deliverychargemain'] = round($kmradius * $delivery_charges_per_km, 2);
                    }
                    $cart['deliverykm'] = $kmradius;
                }
            }

            if (@$cart['isSelfDelivery'] === true || @$cart['isSelfDelivery'] === "true" ) {
                $cart['deliverycharge'] = 0;
            } else {
                $cart['deliverycharge'] = @$cart['deliverychargemain'];
            }

            Session::put('cart', $cart);
            Session::save();
        }
        return view('checkout.checkout', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid]);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function proccesstopay(Request $request)
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $cart = Session::get('cart', []);
        if (@$cart['cart_order']) {
            if ($cart['cart_order']['payment_method'] == 'paystack') {
                $paystack_public_key = $cart['cart_order']['paystack_public_key'];
                $paystack_secret_key = $cart['cart_order']['paystack_secret_key'];
                $paystack_isSandbox = $cart['cart_order']['paystack_isSandbox'];
                $authorName = $cart['cart_order']['authorName'];
                $total_pay = $cart['cart_order']['total_pay'];
                $amount = 0;
                \Paystack\Paystack::init($paystack_secret_key);
                $payment = \Paystack\Transaction::initialize([
                    'email' => $email,
                    'amount' => (int) ($total_pay * 100),
                ]);
                Session::put('paystack_authorization_url', $payment->authorization_url);
                Session::put('paystack_access_code', $payment->access_code);
                Session::put('paystack_reference', $payment->reference);
                Session::save();
                if ($payment->authorization_url) {
                    $script = "<script>
                        window.location = '" . $payment->authorization_url . "';
                    </script>";
                    echo $script;
                    exit();
                } else {
                    $script = "<script>
                        window.location = '" . url('
                        ') . "';
                    </script>";
                    echo $script;
                    exit();
                }
            }
        } else {
            return redirect()->route('checkout');
        }
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function success()
    {
        $cart = Session::get('cart', []);
        $order_json = [];
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        
        if (isset($_GET['reference'])) {
            $paystack_reference = Session::get('paystack_reference');
            $paystack_access_code = Session::get('paystack_access_code');
            if ($paystack_reference == $_GET['reference']) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
        $payment_method = @$cart['cart_order']['payment_method'] ? $cart['cart_order']['payment_method'] : 'cod';
        return view('checkout.success', ['cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'payment_method' => $payment_method]);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function orderProccessing(Request $request)
    {
        $cart_order = $request->all();
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $cart = Session::get('cart', []);
        $cart['cart_order'] = $cart_order;
        Session::put('cart', $cart);
        Session::save();
        $res = ['status' => true];
        echo json_encode($res);
        exit();
    }
    public function proccesspaystack(Request $request)
    {
        $cart = Session::get('cart', []);
        $paystack_public_key = $cart['cart_order']['paystack_public_key'];
        $paystack_secret_key = $cart['cart_order']['paystack_secret_key'];
        \Paystack\Paystack::init($paystack_secret_key);
        $payment = \Paystack\Transaction::initialize([
            'email' => 'jame@gosling.com',
            'amount' => '3000',
        ]);
        exit();
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function failed()
    {
        echo 'failed payment';
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        if ($unit == 'km') {
            return $miles * 1.609344;
        } else {
            return $miles;
        }
    }
}
