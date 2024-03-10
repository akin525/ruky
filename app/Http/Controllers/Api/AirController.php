<?php

namespace App\Http\Controllers\Api;

use App\Console\encription;
use App\Mail\Emailtrans;
use App\Models\bill_payment;
use App\Models\bo;
use App\Models\Comission;
use App\Models\data;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AirController
{
    public function airtime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'refid' => 'required',
            'amount' => 'required',
            'number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $this->error_processor($validator)
            ], 403);
        }
        $apikey = $request->header('apikey');

        $user = User::where('apikey', $apikey)->first();
        if ($user) {
            $bt = data::where("cat_id", $request->name)->first();
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

                $per=2/100;
                $comission=$per*$request->amount;



                $gt = $wallet->balance - $request->amount;


                $wallet->balance = $gt;
                $wallet->save();
                $bo = bill_payment::create([
                    'username' => $user->username,
                    'product' => $request->id.'Airtime',
                    'amount' => $request->amount,
                    'server_response' => 0,
                    'status' => 0,
                    'number' => $request->number,
                    'paymentmethod'=>'wallet',
                    'transactionid' =>'api'. $request->refid,
                    'discountamount' => 0,
                    'balance'=>$gt,
                ]);

                $comiS=Comission::create([
                    'username'=>$user->username,
                    'amount'=>$comission,
                ]);

                $bo['name']=encription::decryptdata($user->name);
                $bo['email']=encription::decryptdata($user->email);

                $resellerURL = 'https://integration.mcd.5starcompany.com.ng/api/reseller/';
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL =>$resellerURL.'pay',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('service' => 'airtime', 'coded' => $request->name, 'phone' => $request->number, 'amount' => $request->amount, 'reseller_price' => $request->amount),

                    CURLOPT_HTTPHEADER => array(
                        'Authorization: mcd_key_75rq4][oyfu545eyuriup1q2yue4poxe3jfd'
                    )));

                $response = curl_exec($curl);

                curl_close($curl);
                $data = json_decode($response, true);
                $success = $data["success"];
                if ($success == 1) {

                    $update=bill_payment::where('id', $bo->id)->update([
                        'server_response'=>$response,
                        'status'=>1,
                    ]);
                    $com=$wallet->balance+$comission;
                    $wallet->balance=$com;
                    $wallet->save();

                    $am = "NGN $request->amount  Airtime Purchase Was Successful To";
                    $ph = $request->number;
                    $receiver =encription::decryptdata($user->email);
                    $admin = 'info@renomobilemoney.com';
                    $bo['name']=encription::decryptdata($user->name);

                    Mail::to($receiver)->send(new Emailtrans($bo));
                    Mail::to($admin)->send(new Emailtrans($bo));
                    return response()->json([
                        'message' => $am, 'ph'=>$ph, 'success'=>$success,
                        'user' => $user
                    ], 200);
                } elseif ($success == 0) {
                    $zo = $wallet->balance + $request->amount;
                    $wallet->balance = $zo;
                    $wallet->save();
                    $update=bill_payment::where('id', $bo->id)->update([
                        'server_response'=>$response,
                        'status'=>0,
                    ]);

//                    $name = $bt->plan;
                    $am = "NGN $request->amount Was Refunded To Your Wallet";
                    $ph = ", Transaction fail";

                    return response()->json([
                        'message' => $am,  'ph'=>$ph, 'success'=>$success,
                        'user' => $user
                    ], 200);

                }




                }
        }else {
            return response()->json([
                'message' => "User not found",
            ], 200);

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
