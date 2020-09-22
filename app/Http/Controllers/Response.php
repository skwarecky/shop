<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class Response extends Controller
{
    public static function sendResponse($result, $message){
        $response = [
            'success'   => true, 
            'data'      => $result,
            'message'   => $message,
        ];
        return response()->json($response, 200);
    }

    public static function sendError($error, $errorMessages = [], $code = 404){
        $response = [
            'success'   => false,
            'message'   => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
}
