<?php

namespace app\Http\Controllers\admin;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class McdController
{
    public function index()
    {


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
//    return $response;
        }
        $data = json_decode($response, true);
        $success = $data["status"];
        return view('admin/mcd', compact('data'));
    }
public function verify(Request $request)
{
    $number=$request['number'];
    $code=$request['bank'];

    $curl = curl_init();


    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/bank/resolve?account_number=$number&bank_code=$code",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer sk_live_15001f29b396a714841ab44f5547801beb3855c2",
            "Cache-Control: no-cache",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
//    return $response;
    }

    $data = json_decode($response, true);
$tran=$data['data'];
    return view("admin/verify", compact("tran", "request"));


}
    public function mcd(Request $request)
    {
        $request->validate([
            'bank' => 'required',
            'amount' => 'required',
        ]);

        $amount=$request->amount;
        $number=$request->number;
        $bank=$request->bank;
        $name=$request->code;
        $resellerURL='https://integration.mcd.5starcompany.com.ng/api/reseller/';


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $resellerURL.'me',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('service' => 'withdraw_commission','amount' => $amount,'account_number' => $number,'bank_code' => $name,'bank' => $bank,'wallet' => 'wallet'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: mcd_key_75rq4][oyfu545eyuriup1q2yue4poxe3jfd'

            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        //echo $amount;
        $data = json_decode($response, true);
        $success = $data["success"];
        $tran = $data["message"];

        Alert::success('MCD Admin', 'Your request has been proceed');
        return redirect('admin/mcd');
    }
}
