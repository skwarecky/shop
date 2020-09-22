<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Response as Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class Register extends Controller
{
    /*
    Register API
    Name, email and password input. 
    */
    public function register(Request $request){
        $input = $request;
        // dd($input);
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return Response::sendError('Validation Error.', $validator->errors());
        }
        if(User::where('email', $request->email)->first()){
            return Response::sendError('User exists.', 'Use diffrent email.');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] = $user->createToken('productApp')->accessToken;
            $success['name'] = $user->name;
    
            return Response::sendResponse($success, 'User register successfully');
        }
    }
}
