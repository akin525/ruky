<?php

namespace app\Http\Controllers\Api;

use App\Models\bo;
use App\Models\data;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BillController;

class EkectController
{
    public function listelect()
    {

        $tv = data::where('plan','elect')->get();

        return response()->json([
            'message' => "electricity fetch successfuly", 'data' => $tv
        ], 200);

    }
    public function electric(Request $request)
    {
        $apikey = $request->header('apikey');
        $user = User::where('apikey',$apikey)->first();
        if ($user) {
            $tv = data::where('plan', 'elect')->get();

            return response()->json([
                'message' => "fecthed", 'user'=>$user, 'tv'=>$tv
            ], 200);

        }
        return response()->json([
            'message' => "User not found",
        ], 200);

    }
    public function verifyelect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cat_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => BillController::error_processor($validator)
            ], 403);
        }
        $apikey = $request->header('apikey');
        $user = User::where('apikey',$apikey)->first();
        if ($user) {
            $tv = data::where('cat_id', $request->cat_id)->first();


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mcd.5starcompany.com.ng/api/reseller/validate',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('service' => 'electricity', 'coded' => $tv->cat_id, 'phone' => $request->number),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: MCDKEY_903sfjfi0ad833mk8537dhc03kbs120r0h9a'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
//            return $response;
            $data = json_decode($response, true);
            $success= $data["success"];
            $name=$data["data"];
            if ($success = 1){
                $log=$name;
            }else{
                $log= "Unable to Identify meter Number";
            }
            return $response;


        }
        return response()->json([
            'message' => "User not found",
        ], 200);
    }
    public function payelect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cat_id' => 'required',
            'refid' => 'required',
            'amount' => 'required',
            'number' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => BillController::error_processor($validator)
            ], 403);
        }
        $apikey = $request->header('apikey');
        $user = User::where('apikey',$apikey)->first();
        if ($user) {
            $tv = data::where('plan_id', $request->cat_id)->first();

            $wallet = wallet::where('username', $user->username)->first();


            if ($wallet->balance < $request->amount) {
                $mg = "You Cant Make Purchase Above" . "NGN" . $request->amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";

                return response()->json([
                    'message' => $mg, 'user'=>$user
                ], 200);

            }
            if ($request->amount < 0) {

                $mg = "error transaction";
                return response()->json([
                    'message' => $mg, 'user'=>$user
                ], 200);

            }
            $bo = bo::where('refid', $request->refid)->first();
            if (isset($bo)) {
                $mg = "duplicate transaction";
                return response()->json([
                    'message' => $mg, 'user'=>$user
                ], 200);

            } else {
                $gt = $wallet->balance - $request->amount;


                $wallet->balance = $gt;
                $wallet->save();
                $resellerURL = 'https://integration.mcd.5starcompany.com.ng/api/reseller/';


                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $resellerURL.'pay',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('service' => 'electricity', 'coded' => $tv->plan_id, 'phone' => $request->number, 'amount' => $request->amount),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: mcd_key_75rq4][oyfu545eyuriup1q2yue4poxe3jfd'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
//                return $response;

                $data = json_decode($response, true);
                $success = $data["success"];


//                        return $response;
                if ($success == 1) {
                    $tran1 = $data["discountAmount"];
                    $tran2 = $data["token"];

                    $bo = bo::create([
                        'username' => $user->username,
                        'plan' => $tv->network,
                        'amount' => $request->amount,
                        'server_res' => $response,
                        'result' => $success,
                        'phone' => $request->number,
                        'refid' =>"Api". $request->refid,
                        'discountamoun' => $tran1,
                        'token' => $tran2,
                    ]);

                }elseif ($success==0){
                    $zo=$user->balance+$tv->tamount;
                    $user->balance = $zo;
                    $user->save();

                }

                return $response;
            }
        }
        return response()->json([
            'message' => "User not found",
        ], 200);
    }

}
