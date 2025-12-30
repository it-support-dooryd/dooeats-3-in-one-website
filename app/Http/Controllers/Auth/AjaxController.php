<?php

/**
 * File name: AjaxController.php
 * Last modified: 2020.06.11 at 16:10:52
 * AjaxController
 * Copyright (c) 2020
 */


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Prettus\Validator\Exceptions\ValidatorException;


class AjaxController extends Controller

{

    public function checkEmail(Request $request)
    {

        $response = array();

        if (User::where('email', $request->email)->exists()) {
            $response['exist'] = 'yes';
        } else {
            $response['exist'] = 'no';
        }

        return response()->json($response);
    }


    public function setToken(Request $request)
    {

        $userId = $request->userId;
        $uuid = $request->id;
        $password = $request->password;
        $exist = VendorUsers::where('email', $request->email)->get();
        $data = $exist->isEmpty();

        if ($exist->isEmpty()) {

            $user = User::create([
                'name' => $request->email,
                'email' => $request->email,
                'password' => Hash::make($password),
            ]);

            DB::table('vendor_users')->insert([
                'user_id' => $user->id,
                'uuid' => $uuid,
                'email' => $request->email,
            ]);

        } else {
            
            $user = VendorUsers::select('id')->where('email', $request->email)->first();

            $user = VendorUsers::find($user->id);

            $user->uuid = $uuid;
            $user->email = $request->email;

            $user->save();

        }

        $user = User::where('email', $request->email)->first();
        Auth::login($user, true);
        $data = array();
        if (Auth::check()) {

            $data['access'] = true;
        }


        return $data;
    }

    public function setTokenOLD(Request $request)
    {


        $userId = $request->userId;

        $uuid = $request->id;

        $password = $request->password;

        $exist = VendorUsers::where('user_id', $userId)->get();

        $data = $exist->isEmpty();

        if ($exist->isEmpty()) {

            DB::table('vendor_users')->insert([

                'user_id' => $userId,

                'uuid' => $uuid,

                'email' => $request->email,

            ]);


            User::create([

                'name' => $request->email,

                'email' => $request->email,

                'password' => Hash::make($password),

            ]);


        } else {


        }

        $user = User::where('email', $request->email)->first();

        Auth::login($user, true);

        $data = array();

        if (Auth::check()) {


            $data['access'] = true;

        }


        return $data;


    }


    public function logoutOLD(Request $request)
    {


        $user_id = Auth::user()->user_id;

        $user = VendorUsers::where('user_id', $user_id)->first();


        try {

            Auth::logout();
            return redirect('/login');

        } catch (\Exception $e) {

            $this->sendError($e->getMessage(), 401);

        }


        $data1 = array();

        if (!Auth::check()) {

            $data1['logoutuser'] = true;

        }

        return $data1;

    }

    public function logout(Request $request)
    {

        $user_id = Auth::user()->user_id;
        $user = VendorUsers::where('user_id', $user_id)->first();

        try {
            Auth::logout();
            return redirect('/login');
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }

        $data1 = array();
        if (!Auth::check()) {
            $data1['logoutuser'] = true;
        }
        return $data1;
    }

    public function newRegister(Request $request)
    {
        $userId = $request->userId;

        $password = $request->password;

        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            $user = $existingUser;
        } else{
            $user = User::create([
                'name' => $request->email,
                'email' => $request->email,
                'password' => Hash::make($password),
            ]);
        }

        $existingVendor = DB::table('vendor_users')->where('email', $request->email)->first();
        if ($existingVendor) {
            DB::table('vendor_users')
                ->where('email', $request->email)
                ->update([
                    'user_id' => $user->id,
                    'uuid' => $userId
                ]);
        } else{
            DB::table('vendor_users')->insert([
                'user_id' => $user->id,
                'uuid' => $userId,
                'email' => $request->email,
            ]);
        }


        $user = User::where('email', $request->email)->first();


        Auth::login($user, true);

        $signupdata = array();

        if (Auth::check()) {

            $signupdata['access'] = true;

        }

        return $signupdata;

    }

    public function verifyRecaptcha(Request $request)
    {
        $token = $request->input('token');
        $action = $request->input('action', 'LOGIN');
        $apiKey = env('RECAPTCHA_API_KEY', config('firebase.api_key'));
        $projectId = config('firebase.project_id', 'dooeats-c690f');
        $siteKey = '6LcKSzosAAAAADS4s80I4QKaDK0ub7tkwRuwSrLd';

        $url = "https://recaptchaenterprise.googleapis.com/v1/projects/{$projectId}/assessments?key={$apiKey}";

        $data = [
            'event' => [
                'token' => $token,
                'expectedAction' => $action,
                'siteKey' => $siteKey,
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $result = json_decode($response, true);
            // Check if token is valid and score is acceptable (if score based)
            // Enterprise returns 'tokenProperties' => ['valid' => true, ...]
            
            if (isset($result['tokenProperties']['valid']) && $result['tokenProperties']['valid'] === true) {
                // You can also check score here : $result['riskAnalysis']['score']
                return response()->json(['success' => true, 'score' => $result['riskAnalysis']['score'] ?? null]);
            } else {
                return response()->json(['success' => false, 'error' => 'Invalid Token', 'details' => $result], 200);
            }
        } else {
             return response()->json(['success' => false, 'error' => 'API Error', 'details' => $response], 500);
        }
    }


}

