<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorUsers;
use Illuminate\Support\Facades\Auth;
use Session;

class DiveinRestaurantController extends Controller
{
    public function __construct()
    {
        // Location check removed - location is now optional
    }
	
    public function index()
    {
        return view ('dinein.index');
    }

    public function dyiningrestaurant(){
        
        return view('dinein.dinerestaurant');
    }
    
}
