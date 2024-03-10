<?php

namespace app\Http\Controllers;

use App\Console\encription;
use App\Mail\Emailtrans;
use App\Models\bill;
use App\Models\bill_payment;
use App\Models\bo;
use App\Models\data;
use App\Models\transaction;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EkectController
{
    public function listelect()
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
            CURLOPT_POSTFIELDS => array('service' => 'electricity'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: MCDKEY_903sfjfi0ad833mk8537dhc03kbs120r0h9a'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

        $data = json_decode($response, true);
        $plan1= $data["data"];
        foreach ($plan1 as $plan){
//            $success =$plan["type"];
            $planid = $plan["code"];
//            $price= $plan['amount'];
            $allowance=$plan['name'];
            $insert= data::create([
                'plan_id' =>$planid,
                'network' =>'elect',
                'name' =>$allowance,
                'server'=>1,
                'amount'=>0,
                'tamount'=>0,
                'ramount'=>0,
            ]);
        }
    }
    public function electric(Request $request)
    {
        if (Auth::check()) {
            $user = User::find($request->user()->id);
            $tv = data::where('network', 'elect')->get();

            return  view('bills.elect', compact('user', 'tv'));

        }
        return redirect("login")->withSuccess('You are not allowed to access');

    }
    public function verifyelect($value1, $value2)
    {
        if (Auth::check()) {
            $tv = data::where('id', $value2)->first();


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
                CURLOPT_POSTFIELDS => array('service' => 'electricity', 'coded' => $tv->plan_id, 'phone' => $value1),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: MCDKEY_903sfjfi0ad833mk8537dhc03kbs120r0h9a'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
//            echo $response;
            $data = json_decode($response, true);
            $success= $data["success"];
            if ($success == 1){
                $name=$data["data"];
                $log=$name;
            }else{
                $log= $response;
            }
            return response()->json($log);


        }
    }
    public function payelect(Request $request)
    {
        if (Auth::check()) {
            $user = User::find($request->user()->id);
            $tv = data::where('id', $request->id)->first();

            $wallet = wallet::where('username', $user->username)->first();


            if ($wallet->balance < $request->amount) {
                $mg = "You Cant Make Purchase Above" . "NGN" . $request->amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";
                return response()->json( $mg, Response::HTTP_BAD_REQUEST);

            }
            if ($request->amount < 0 || $request->amount <500) {

                $mg = "error transaction";
                return response()->json( $mg, Response::HTTP_BAD_REQUEST);

            }
            $bo = bill::where('refid', $request->refid)->first();
            if (isset($bo)) {
                $mg = "duplicate transaction";
                return response()->json( $mg, Response::HTTP_CONFLICT);

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
                        'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
//                echo $response;

                $data = json_decode($response, true);
                $success = $data["success"];


//                        return $response;
                if ($success == 1) {
                    $tran1 = $data["discountAmount"];
                    $tran2 = $data["token"];
                    $bo =bill::create([
                        'username' => $user->username,
                        'product' => $tv->name,
                        'amount' => $request->amount,
                        'server_response' => $response,
                        'status' => $success,
                        'number' => $request->number,
                        'transactionid' => $request->refid,
                        'discountamount' => $tran1,
                        'token'=>$tran2,
                        'paymentmethod'=>'wallet',
                    ]);
                    $transaction=transaction::create([
                        'username'=>$user->username,
                        'activities'=>'Being Purchase Of '.$bo['product'].' on '.$request->number,
                    ]);


                    $name = $tv->name;
                    $am = $tv->name."was Successful to";
                    $ph = $request->number."| Token:".$tran2;



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
                        'message' => $response,
//                            'data' => $responseData // If you want to include additional data
                    ]);
                }
            }
        }
    }

}
