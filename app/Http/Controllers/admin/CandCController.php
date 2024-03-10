<?php

namespace App\Http\Controllers\admin;

use App\Console\encription;
use App\Mail\Emailcharges;
use App\Mail\Emailfund;
use App\Models\charge;
use App\Models\charges;
use App\Models\charp;
use App\Models\deposit;
use App\Models\setting;
use App\Models\transaction;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class CandCController
{
    public function cr()
    {
        if (Auth()->user()->role == "admin") {

            $wallet = wallet::get();
            $totalwallet = 0;
            foreach ($wallet as $wall) {
                $totalwallet += (int)$wall->balance;

            }
            return view('admin/credit', compact('totalwallet'));
        }
        return redirect("admin/login")->with('status', 'You are not allowed to access');


    }
public function credit(Request $request)
{
    $request->validate([
        'username' => 'required',
        'amount' => 'required',
        'refid' => 'required',
    ]);
    if (Auth()->user()->role == "admin") {


        $user = User::where('username', $request->username)->first();
        if (!isset($user)){
            $mg='Username not found';
            return response()->json($mg, Response::HTTP_BAD_REQUEST);


        }
        $wallet = wallet::where('username', $request->username)->first();

        $depo = deposit::where('refid',$request->refid)->first();
        if (isset($depo)) {
            $mg= 'Duplicate Transaction';
            return response()->json($mg, Response::HTTP_CONFLICT);

        } else {
            $gt = $wallet->balance + $request->amount;
            $deposit = deposit::create([
                'username' => $request->username,
                'refid' => $request->refid,
                'narration'=>'Being Fund By Admin',
                'amount' => $request->amount,
                'iwallet' => $wallet->balance,
                'fwallet' => $gt,
            ]);
            $transaction=transaction::create([
                'username'=>$request->username,
                'activities'=>'Being Fund By Admin',
            ]);

            $wallet->balance = $gt;
            $wallet->save();
            $admin = 'info@efemobilemoney.com';

            $receiver = $user->email;
            Mail::to($receiver)->send(new Emailfund($deposit));
            Mail::to($admin)->send(new Emailfund($deposit));
            $mo=$request->username." was successful fund with NGN".$request->amount;

            return response()->json(['status'=>'success', 'message'=>$mo]);

        }
    }
    return redirect("admin/login")->with('status', 'You are not allowed to access');


}
public function refund(Request $request)
{
    $request->validate([
        'username' => 'required',
        'amount' => 'required',
        'refid' => 'required',
    ]);
    if (Auth()->user()->role == "admin") {


        $user = User::where('username', $request->username)->first();
        if (!isset($user)){
            $mg='Username not found';
            return response()->json($mg, Response::HTTP_BAD_REQUEST);


        }
        $wallet = wallet::where('username', $request->username)->first();

        $depo = deposit::where('refid',$request->refid)->first();
        if (isset($depo)) {
            $mg= 'Duplicate Transaction';
            return response()->json($mg, Response::HTTP_CONFLICT);

        } else {
            $gt = $wallet->balance + $request->amount;
            $transaction=transaction::create([
                'username'=>$request->username,
                'activities'=>'Being Re-Fund ₦'.$request->amount.' By Admin',
            ]);

            $wallet->balance = $gt;
            $wallet->save();
            $mo=$request->username." was successful re-fund with NGN".$request->amount;

            return response()->json(['status'=>'success', 'message'=>$mo]);

        }
    }
    return redirect("admin/login")->with('status', 'You are not allowed to access');


}
public function sp()
{
    $ch=charges::get();
    return view('admin/charge', compact('ch'));

}
public function charge(Request $request)
{
    $request->validate([
        'username' => 'required',
        'amount' => 'required',
        'refid' => 'required',
    ]);
    if (Auth()->user()->role == "admin") {
        $user = User::where('username', $request->username)->first();
        if (!isset($user)){

            return response()->json('User not found', Response::HTTP_BAD_REQUEST);

        }
        $wallet = wallet::where('username', $user->username)->first();


        $gt = $wallet->balance - $request->amount;
        $charp = charges::create([
            'username' => $user->username,
            'refid' => $request->refid,
            'amount' => $request->amount,
            'iwallet' => $wallet->balance,
            'fwallet' => $gt,
        ]);

        $wallet->balance = $gt;
        $wallet->save();

        $admin = 'info@efemobilemoney.com';

        $receiver = $user->email;

//        Mail::to($receiver)->send(new Emailcharges($charp));
//        Mail::to($admin)->send(new Emailcharges($charp));
        $mg=$request->amount . " was charge from " . $request->username . ' wallet successfully';
        return response()->json(['status'=>'success', 'message'=>$mg]);


    }
    return redirect("admin/login")->with('status', 'You are not allowed to access');

}

}
