<?php

namespace app\Http\Controllers;

use App\Console\encription;
use App\Mail\Emailotp;
use App\Mail\Emailtrans;
use App\Models\big;
use App\Models\bill;
use App\Models\bill_payment;
use App\Models\bo;
use App\Models\data;
use App\Models\Messages;
use App\Models\refer;
use App\Models\transaction;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AlltvController
{
    public function listtv(Request $request)
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://integration.mcd.5starcompany.com.ng/api/reseller/list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('service' => 'tv'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: MCDKEY_903sfjfi0ad833mk8537dhc03kbs120r0h9a'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        echo $response;
        return $response;
        $data = json_decode($response, true);
        $plan1= $data["data"];
        foreach ($plan1 as $plan){
            $success =$plan["type"];
            $planid = $plan["code"];
            $price= $plan['amount'];
            $allowance=$plan['name'];
            $insert= data::create([
                'plan_id' =>$planid,
                'network' =>$success,
                'name' =>$allowance,
                'server'=>'1',
                'amount'=>$price,
                'tamount'=>$price,
                'ramount'=>$price,
            ]);
        }

    }

    public function verifytv($value1, $value2)
    {

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
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('service' => 'tv', 'coded' =>$value2, 'phone' => $value1),
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
        if($success== 1){
            $name=$data["data"];

            $log=$name;
        }else{
            $log= $response;
        }
        return response()->json($log);

    }

    public function tv(Request $request, $selectedValue)
    {

            $user = User::find($request->user()->id);
            $tv = data::where('network',$selectedValue)->get();
        return response()->json($tv);
    }

        public function paytv(Request $request)
        {
            if (Auth::check()) {
                $user = User::find($request->user()->id);
                $tv = data::where('id', $request->productid)->first();

                $wallet = wallet::where('username', $user->username)->first();


                if ($wallet->balance < $tv->tamount) {
                    $mg = "You Cant Make Purchase Above" . "NGN" . $tv->tamount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";

                    return response()->json( $mg, Response::HTTP_BAD_REQUEST);

                }
                if ($tv->tamount < 0) {

                    $mg = "error transaction";
                    return response()->json( $mg, Response::HTTP_BAD_REQUEST);

                }
                $bo = bill::where('refid', $request->refid)->first();
                if (isset($bo)) {
                    $mg = "duplicate transaction";
                    return response()->json( $mg, Response::HTTP_CONFLICT);

                } else {
                    $gt = $wallet->balance - $tv->tamount;


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
                        CURLOPT_POSTFIELDS => array('service' => 'tv', 'coded' => $tv->cat_id, 'phone' => $request->number),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
                        )
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
//                    echo $response;
                    $data = json_decode($response, true);
                    $success = $data["success"];


//                        return $response;
                    if ($success == 1) {
                        $tran1 = $data["discountAmount"];

                        $bo =bill::create([
                            'username' => $user->username,
                            'product' => $tv->name,
                            'amount' => $tv->tamount,
                            'server_response' => $response,
                            'status' => $success,
                            'phone' => $request->number,
                            'transactionid' => $request->refid,
                            'discountamount' => $tran1,
                            'paymentmethod'=>'wallet',
                        ]);
                        $transaction=transaction::create([
                            'username'=>$user->username,
                            'activities'=>'Being Purchase Of '.$bo['product'].' on '.$request->number,
                        ]);

                        $name = $tv->plan;
                        $am = $tv->network."was Successful to";
                        $ph = $request->number;

                        return response()->json([
                            'status' => 'success',
                            'message' => $am.' ' .$ph,
//                            'data' => $responseData // If you want to include additional data
                        ]);

                    }elseif ($success==0){
                        $zo=$user->balance+$tv->tamount;
                        $user->balance = $zo;
                        $user->save();

                        $name= $tv->network;
                        $am= "NGN $request->amount Was Refunded To Your Wallet";
                        $ph=", Transaction fail";

                        return response()->json([
                            'status' => 'fail',
                            'message' => $am.' ' .$ph,
//                            'data' => $responseData // If you want to include additional data
                        ]);
                    }
                }
            }
        }

    public function createemail(Request $request)
    {
        $input['email']="uyewye";
        $input['username']="ewhdhede";
        $input['name']="hdhhsfdf";
        $input['password']="shsajhfshf";

        Mail::to("odejinmiabraham@gmail.com")->send(new Emailotp($input));
        return "Hello guys how are you";
    }

}
