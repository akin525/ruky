<?php

namespace App\Http\Controllers\Api;
use App\Console\encription;
use App\Mail\Emailcharges;
use App\Mail\Emailfund;
use App\Models\bo;
use App\Models\charges;
use App\Models\settings;
use App\Models\transaction;
use App\Models\web;
use App\Models\webook;
use App\Models\deposit;
use App\Models\setting;
use App\Models\wallet;
use App\Notifications\SendPushNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Kutia\Larafirebase\Facades\Larafirebase;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class VertualController  extends Notification
{
    public function vertual(Request $request)
    {
        $apikey = $request->header('apikey');
        $user = User::where('apikey',$apikey)->first();
        if ($user) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mcd.5starcompany.com.ng/api/reseller/virtual-account',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('account_name' => $user->username, 'business_short_name' => 'EVERDATA', 'uniqueid' => $user->name, 'email' => $user->email, 'phone' => '08146328645', 'webhook_url' => 'https://mobile.prinedata.com.ng/run.php',),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: MCDKEY_903sfjfi0ad833mk8537dhc03kbs120r0h9a'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
//            echo $response;
//return $response;
//var_dump(array('account_name' => $name,'business_short_name' => 'RENO','uniqueid' => $username,'email' => $email,'phone' => '08146328645', 'webhook_url'=>'https://renomobilemoney.com/go/run.php'));
            $data = json_decode($response, true);
            $account = $data["data"]["account_name"];
            $number = $data["data"]["account_number"];
            $bank = $data["data"]["bank_name"];

            $user->account_no = $number;
            $user->account_name = $account;
            $user->save();

            return response()->json([
                'message' => 'You are not allowed to access',
            ], 200);


        }
    }


    public function run(Request $request)
    {
//        $web = web::create([
//            'webbook' => $request
//        ]);

        if ($json = json_decode(file_get_contents("php://input"), true)) {
            print_r($json['ref']);
            $data = $json;

        }
//return $data;
        $refid=$data["ref"];
        $amount=$data["amount"];
        $no=$data["account_number"];
        $from=$data["from_account_name"];
        $from1=$data["from_account_number"];

        $wallet = wallet::where('account_number', $no)->first();
        $pt=$wallet['balance'];

        if ($no == $wallet->account_number) {
            $user = user::where('username', $wallet->username)->first();
            $depo = deposit::where('payment_ref', 'Reno'.$refid)->first();
            if (isset($depo)) {
                echo "payment refid the same";
                return $depo;
            } else {

                $char = setting::first();
                $amount1 = $amount - $char->charges;


                $gt = $amount1 + $pt;
                $reference = $refid;

                $deposit = deposit::create([
                    'username' => $wallet->username,
                    'payment_ref' => "Reno" . $reference,
                    'amount' => $amount,
                    'iwallet' => $pt,
                    'fwallet' => $gt,
                ]);
                $charp = charge::create([
                    'username' => $wallet->username,
                    'payment_ref' => "Api" . $reference,
                    'amount' => $char->charges,
                    'iwallet' => $pt,
                    'fwallet' => $gt,
                ]);
                $wallet->balance = $gt;
                $wallet->save();
                $title = encription::decryptdata($user->username)." Account Funded";
                $body = encription::decryptdata($user->username). ' Account Fund with ₦'.$amount.' from'.$from.' '.$from1;


                $admin = 'info@renomobilemoney.com';

                $receiver = encription::decryptdata($user->email);
                $username=encription::decryptdata($user->username);

                $this->firebasenotification($username, $title, $body);
                $this->firebasenotificationadmin($username, $title, $body);
                $this->firebasenotificationadmin1($username, $title, $body);


                Mail::to($receiver)->send(new Emailcharges($charp));
                Mail::to($admin)->send(new Emailcharges($charp));


                Mail::to($receiver)->send(new Emailfund($deposit));
                Mail::to($admin)->send(new Emailfund($deposit));




            }




        }
    }
    public function run1(Request $request)
    {
//        $web = web::create([
//            'webbook' => $request
//        ]);

        if ($json = json_decode(file_get_contents("php://input"), true)) {
            print_r($json['ref']);
            $data = $json;

        }
//return $data;
        $refid=$data["ref"];
        $amount=$data["amount"];
        $no=$data["account_number"];
        $from=$data["from_account_name"];
        $from1=$data["from_account_number"];
        $narration=$data['narration'];

        $wallet = wallet::where('account_number', $no)->first();
        $pt=$wallet['balance'];

        if ($no == $wallet->account_number) {
            $user = user::where('username', $wallet->username)->first();
            $depo = deposit::where('refid', $refid)->first();
            if (isset($depo)) {
                echo "payment refid the same";
                return $depo;
            } else {

                $char = settings::first();
                $amount1 = $amount - $char->charges;


                $gt = $amount1 + $pt;
                $reference = $refid;

                $deposit = deposit::create([
                    'username' => $wallet->username,
                    'refid' =>  $reference,
                    'amount' => $amount,
                    'narration'=>$narration,
                    'iwallet' => $pt,
                    'fwallet' => $gt,
                ]);
                $charp = charges::create([
                    'username' => $wallet->username,
                    'refid' => $reference,
                    'amount' => $char->charges,
                    'iwallet' => $pt,
                    'fwallet' => $gt,
                ]);
                $wallet->balance = $gt;
                $wallet->save();

                $transaction=transaction::create([
                    'username'=>$deposit['username'],
                    'activities'=>$narration,
                ]);
                $title = $user->username." Account Funded";
                $body = $user->username. ' Account Fund with ₦'.$amount.' from'.$from.' '.$from1;


                $admin = 'info@efemobilemoney.com';

                $receiver = $user->email;
                $username=$user->username;
                $deposit['charges']=$char->charges;

                Mail::to($receiver)->send(new Emailfund($deposit));
                Mail::to($admin)->send(new Emailfund($deposit));




            }




        }
    }
    public  function firebasenotification($username, $title, $body)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
    "to": "/topics/'.$username.'",
    "notification": {
        "body": "'.$body.'",
        "title": "'.$title.'"

    }
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer AAAA0VPmumc:APA91bFO0BZ1BL5bGsBIFW2JGE3SZzC60y-Hrqg2UgVlgeYfj7_kIawa7W1Vz0LMTVhhyC1uy4dsSGAU2oe87HzR27PInPhLlDlWKOS5buvaejdQl2O2lWe9Ewts09GiRcmJEi3LnkzB',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        dd($response);
//        echo $response;
    }
    public  function firebasenotificationadmin($username, $title, $body)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
    "to": "/topics/Adeolu23",
    "notification": {
        "body": "'.$body.'",
        "title": "'.$title.'"

    }
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer AAAA0VPmumc:APA91bFO0BZ1BL5bGsBIFW2JGE3SZzC60y-Hrqg2UgVlgeYfj7_kIawa7W1Vz0LMTVhhyC1uy4dsSGAU2oe87HzR27PInPhLlDlWKOS5buvaejdQl2O2lWe9Ewts09GiRcmJEi3LnkzB',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        dd($response);
//        echo $response;
    }
    public  function firebasenotificationadmin1($username, $title, $body)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
    "to": "/topics/Izormor2019",
    "notification": {
        "body": "'.$body.'",
        "title": "'.$title.'"
    }
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer AAAA0VPmumc:APA91bFO0BZ1BL5bGsBIFW2JGE3SZzC60y-Hrqg2UgVlgeYfj7_kIawa7W1Vz0LMTVhhyC1uy4dsSGAU2oe87HzR27PInPhLlDlWKOS5buvaejdQl2O2lWe9Ewts09GiRcmJEi3LnkzB',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        dd($response);
//        echo $response;
    }

    public function honor(Request $request)
    {
//        dd($request->all());
//        $webook=webook::create([
//            'code'=>$request,
//            'message'=>$request,
//        ]);

       $json = json_decode(file_get_contents("php://input"), true) ;
//            print_r($json['ref']);
//    print_r($json['accountDetails']['accountName']);
//        return $request;
//        $data = json_decode($request, true);

        $data = $json;
//        return $data;



        $code=$data['code'];
        $message=$data['message'];

        $webook=webook::create([
            'code'=>$code,
            'message'=>$message,
        ]);
    }
    function eassy(Request $request)
    {
        $json = json_decode(file_get_contents("php://input"), true) ;

        $data = $json;
        $message=$data["message"];
        $refid=$data["reference"];
        $web = web::create([
            'webbook' => $message. " ID:".$refid
        ]);

    }
}
