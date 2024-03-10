<?php

namespace App\Http\Controllers;

use App\Models\transaction;
use Illuminate\Support\Facades\Auth;

class NotificationController
{
public function loadtransaction()
{
    $no=transaction::where('username', Auth::user()->username)->orderBy('id', 'desc')->get();

    return view('notification', compact('no'));

}
public function cleartransaction()
{
    $no=transaction::where('username', Auth::user()->username)->delete();

    return response()->json([
        'status'=>'success',
        'message'=>'Notification cleared'
    ]);

}

}
