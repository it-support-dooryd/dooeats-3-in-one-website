<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;
use Illuminate\Http\Request;
use Session;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function show()
    {
        $user = Auth::user();
        $userId = Auth::id();
        $exist = VendorUsers::where('user_id', $userId)->first();
        $userId = $exist->uuid;
        return view("subscription_plans.show")->with('userId', $userId);
    }
    public function checkout($id)
    {
        $user = Auth::user();
        $userId = Auth::id();
        $exist = VendorUsers::where('user_id', $userId)->first();
        $userId = $exist->uuid;
        $planId = $id;
        return view("subscription_plans.checkout")->with('userId',$userId)->with( 'planId',$planId);
    }
    public function orderProccessing(Request $request)
    {
        $cart_order = $request->all();
        $email = Auth::user()->email;
        $cart = Session::get('cart', []);
        $cart['cart_order'] = $cart_order;
        Session::put('cart', $cart);
        Session::save();
        $res = array('status' => true);
        echo json_encode($res);
        exit;
    }
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
                $authorName = $cart['cart_order']['order_json']['authorName'];
                $email = $cart['cart_order']['order_json']['authorEmail'];
                $total_pay = $cart['cart_order']['total_pay'];
                $amount = 0;
                $fail_url = route('subscription-plan.show');
                $success_url = route('success');
                \Paystack\Paystack::init($paystack_secret_key);
                $payment = \Paystack\Transaction::initialize([
                    'email' => $email,
                    'amount' => (int) ($total_pay * 100),
                    'callback_url' => $success_url, 
                    'metadata' => [
                        'custom_fields' => [
                            ['display_name' => 'Author Name', 'variable_name' => 'author_name', 'value' => $authorName],
                            ['display_name' => 'Failure URL', 'variable_name' => 'fail_url', 'value' => $fail_url],
                        ]
                    ]
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
            return redirect()->route('checkout');
        }
    }

    public function success()
    {
        $cart = Session::get('cart', []);
        $order_json = array();
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
        
        $payment_method = (@$cart['cart_order']['payment_method']) ? $cart['cart_order']['payment_method'] : '';
        return view('subscription_plans.success', ['cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'payment_method' => $payment_method]);
    }
}