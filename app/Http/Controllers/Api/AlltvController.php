<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BillController;
use App\Models\bo;
use App\Models\data;
use App\Models\Messages;
use App\Models\refer;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AlltvController
{
    public function listtv()
    {

        $tv = data::where('plan','tv')->get();

        return response()->json([
            'message' => "tv fetch successfuly", 'data' => $tv
        ], 200);

    }

    public function verifytv(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'productid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => BillController::error_processor($validator)
            ], 403);
        }
//        return $request;
        $ve=data::where('plan_id', $request->productid)->first();
//        return $request;

//return $ve;
        $resellerURL='https://integration.mcd.5starcompany.com.ng/api/reseller/';


        $curl = curl_init();


        curl_setopt_array($curl, array(

            CURLOPT_URL => $resellerURL.'validate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('service' => 'tv', 'coded' =>$ve->plan_id, 'phone' => $request->number),
            CURLOPT_HTTPHEADER => array(
                'Authorization: MCDKEY_903sfjfi0ad833mk8537dhc03kbs120r0h9a'
            )
        ));

                $response = curl_exec($curl);

        curl_close($curl);
//        echo $response;
//return $response;
        $data = json_decode($response, true);
        $success= $data["success"];
        $name=$data["data"];
        if ($success = 1){
            $log=$name;
        }else{
            $log= "Unable to Identify IUC Number";
        }
        return response()->json([
            'message' => $log, 'request'=>$request, 'name'=>$name
        ], 200);


    }
//    public function process(Request $request)
//    {
//        if (Auth::check()) {
//            $user = User::find($request->user()->id);
//            $tv = data::where('id', $request->id)->first();
//
//            return  view('tvp', compact('user', 'request'));
//
//        }
//        return redirect("login")->withSuccess('You are not allowed to access');
//
//    }
    public function tv(Request $request)
    {

        $apikey = $request->header('apikey');
        $user = User::where('apikey',$apikey)->first();
        if ($user) {
            $tv = data::where('plan', 'tv')->get();
            return response()->json([
                'message' => "fecthed", 'user'=>$user, 'tv'=>$tv
            ], 200);

        }
        return response()->json([
            'message' => "User not found",
        ], 200);

    }

        public function paytv(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'coded' => 'required',
                'refid' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => BillController::error_processor($validator)
                ], 403);
            }
            $apikey = $request->header('apikey');
            $user = User::where('apikey',$apikey)->first();
            if ($user) {
                $tv = data::where('plan_id', $request->coded)->first();
//                return $tv;

                $wallet = wallet::where('username', $user->username)->first();


                if ($wallet->balance < $tv->tamount) {
                    $mg = "You Cant Make Purchase Above" . "NGN" . $tv->tamount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";
                    return response()->json([
                        'success'=>0, 'message' => $mg, 'user'=>$user
                    ], 200);

                }
                if ($tv->tamount < 0) {

                    $mg = "error transaction";
                    return response()->json([
                        'success'=>0,  'message' => $mg, 'user'=>$user
                    ], 200);

                }
                $bo = bo::where('refid', $request->refid)->first();
                if (isset($bo)) {
                    $mg = "duplicate transaction";
                    return response()->json([
                        'success'=>0, 'message' => $mg, 'user'=>$user
                    ], 200);

                } else {
                    $gt = $wallet->balance - $tv->tamount;


                    $wallet->balance = $gt;
                    $wallet->save();

                    $resellerURL = 'https://integration.mcd.5starcompany.com.ng/api/reseller/';
                    $test= "https://test.mcd.5starcompany.com.ng/api/reseller";
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
                        CURLOPT_POSTFIELDS => array('service' => 'tv', 'coded' => $tv->plan_id, 'phone' => $request->phone),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: mcd_key_75rq4][oyfu545eyuriup1q2yue4poxe3jfd'

                        )
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
//                   return $response;

                    $data  = json_decode($response, true);
                    $success = $data["success"];
                    $tran1 = $data["discountAmount"];

//                        return $response;
                    if ($success == 1) {

                        $bo = bo::create([
                            'username' => $user->username,
                            'plan' => $tv->network,
                            'amount' => $tv->tamount,
                            'server_res' => $response,
                            'result' => $success,
                            'phone' => $request->number,
                            'refid' =>"Api". $request->refid,
                            'discountamoun' => $tran1,
                        ]);


                        $name = $tv->plan;
                        $am = $tv->network."was Successful to";
                        $ph = $request->number;


                        return response()->json([
                            'user'=>$user, 'name'=>$name, 'am'=>$am, 'ph'=>$ph, 'success'=>$success
                        ], 200);


                    }elseif ($success==0){
                        $zo=$user->balance+$tv->tamount;
                        $user->balance = $zo;
                        $user->save();

                        $name= $tv->network;
                        $am= "NGN $request->amount Was Refunded To Your Wallet";
                        $ph=", Transaction fail";
                        return response()->json([
                            'user'=>$user, 'name'=>$name, 'am'=>$am, 'ph'=>$ph, 'success'=>$success
                        ], 200);

                    }
                }
            }
            return response()->json([
                'message' => "User not found",
            ], 200);

        }

}
