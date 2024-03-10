<?php

namespace App\Http\Controllers;
use App\Models\bo;
use App\Models\data;
use App\Models\deposit;
use App\Models\setting;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RealRashid\SweetAlert\Facades\Alert;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class ResellerController
{
    public function sell(Request $request)
    {
        if (Auth::check()) {
            $user = User::find($request->user()->id);
            $wallet = wallet::where('username', $user->username)->first();


//            $wallet->account_number = $number;-
//            $wallet->account_name = $account;
//            $wallet->save();

            return view('reseller.reseller', compact('user', 'wallet'));



        }
    }
    public function apiaccess(Request $request)
    {
        if (Auth::check()) {
            $user = User::find($request->user()->id);
            $wallet = wallet::where('username', $user->username)->first();


//            $wallet->account_number = $number;-
//            $wallet->account_name = $account;
//            $wallet->save();

            return view('reseller.upgrade', compact('user'));



        }
    }
    public function reseller(Request $request)
    {
        if (Auth::check()) {
            $user = User::find($request->user()->id);
            $wallet = wallet::where('username', $user->username)->first();


            if (Auth::user()->apikey != null){
                $mg="You are already a reseller";
                return response()->json($mg, Response::HTTP_BAD_REQUEST);

            }

            if ($wallet->balance < $request->amount) {
                $mg = "You Cant Upgrade Your Account" . "NGN" . $request->amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";

           return response()->json($mg, Response::HTTP_BAD_REQUEST);
            }
            if ($request->amount < 0) {

                $mg = "error transaction";
                return response()->json($mg, Response::HTTP_BAD_REQUEST);


            }else {
                $user = User::find($request->user()->id);
                $wallet = wallet::where('username', $user->username)->first();


                $gt = $wallet->balance - $request->amount;


                $wallet->balance = $gt;
                $wallet->save();


                $token = uniqid(Auth::user()->username,true);

                $user->apikey = $token;
                $user->save();
                $mg="You Are Now A Reseller";
                return response()->json([
                    'status'=>'auccess',
                    'message'=>$mg,
                ]);
            }


        }
    }
}
