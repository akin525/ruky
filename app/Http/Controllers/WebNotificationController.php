<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebNotificationController extends Controller
{

    public function storeToken(Request $request)
    {
        auth()->user()->update(['device_key'=>$request->token]);
        return response()->json(['Token successfully stored.']);
    }
    //
}
