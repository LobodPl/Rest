<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Auth;
use DateTime;
use DateInterval;
use Entrust;
class Crud extends ApiController
{
    public function checktoken()
    {
        $user=Auth::guard('api')->User();
        if (strtotime ($user->api_token_expires)>strtotime (date('Y-m-d H:i:s'))){
            return true;
        }
        return false;
    }
    public function renew($min=30)
    {
        if ($this->checktoken()==0){
            return false;
        }
        $date = new DateTime(date('Y-m-d H:i:s'));
        $date->add(new DateInterval('PT'.$min.'M'));
        $user=Auth::guard('api')->User();
        $user->api_token_expires = $date->format('Y-m-d H:i:s');
        $user ->save();
        return $date->format('Y-m-d H:i:s');
    }
    public function index()
    {
        if(!$this->isAuthenticated()) {
            return abort(401);
        }
        Crud::renew(30);
        if (Auth::guard('api')->User()->hasRole("admin")){
            $data = User::all(array('id','name','email','api_token'));
            return $data;
        }
        return abort(403);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function newu(Request $request)
    {
        Crud::renew(30);
        if (Auth::guard('api')->user()->hasRole("admin")){
        Crud::create(array('name' => $request->name,'email' => $request->email,'password' => $request->password));
        $entry = user::where('name', $name)->first();
        return $entry;
        }
        return abort(403);
    }
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'api_token' => str_random(60),
        ]);
    }
    public function del($id)
    {
        Crud::renew(30);
        if (Auth::guard('api')->user()->hasRole("admin")){
            $del = User::find($id);
            $del->delete();
            return abort(204);
        }
        return abort(403);
    }
    public function detail($id)
    {
        Crud::renew(30);
        if (Auth::guard('api')->user()->hasRole("admin")){
            $entry = User::find($id);
            return $entry;
        }
        return abort(403);
    }
    public function update(Request $request, $id)
    {
        Crud::renew(30);
        if (Auth::guard('api')->user()->hasRole("admin")){
            $user = User::find($id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->save();
            return User::find($id);
        }
        return abort(403); 
    }
}