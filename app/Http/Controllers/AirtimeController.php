<?php

namespace App\Http\Controllers;

use App\Mail\Emailtrans;
use App\Models\airtimecons;
use App\Models\bill;
use App\Models\Comission;
use App\Models\data;
use App\Models\server;
use App\Models\transaction;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class AirtimeController
{

    public function buyairtime(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'amount'=>'required',
            'number'=>['required', 'numeric',  'digits:11'],
            'refid'=>'required',
        ]);
//        return response()->json( $request, Response::HTTP_BAD_REQUEST);

        $user = User::where('username', Auth::user()->username)->first();
        $wallet = wallet::where('username', $user->username)->first();
        if ($wallet->balance < $request->amount) {
            $mg = "You Cant Make Purchase Above" . "NGN" . $request->amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";
            return response()->json( $mg, Response::HTTP_BAD_REQUEST);

        }
        if ($request->amount < 0) {

            $mg = "error transaction";
            return response()->json($mg, Response::HTTP_BAD_REQUEST);
        }
        $bo1 = bill::where('transactionid', $request->refid)->first();
        if (isset($bo1)) {
            $mg = "duplicate transaction, kindly reload this page";
            return response()->json( $mg, Response::HTTP_CONFLICT);


        } else {

            $user = User::find($request->user()->id);
            $wallet = wallet::where('username', $user->username)->first();
            $per=2/100;
            $comission=$per*$request->amount;

            $fbalance=$wallet->balance;

            $gt = $wallet->balance - $request->amount;
            $wallet->balance = $gt;
            $wallet->save();

                    $bo = bill::create([
                        'username' => $user->username,
                        'product' => $request->id.'Airtime',
                        'amount' => $request->amount,
                        'server_response' => 0,
                        'status' => 0,
                        'number' => $request->number,
                        'paymentmethod'=>'wallet',
                        'transactionid' => $request->refid,
                        'discountamount' => 0,
                        'fbalance'=>$fbalance,
                        'balance'=>$gt,
                    ]);

                    $transaction=transaction::create([
                        'username'=>$user->username,
                        'activities'=>'Being Purchase Of Airtime to '.$request->number,
                    ]);

                    $comiS=Comission::create([
                        'username'=>Auth::user()->username,
                        'amount'=>$comission,
                    ]);
                    $bo['name']=$user->name;
                    $bo['email']=Auth::user()->email;

            $daterserver = new AirtimeserverController();
            $mcd = airtimecons::where('status', "1")->first();

           if ($mcd->server == "mcd"){
                $response = $daterserver->mcdbill1($request);

                $data = json_decode($response, true);

               $success = $data["success"];
                if ($success == 1) {

                    $update=bill::where('id', $bo->id)->update([
                        'server_response'=>$response,
                        'status'=>1,
                    ]);
                    $am = "NGN $request->amount  Airtime Purchase Was Successful To";
                    $ph = $request->number;

                    $com=$wallet->bonus+$comission;
                    $wallet->bonus=$com;
                    $wallet->save();

                    $parise=$comission."₦ Commission Is added to your wallet balance";
                    $receiver = $user->email;
                    $admin = 'info@efemobilemoney.com';


                    Mail::to($receiver)->send(new Emailtrans($bo));
                    Mail::to($admin)->send(new Emailtrans($bo));
                    return response()->json([
                        'status' => 'success',
                        'message' => $am.' ' .$ph.' & '.$parise,
//                            'data' => $responseData // If you want to include additional data
                    ]);
                } elseif ($success == 0) {

                    $am = "Contact your Admin";
                    $ph = ", Transaction fail";

//                        Alert::error('error', $am.' ' .$ph);
//                        return redirect()->route('viewpdf', $bo->id);

                    return response()->json([
                        'status' => 'fail',
                        'message' => $response,
//                            'message' => $am.' ' .$ph,
//                            'data' => $responseData // If you want to include additional data
                    ]);
                }
            }elseif ($mcd->server == "easyaccess"){
                $response = $daterserver->easyaccess($request);
                $data = json_decode($response, true);

                if ($data['success']== 'true') {

                    $success=1;
                    $update=bill::where('id', $bo->id)->update([
                        'server_response'=>$response,
                        'status'=>1,
                    ]);
                    $name = "Airtime";
                    $am = "NGN $request->amount  Airtime Purchase Was Successful To";
                    $ph = $request->number;

                    $receiver = $user->email;
                    $admin = 'info@amazingdata.com.ng';

                    Mail::to($receiver)->send(new Emailtrans($bo));
                    Mail::to($admin)->send(new Emailtrans($bo));
                    return response()->json([
                        'status' => 'success',
                        'message' => $am.' '.$ph,
                        'id'=>$bo['id'],
                    ]);
                } elseif ($data['message']== 'Possible duplicate transaction, Please retry after 2 minutes') {
                    $zo = $user->balance + $request->amount;
                    $user->balance = $zo;
                    $user->save();
                    $success=0;
                    $name = 'Airtime';
                    $am = "NGN $request->amount Was Refunded To Your Wallet";
                    $ph = ", Possible duplicate transaction, Please retry after 2 minutesl";

                    return response()->json([
                        'status' => 'fail',
                        'message' => $am.' ' .$ph,
//                            'data' => $responseData // If you want to include additional data
                    ]);

                } elseif ($data['success']== 'false') {
                    $zo = $user->wallet + $request->amount;
                    $user->wallet = $zo;
                    $user->save();
                    $success=0;
                    $name = 'Airtime';
                    $am = "NGN $request->amount Was Refunded To Your Wallet";
                    $ph = ", Transaction fail";
                    return response()->json([
                        'status' => 'fail',
                        'message' => $response,
//                            'message' => $am.' ' .$ph,
//                            'data' => $responseData // If you want to include additional data
                    ]);

                }
            }

                }

            }
}
