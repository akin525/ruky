<?php

namespace App\Http\Controllers;

use App\Console\encription;
use App\Mail\withdraws;
use App\Models\User;
use App\Models\wallet;
use App\Models\withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class WithdrawController
{
public function bank()
{
    if (Auth::user()->bvn==NULL){
        Alert::warning('Update', 'Please Kindly Update your profile including your bvn to enable withdraw');
        return redirect()->intended('myaccount')
            ->with('info', 'Please Kindly Update your profile including your bvn for account two');
    }
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/bank",
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

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
//return $response;
    }
    $data = json_decode($response, true);
    $success = $data["status"];
    $with=withdraw::where('username', Auth::user()->username)->get();

    return view("withdraw", compact("data", "with"));
}
public function verify(Request $request)
{
    $request->validate([
        'bank'=>'required',
        'number'=>'required',
    ]);

    $input=$request->all();

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://sandbox.monnify.com/api/v1/disbursements/account/validate?accountNumber=$request->number&bankCode=$request->bank",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization:  Basic TUtfVEVTVF9LUFoyQjJUQ1hLOkJERkJZUUtRSEVHR1NCOFJFODI3VlRGODhYVEJQVDJN",
            "Cache-Control: no-cache",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
//return $response;
    }

    $data = json_decode($response, true);
//$success = $data["status"];
    $tran = $data["responseBody"]["accountName"];
return view("witve", compact("request", "tran"));
}
public function sub(Request $request)
{
    $request->validate([
        'number'=>'required',
        'name'=>'required',
        'amount'=>'required',
    ]);
    $wallet=wallet::where('username', Auth::user()->username)->first();
    $user = User::where('username', Auth::user()->username)->first();

    if ($wallet->balance < $request->amount) {
        $msg ="Insufficient Balance ";
        Alert::error('error', $msg);
        return redirect('withdraw');
    }
    if ($request->amount < 2000) {
        $msg ="Your amount must not be less down ₦2000";
        Alert::error('error', $msg);
        return redirect('withdraw');
    }

    if ($request->amount < 0) {
        $msg ="Please Enter a valid amount";
        Alert::error('error', $msg);
        return redirect('withdraw');
    }


    $total=$wallet->balance - $request->amount;
    $wallet->balance=$total;
    $wallet->save();
    $insert=withdraw::create([
       'username'=>Auth::user()->username,
       'amount'=>$request['amount'],
        'bank'=>$request['bank'],
        'account_no'=>$request['number'],
        'name'=>$request['name'],
    ]);
    $receiver = encription::decryptdata($user->email);
    $admin = 'info@renomobilemoney.com';


    Mail::to($receiver)->send(new withdraws($insert));
    Mail::to($admin)->send(new withdraws($insert));
//                        Mail::to($admin2)->send(new Emailtrans($bo));
    $username=encription::decryptdata(Auth::user()->username);
    $body=$username.' place a withdraw request of NGN'.$request->amount;
    $this->reproduct($username, "Withdraw Request", $body);
    $this->reproduct1($username, "Withdraw Request", $body);
    $this->reproduct2($username, "Withdraw Request", $body);


    $mg="Your request has been received u will receive alert soon";
    Alert::success('Succcess', $mg);
    return redirect('withdraw');
}
    public  function reproduct($username, $title, $body)
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
    public  function reproduct1($username, $title, $body)
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
    public  function reproduct2($username, $title, $body)
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

}
