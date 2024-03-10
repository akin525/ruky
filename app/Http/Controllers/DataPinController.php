<?php

namespace App\Http\Controllers;

use App\Console\encription;
use App\Mail\Emailtrans;
use App\Models\bill;
use App\Models\bill_payment;
use App\Models\data;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class DataPinController extends Controller
{

    function dataindex()
    {
        $product = data::where('network', 'data_pin')->first();

        return view('bills.datapin', compact('product'));
    }

    function processdatapin(Request $request)
    {
        $request->validate([

            'productid'=>'required'
        ]);
        $user = User::find($request->user()->id);
        $wallet = wallet::where('username', $user->username)->first();
        $product = data::where('network', $request->productid)->first();
        if ($user->apikey == '') {
            $amount = $product->tamount;
        } elseif ($user != '') {
            $amount = $product->ramount;
        }
//        if (Auth::user()->bvn==NULL){
//            Alert::warning('Update', 'Please Kindly Update your profile including your bvn for account two & to continue');
//            return redirect()->intended('myaccount')
//                ->with('info', 'Please Kindly Update your profile including your bvn for account two');
//        }
        if ($wallet->balance < $amount) {
            $mg = "You Cant Make Purchase Above" . "NGN" . $amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";

            return response()->json( $mg, Response::HTTP_BAD_REQUEST);


        }
        if ($amount < 0) {

            $mg = "error transaction";
            return response()->json( $mg, Response::HTTP_BAD_REQUEST);


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


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mcd.5starcompany.com.ng/api/reseller/pay',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('service' => 'data_pin', 'coded' => $product->code, 'quantity' => '1'),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
                ),
            ));


            $response = curl_exec($curl);

            curl_close($curl);
//            echo $response;

            $data = json_decode($response, true);
            $success = $data["success"];

            if ($success=='1'){

                $bo =bill::create([
                    'username' => $user->username,
                    'product' => $product->name,
                    'amount' => $product->amount,
                    'server_response' => $response,
                    'status' => $success,
                    'number' => 'Any Number',
                    'transactionid' => $request->refid,
                    'discountamount' => $data["discountAmount"],
                    'token'=>$data['token'],
                    'paymentmethod'=>'wallet',
                    'fbalance'=>$fbalance,
                    'balance'=>$gt,
                ]);

                $name = $product->plan;
                $am = $product->network."was Successful";
                $ph = "| Token:".$data['token'];

                return response()->json([
                    'status' => 'success',
                    'message' => $am.' ' .$ph,
                ]);

            }
        }


    }

}
