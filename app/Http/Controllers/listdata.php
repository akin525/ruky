<?php

namespace App\Http\Controllers;
use App\Models\big;
use App\Models\data;
use Illuminate\Http\Request;


class listdata
{
public function list(Request $request)
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
        CURLOPT_POSTFIELDS => array('service' => 'data','coded' => '9'),
        CURLOPT_HTTPHEADER => array(
            'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
//    echo $response;

    return $response;

    $data = json_decode($response, true);

//return $success;
foreach ($data['data'] as $plan){
    $success =$plan["type"];
    $planid = $plan["code"];
    $price= $plan['amount'];
    $allowance=$plan['name'];
    $validity ='1';
    $insert= data::create([
        'plan_id' =>$planid,
        'network' =>$success,
        'name' =>$allowance,
        'amount'=>$price,
        'server'=>$validity,
        'tamount'=>$price,
        'ramount'=>$price,
    ]);
}


}
public function lis()
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.honourworld.com.ng/api/v1/set/webhook/url',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
    "url" : "https://mobile.primedata.com.ng/api/honor"
}',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer {{Token}}',
            'Content-Type: application/json',
            'Accept: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;

}
}
