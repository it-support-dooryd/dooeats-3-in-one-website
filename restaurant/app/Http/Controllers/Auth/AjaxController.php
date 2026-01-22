<?php
/**
 * File name: AjaxController.php
 * Last modified: 2022.06.11 at 16:10:52
 * Author:Siddhi
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\Auth;

use App\Models\VendorUsers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Prettus\Validator\Exceptions\ValidatorException;

class AjaxController extends Controller
{


    public function setToken(Request $request)
    {
        $uuid = $request->id;
        $password = $request->password;
        $email = $request->email;
        $isSubscribed = $request->isSubscribed;

        // Check if VendorUser exists
        $vendorUser = VendorUsers::where('email', $email)->first();
        
        if (!$vendorUser) {
            // Check if User exists to avoid duplicate entry error
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $email, // Default name as email
                    'email' => $email,
                    'password' => Hash::make($password),
                    'isSubscribed' => $isSubscribed
                ]);
            } else {
                 // Update subscription if needed
                 $user->update(['isSubscribed' => $isSubscribed]);
            }

            DB::table('vendor_users')->insert([
                'user_id' => $user->id,
                'uuid' => $uuid,
                'email' => $email,
            ]);
        } else {
             User::where('email', $email)->update([
                'isSubscribed' => ($isSubscribed == null) ? '' : $isSubscribed
            ]);
        }

        $user = User::where('email', $email)->first();
        Auth::login($user, true);
        
        return ['access' => Auth::check()];
    }
    public function setSubcriptionFlag(Request $request)
    {
        User::where('email', $request->email)->update([
            'isSubscribed' => $request->isSubscribed
        ]);

        $data = array();
        if (Auth::check()) {
            $data['access'] = true;
        }


        return $data;
    }


    public function logout(Request $request)
    {

        $user_id = Auth::user()->user_id;
        $user = VendorUsers::where('user_id', $user_id)->first();

        try {
            Auth::logout();
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }

        $data1 = array();
        if (! Auth::check()) {
            $data1['logoutuser'] = true;
        }
        return $data1;
    }

}
