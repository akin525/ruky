<?php

namespace App\Http\Controllers\admin;

use App\Console\encription;
use App\Http\Controllers\Controller;
use App\Models\transaction;
use App\Models\User;
use App\Models\wallet;
use RealRashid\SweetAlert\Facades\Alert;

class RegenerateVirtualAccountController extends Controller
{
function regenrateaccount1($request)
{
    $user=User::where('id', $request)->first();
    $wallet=wallet::where('username', $user->username)->first();

    $username=$user['username'].rand(111, 999);
    $email=$user['email'];
    $name=$user['name'];
    $phone=$user['phone'];


    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://integration.mcd.5starcompany.com.ng/api/reseller/virtual-account3',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('account_name' => $name,
            'business_short_name' => 'EFE','uniqueid' => $username,
            'email' => $email,'dob' => $user['dob'],
            'address' => $user['address'],'gender' => $user['gender'], 'provider'=>'providus',
            'phone' =>$phone,'webhook_url' => 'https://app.efemobilemoney.com/api/run1'),
        CURLOPT_HTTPHEADER => array(
            'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $data = json_decode($response, true);
    if ($data['success']==1){
        $account = $data["data"]["customer_name"];
        $number = $data["data"]["account_number"];
        $bank = $data["data"]["bank_name"];

        $wallet->account_number=$number;
        $wallet->account_name=$account;
        $wallet->bank=$bank;
        $wallet->save();

        $transaction=transaction::create([
            'username'=>$user['username'],
            'activities'=>'Virtual Account Generated Successfully',
        ]);

        Alert::success('Success', 'Account Details Generated Successful');
        return back();
    }elseif ($data['success']==0){
        Alert::error('Oops', $response);
        return back();
    }
}
function regenrateaccount($request)
{
    $user=User::where('id', $request)->first();
    $wallet=wallet::where('username', $user->username)->first();

    $username=$user['username'].rand(111, 999);
    $email=$user['email'];
    $name=$user['name'];
    $phone=$user['phone'];


    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://integration.mcd.5starcompany.com.ng/api/reseller/virtual-account3',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('account_name' => $name,
            'business_short_name' => 'EFE','uniqueid' => $username,
            'email' => $email,'dob' => $user['dob'],
            'address' => $user['address'],'gender' => $user['gender'], 'provider'=>'safeheaven',
            'phone' =>$phone,'webhook_url' => 'https://app.efemobilemoney.com/api/run1'),
        CURLOPT_HTTPHEADER => array(
            'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $data = json_decode($response, true);
    if ($data['success']==1){
        $account = $data["data"]["customer_name"];
        $number = $data["data"]["account_number"];
        $bank = $data["data"]["bank_name"];

        $wallet->account_number=$number;
        $wallet->account_name=$account;
        $wallet->bank=$bank;
        $wallet->save();

        $transaction=transaction::create([
            'username'=>$user['username'],
            'activities'=>'Virtual Account Generated Successfully',
        ]);

        Alert::success('Success', 'Account Details Generated Successful');
        return back();
    }elseif ($data['success']==0){
        Alert::error('Oops', $response);
        return back();
    }
}
}
