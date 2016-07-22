<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Auth;
use DateTime;
use DateInterval;
use Entrust;

class ApiController extends Controller
{
	const ROLE_ADMIN = "admin";
	const ROLE_USER = "user";

	protected function isAuthenticated() 
	{
        $user=Auth::guard('api')->User();
        if (strtotime ($user->api_token_expires) > time()) {
            return true;
        }
		return false;
	}
}