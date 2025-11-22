<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

class PaymentController extends Controller
{
    public function index($id='')
    {
        return view("order_transactions.index")->with('id',$id);
    }

    public function paymentsuccess()
    {
        return response()->json(array('result'=>$_REQUEST));
    }

    public function paymentfailed()
    {
        return response()->json(array('result'=>$_REQUEST));
    }

    public function paymentpending()
    {
        return response()->json(array('result'=>$_REQUEST));
    }
}
