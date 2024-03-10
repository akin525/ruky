<?php

namespace App\Http\Controllers\Api;

use App\Console\encription;
use App\Models\bill_payment;
use App\Models\data;
use App\Models\neco;
use App\Models\server;
use App\Models\User;
use App\Models\waec;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class RducationController
{
public function indexw()
{
    $waec=data::where('network', 'WAEC')->first();
    $wa=waec::where('username', Auth::user()->username)->get();
return view('waec', compact('waec', 'wa'));

}
public function indexn()
{
    $neco=data::where('network', 'NECO')->first();
    $ne=neco::where('username', Auth::user()->username)->get();

    return view('neco', compact('neco', 'ne'));

}
public function waec(Request $request)
{
    $validator = Validator::make($request->all(), [
        'value' => 'required',
        'amount' => 'required',
        'refid' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $this->error_processor($validator)
        ], 403);
    }
    $apikey = $request->header('apikey');
    $user = User::where('apikey', $apikey)->first();
    if ($user) {
        $wallet = wallet::where('username', $user->username)->first();

        if ($wallet->balance < $request->amount) {
            $mg = "You Cant Make Purchase Above " . "NGN" . $request->amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";

            return response()->json([
                'message' => $mg,
                'user' => $user,
                'success' => 0
            ], 200);

        }
        if ($request->amount < 0) {

            $mg = "error transaction";
            return response()->json([
                'message' => $mg,
                'user' => $user,
                'success' => 0
            ], 200);

        }
        $bo = bill_payment::where('transactionid', $request->refid)->first();;
        if (isset($bo)) {
            $mg = "duplicate transaction";
            return response()->json([
                'message' => $mg,
                'user' => $user,
                'success' => 0
            ], 200);

        } else {
            $bt = data::where("plan_id", 'WAEC')->first();
            if (!isset($bt)) {
                return response()->json([
                    'message' => "invalid code, check and try again later",
                    'user' => $user,
                    'success' => 0
                ], 200);
            }
            $gt = $wallet->balance - $request->amount;


            $wallet->balance = $gt;
            $wallet->save();

            $bo = bill_payment::create([
                'username' => $user->username,
                'product' => $bt->network,
                'amount' => $request->amount,
                'server_response' => 'ur fault',
                'status' => 1,
                'number' => $request->number,
                'transactionid' => $request->refid,
                'discountamount' => 0,
                'paymentmethod' => 'wallet',
                'balance' => $gt,
            ]);
            $bo['name'] = encription::decryptdata($user->name);
            $resellerURL = 'https://integration.mcd.5starcompany.com.ng/api/reseller/';
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $resellerURL . 'pay',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('service' => 'result_checker', 'coded' => 'WAEC', 'quantity' => $request->value, 'phone' => encription::decryptdata($user->phone)),

                CURLOPT_HTTPHEADER => array(
                    'Authorization: mcd_key_75rq4][oyfu545eyuriup1q2yue4poxe3jfd'
                )));


            $response = curl_exec($curl);

            curl_close($curl);
//                echo $response;
            $data = json_decode($response, true);
            $success = $data["success"];

            if ($success == 1) {
                $ref = $data['ref'];
                $token = $data['token'];
                $token1 = json_decode($token, true);
//return $token1;
                foreach ($token1 as $to) {

                    $insert = waec::create([
                        'username' => $user->username,
                        'seria' => $to['serial_number'],
                        'pin' => $to['pin'],
                        'ref' => $ref,
                    ]);
                }
                return response()->json([

                    'ok'=>$data
                ], 200);

            } elseif ($success == 0) {

                return response()->json([
                   'ok'=> $data
                ], 200);
            }
        }

    }
}
public function neco(Request $request)
{
    $validator = Validator::make($request->all(), [
        'value' => 'required',
        'amount' => 'required',
        'refid' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success'=>0,
            'errors' => $this->error_processor($validator)
        ], 403);
    }
    $apikey = $request->header('apikey');
    $user = User::where('apikey', $apikey)->first();
    if ($user) {
        $wallet = wallet::where('username', $user->username)->first();

        if ($wallet->balance < $request->amount) {
            $mg = "You Cant Make Purchase Above " . "NGN" . $request->amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";

            return response()->json([
                'message' => $mg,
                'user' => $user,
                'success' => 0
            ], 200);

        }
        if ($request->amount < 0) {

            $mg = "error transaction";
            return response()->json([
                'message' => $mg,
                'user' => $user,
                'success' => 0
            ], 200);

        }
        $bo = bill_payment::where('transactionid', $request->refid)->first();;
        if (isset($bo)) {
            $mg = "duplicate transaction";
            return response()->json([
                'message' => $mg,
                'user' => $user,
                'success' => 0
            ], 200);

        } else {
            $bt = data::where("plan_id", 'NECO')->first();
            if (!isset($bt)) {
                return response()->json([
                    'message' => "invalid code, check and try again later",
                    'user' => $user,
                    'success' => 0
                ], 200);
            }
            $gt = $wallet->balance - $request->amount;


            $wallet->balance = $gt;
            $wallet->save();

            $bo = bill_payment::create([
                'username' => $user->username,
                'product' => $bt->network,
                'amount' => $request->amount,
                'server_response' => 'ur fault',
                'status' => 1,
                'number' => $request->number,
                'transactionid' => $request->refid,
                'discountamount' => 0,
                'paymentmethod' => 'wallet',
                'balance' => $gt,
            ]);
            $bo['name'] = encription::decryptdata($user->name);
            $resellerURL = 'https://integration.mcd.5starcompany.com.ng/api/reseller/';
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $resellerURL . 'pay',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('service' => 'result_checker', 'coded' => 'NECO', 'quantity' => $request->value, 'phone' => encription::decryptdata($user->phone)),

                CURLOPT_HTTPHEADER => array(
                    'Authorization: mcd_key_75rq4][oyfu545eyuriup1q2yue4poxe3jfd'
                )));


            $response = curl_exec($curl);

            curl_close($curl);
//                echo $response;
            $data = json_decode($response, true);
            $success = $data["success"];

            if ($success == 1) {
                $ref = $data['ref'];
                $token = $data['token'];
                $token1 = json_decode($token, true);
//return $token1;
                foreach ($token1 as $to) {

                    $insert = neco::create([
                        'username' => $user->username,
                        'pin' => $to['pin'],
                        'ref' => $ref,
                    ]);
                }
                return response()->json([

                    'ok'=>$data

                ], 200);

            } elseif ($success == 0) {

                return response()->json([
                    'ok'=>$data

                ], 200);
            }
        }

    }
}
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }
}

