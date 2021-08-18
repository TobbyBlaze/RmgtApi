<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Seller;
use App\Models\Admin;
// use App\Courier;

class ResponseController extends Controller
{
    public function sendResponse($response)
    {
        return response()->json($response, 200);
    }


    public function sendError($error, $code = 404)
    {
    	$response = [
            'error' => $error,
        ];
        return response()->json($response, $code);
    }
}
