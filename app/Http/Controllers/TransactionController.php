<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorUsers;
use Illuminate\Support\Facades\Auth;
use Session;

class TransactionController extends Controller
{
    
    public function __construct()
    {
       $this->middleware('auth');
    }
 
    public function index()
    {
        return view('transactions.index');
    }

    public function proccesstopaywallet(Request $request)
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $user_wallet = Session::get('user_wallet', []);
        if ($user_wallet) {
            if ($user_wallet['data']['payment_method'] == 'paystack') {
                $paystack_public_key = $user_wallet['data']['paystack_public_key'];
                $paystack_secret_key = $user_wallet['data']['paystack_secret_key'];
                $paystack_isSandbox = $user_wallet['data']['paystack_isSandbox'];
                $userEmail = $user_wallet['user']['email'];
                $authorName = $user_wallet['user']['firstName'];
                $total_pay = $user_wallet['data']['amount'];
                \Paystack\Paystack::init($paystack_secret_key);
                $payment = \Paystack\Transaction::initialize([
                    'email' => $userEmail,
                    'amount' => (int)($total_pay * 100),
                    'callback_url' => route('wallet-success')
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
            } else {
                return response()->json(['error' => 'Invalid payment method selected'], 400);
            }
        } else {
            return response()->json(['error' => 'Wallet is empty or invalid'], 400);
        }
    }
    
    public function success() {
        $user_wallet = Session::get('user_wallet', []);
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
    
        // Paystack payment check
        if (isset($_GET['reference'])) {
            $paystack_reference = Session::get('paystack_reference');
            if ($paystack_reference == $_GET['reference']) {
                $user_wallet['transaction_id'] = "";
                $user_wallet['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
    
        $payment_method = $user_wallet['data']['payment_method'];
    
        return view('transactions.success', [
            'user_wallet' => $user_wallet, 
            'id' => $user->uuid, 
            'email' => $email, 
            'payment_method' => $payment_method
        ]);
    }
    
    public function walletProccessing(Request $request) {
        $data = $request->all();
        $user = Auth::user();
        $user_wallet = [];
        $user_wallet['data'] = $data;
        $user_wallet['user'] = json_decode($request->author, true);
        Session::put('user_wallet', $user_wallet);
        Session::save();
        $res = ['status' => true];
        echo json_encode($res);
        exit;
    }
    
    public function failed() {
        echo "failed payment";
    }         
          
}
