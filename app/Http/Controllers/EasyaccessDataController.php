<?php

namespace App\Http\Controllers;

use App\Mail\Emailtrans;
use App\Models\bill;
use App\Models\data;
use App\Models\easy;
use App\Models\profit;
use App\Models\transaction;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class EasyaccessDataController
{
    function sentpick($request)
    {
        $netm=data::where('network', $request)->get();
        $neta=easy::where('network', $request)->get();
        $net9=easy::where('network', $request)->get();
        $netg=easy::where('network', $request)->get();

        return view('bills.data', compact('net9', 'neta', 'netg', 'netm'));


    }
public function loadeasydata($selectedValue)
{
    $options = easy::where('network', $selectedValue)->get();
    return response()->json($options);

}

function sellfromeasyaccess(Request $request)
{
    $request->validate([
        'productid' => 'required',
        'number'=>['required', 'numeric',  'digits:11'],
        'refid' => 'required',
    ]);
    $user = User::find($request->user()->id);
    $wallet = wallet::where('username', $user->username)->first();
    $product = easy::where('id', $request->productid)->first();
    if ($user->apikey == '') {
        $amount = $product->tamount;
    } elseif ($user != '') {
        $amount = $product->ramount;
    }
    if ($wallet->balance < $amount) {
        $mg = "You Cant Make Purchase Above" . "NGN" . $amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";


        return response()->json( $mg, Response::HTTP_BAD_REQUEST);

    }
    if ($request->amount < 0) {

        $mg = "error transaction";
        return response()->json($mg, Response::HTTP_BAD_REQUEST);


    }
    $bo = bill::where('transactionid', $request->refid)->first();
    if (isset($bo)) {
        $mg = "duplicate transaction";
        return response()->json( $mg, Response::HTTP_CONFLICT);


    } else {

        $fbalance=$wallet->balance;

        $gt = $wallet->balance - $amount;


        $wallet->balance = $gt;
        $wallet->save();
        $bo = bill::create([
            'username' => $user->username,
            'product' => $product->network . '|' . $product->name,
            'amount' => $product->tamount,
            'server_response' => 'ur fault',
            'status' => 0,
            'number' => $request->number,
            'transactionid' => $request->refid,
            'discountamount'=>0,
            'paymentmethod'=> 'wallet',
            'fbalance'=>$fbalance,
            'balance'=>$gt,
        ]);

        $transaction=transaction::create([
            'username'=>$user->username,
            'activities'=>'Being Purchase Of' .$product->plan.' to '.$request->number,
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://easyaccess.com.ng/api/data.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'network' =>$product->plan_id,
                'mobileno' => $request->number,
                'dataplan' => $product->code,
                'client_reference' => $request->id, //update this on your script to receive webhook notifications
            ),
            CURLOPT_HTTPHEADER => array(
                "AuthorizationToken: 13260b43d867388de4a1efc4265fa556", //replace this with your authorization_token
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }


    $data = json_decode($response, true);
//                    return $response;
//    return response()->json($data, Response::HTTP_BAD_REQUEST);
    $success = "";
    if ($data['success'] == 'true') {
        $success = 1;
        $ms = $data['message'];

//                    echo $success;

        $po = $amount - $product->amount;


        $profit = profit::create([
            'username' => $user->username,
            'plan' => $product->network . '|' . $product->plan,
            'amount' => $po,
        ]);

        $update = bill::where('id', $bo->id)->update([
            'server_response' => $response,
            'status' => 1,
        ]);
        $name = $product->plan;
        $am = "$product->plan  was successful delivered to";
        $ph = $request->number;

        $receiver = $user->email;
        $admin = 'info@efemobilemoney.com';

        Mail::to($receiver)->send(new Emailtrans($bo));
        Mail::to($admin)->send(new Emailtrans($bo));
        return response()->json([
            'status' => 'success',
            'message' => $am.' '.$ph,
            'id'=>$bo['id'],
        ]);

    } elseif ($data['success'] == 'false') {
        $success = 0;
        $zo = $wallet->balance + $request->amount;
        $wallet->balance = $zo;
        $wallet->save();

        $name = $product->plan;
        $am = "NGN $request->amount Was Refunded To Your Wallet";
        $ph = ", Transaction fail";
        return response()->json([
            'status' => 'fail',
            'message' => $response,
//                            'data' => $responseData // If you want to include additional data
        ]);
    }

    }
}
