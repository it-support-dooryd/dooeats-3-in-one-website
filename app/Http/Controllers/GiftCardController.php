<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;
use App\Models\User;
use Session;

class GiftCardController extends Controller
{
    public function __construct()
    {
        if (!isset($_COOKIE['address_name'])) {
            \Redirect::to('set-location')->send();
        }
        $this->middleware('auth');
    }

    public function index()
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        return view('gift_card.giftcard')->with('id',$user->uuid);
    }

    public function giftCardProcessing(Request $request){
        $gift_cart_order = $request->all();
        $cart = array();
        Session::put('gift_cart', $cart);
        $cart = Session::get('gift_cart', []);
        $cart['gift_cart_order'] = $gift_cart_order;
        Session::put('gift_cart', $cart);
        Session::save();
        $res = array('status' => true);
        echo json_encode($res);
        exit;
    }

    public function proccesstopay(Request $request)
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $cart = Session::get('gift_cart', []);
        if (@$cart['gift_cart_order']) {
            if ($cart['gift_cart_order']['payment_method'] == 'paystack') {
                $paystack_public_key = $cart['gift_cart_order']['paystack_public_key'];
                $paystack_secret_key = $cart['gift_cart_order']['paystack_secret_key'];
                $paystack_isSandbox = $cart['gift_cart_order']['paystack_isSandbox'];
                $authorName = $cart['gift_cart_order']['authorName'];
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $amount = 0;
                
                define("PaystackPublicKey", $paystack_public_key);
                define("PaystackSecretKey", $paystack_secret_key);
                \Paystack\Paystack::init($paystack_secret_key);
                $payment = \Paystack\Transaction::initialize([
                    'email' => $email,
                    'amount' => (int) ($total_pay * 100),
                    'callback_url'=>route('giftcard.success')
                ]);
                Session::put('paystack_authorization_url', $payment->authorization_url);
                Session::put('paystack_access_code', $payment->access_code);
                Session::put('paystack_reference', $payment->reference);
                Session::save();
                if ($payment->authorization_url) {
                    $script = "<script>window.location = '" . $payment->authorization_url . "';</script>";
                    echo $script;
                    exit;
                } else {
                    $script = "<script>window.location = '" . url('') . "';</script>";
                    echo $script;
                    exit;
                }
            }
        } else {
            return redirect()->route('customize.giftcard');
        }
    }

    public function success()
    {
        $cart = Session::get('gift_cart', []);
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        
        if (isset($_GET['reference'])) {
            $paystack_reference = Session::get('paystack_reference');
            $paystack_access_code = Session::get('paystack_access_code');
            if ($paystack_reference == $_GET['reference']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
        
        if(@$cart['gift_cart_order']['payment_method'] && $cart['gift_cart_order']['payment_method'] == "wallet"){
            $cart['payment_status'] = true;
            Session::put('gift_cart', $cart);
            Session::put('success', 'Payment successful');
            Session::save();
        }
        $payment_method = (@$cart['gift_cart_order']['payment_method']) ? $cart['gift_cart_order']['payment_method'] : 'cod';
        return view('gift_card.success', ['cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'payment_method' => $payment_method]);
    }

    public function giftcards()
    {
        return view('gift_card.my_giftcards');
    }
}
