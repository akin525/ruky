<?php

namespace app\Http\Controllers\Api;
use App\Console\encription;
use App\Mail\Emailcharges;
use App\Mail\Emailfund;
use App\Mail\Emailotp;
use App\Models\bill_payment;
use App\Models\bo;
use App\Models\charge;
use App\Models\setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\User;
use App\Models\wallet;
use App\Models\deposit;
use Illuminate\Support\Facades\Auth;

class FundController

{
    public function fund(Request  $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'refid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $this->error_processor($validator)
            ], 403);
        }
        $apikey = $request->header('apikey');
        $user = User::where('apikey',$apikey)->first();
        if ($user) {
            $bo = deposit::where('payment_ref', "YellowTech".$request->refid)->first();;
            if (isset($bo)) {
                $mg = "duplicate transaction";
                return response()->json([
                    'message' => $mg,
                    'user' => $user,
                    'success' => 0
                ], 200);

            } else {
                $wallet = wallet::where('username', $user->username)->first();
                $pt = $wallet['balance'];
                $char = setting::first();
                $amount1 = $request->amount - 50;


                $gt = $amount1 + $pt;
                $reference = $request->refid;

                $deposit = deposit::create([
                    'username' => $wallet->username,
                    'payment_ref' => $reference,
                    'amount' => $request->amount,
                    'iwallet' => $pt,
                    'fwallet' => $gt,
                ]);

                $charp = charge::create([
                    'username' => $wallet->username,
                    'payment_ref' => "YellowTech" . $reference,
                    'amount' => 50,
                    'iwallet' => $pt,
                    'fwallet' => $gt,
                ]);
                $wallet->balance = $gt;
                $wallet->save();

                $admin="info@renomobilemoney.com";
                $receiver= encription::decryptdata($user->email);
                Mail::to($receiver)->send(new Emailcharges($charp ));
                Mail::to($admin)->send(new Emailcharges($charp ));


                Mail::to($receiver)->send(new Emailfund($deposit ));
                Mail::to($admin)->send(new Emailfund($deposit ));

                return response()->json([
                    'wallet' => $wallet,
                    'user' => $user,
                ], 200);
            }
            return response()->json([
                'message' => 'You are not allowed to access',
            ], 200);
        }

    }

        public function tran(Request $request, $reference)
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer sk_test_280c68e08f76233b476038f04d92896b4749eec3",
                "Cache-Control: no-cache",
            ),
        ));
//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0)

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
//             echo $response;
        }
//        return $response;
        $data=json_decode($response, true);
        $amount=$data["data"]["amount"]/100;
        $auth=$data["data"]["authorization"]["authorization_code"];
// echo $auth;

            $apikey = $request->header('apikey');
            $user = User::where('apikey',$apikey)->first();
            if ($user) {
            $wallet = wallet::where('username', $user->username)->first();
        $pt=$wallet->balance;

            $depo = deposit::where('payment_ref', $reference)->first();
            if (isset($depo)) {
                return response()->json([
                    'message' => 'Duplicate Transaction',
                    'user' => $user,
                ], 200);

            } else {

                $gt = $amount + $pt;
                $deposit = deposit::create([
                    'username' => $user->username,
                    'payment_ref' =>"Api". $reference,
                    'amount' => $amount,
                    'iwallet' => $pt,
                    'fwallet' => $gt,
                ]);
                $wallet->balance = $gt;
                $wallet->save();

              $receiver= $user->email;
                Mail::to($receiver)->send(new Emailfund($deposit ));

                return response()->json([
                    'message' => "You are not allowed to access",
                ], 200);
            }
        }

    }
}
