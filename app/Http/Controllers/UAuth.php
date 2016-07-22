<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\User;
use DateTime;
use DateInterval;
class UAuth extends Controller
{
    public function checktoken()
    {
        $user=Auth::Guard("api")->User();
        if (strtotime ($user->api_token_expires)>strtotime (date('Y-m-d H:i:s'))){
            return true;
        }
        return false;
    }
    public function renew($min)
    {
        $date = new DateTime(date('Y-m-d H:i:s'));
        $date->add(new DateInterval('PT'.$min.'M'));
        $user=Auth::User();
        $user->api_token_expires = $date->format('Y-m-d H:i:s');
        $user ->save();
        return $date->format('Y-m-d H:i:s');
    }
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            Uauth::renew(30);
            $user=Auth::User();
            return $user;
        }
        return abort(400);
    }
    public function changpasswd(Request $request)
    {
        if (Uauth::checktoken()==0){
            return abort(401);
        }
        $user=Auth::guard('api')->User();
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')]))
        {
            $user->password = bcrypt($request->input('new'));
            $user->save();
            return "{'name':'".$user->name."','email':'".$user->email."', 'password':'".$request->input('new')."'}";
        }
            return "{'error':'Podano niepoprawne hasło'}";
    }
    public function logout()
    {
        $date = new DateTime(date('Y-m-d H:i:s'));
        $user=Auth::Guard("api")->User();
        $user->api_token_expires = $date->format('Y-m-d H:i:s');
        $user ->save();
        return "Wylogowano!";
    }
}
