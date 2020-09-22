<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Response as Response;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class Login extends Controller
{
    /*
    Login API 
    Email, and password input 
    */

    public function login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password'=> $request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('productApp')->accessToken;
            $success['name'] = $user->name;

            return Response::sendResponse($success, 'User login successfully');
        }
        else{
            return Response::sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}
