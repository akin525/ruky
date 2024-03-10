<?php

namespace App\Http\Controllers;

use App\Console\encription;
use App\Models\bill;
use App\Models\bill_payment;
use App\Models\data;
use App\Models\neco;
use App\Models\necos;
use App\Models\server;
use App\Models\transaction;
use App\Models\User;
use App\Models\waec;
use App\Models\wallet;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class EducationController
{
    public function listedu()
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
            CURLOPT_POSTFIELDS => array('service' => 'result_checker'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;


    }
public function indexw()
{
    $waec=data::where('network', 'WAEC')->first();
    $wa=waec::where('username', Auth::user()->username)->get();
return view('education.waec', compact('waec', 'wa'));

}
public function indexn()
{
    $neco=data::where('network', 'NECO')->first();
    $ne=necos::where('username', Auth::user()->username)->get();

    return view('education.neco', compact('neco', 'ne'));

}
public function waec(Request $request)
{
$request->validate([
    'value'=>'required',
    'amount'=>'required',
]);
    $user = User::find($request->user()->id);
    $wallet = wallet::where('username', $user->username)->first();
    $serve = server::where('status', '1')->first();
    $product=data::where('network', 'WAEC')->first();

    if ($user->apikey == '') {
        $amount = $product->tamount *$request->value;
    } elseif ($user != '') {
        $amount = $product->ramount *$request->value;
    }

    if ($wallet->balance < $amount) {
        $mg = "You Cant Make Purchase Above" . "NGN" . $amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";

       return response()->json($mg, Response::HTTP_BAD_REQUEST);

    }
    if ($request->amount < 0) {

        $mg = "error transaction";
        return response()->json($mg, Response::HTTP_BAD_REQUEST);


    }
    $bo = bill::where('transactionid', $request->id)->first();
    if (isset($bo)) {
        $mg = "duplicate transaction";
        return response()->json($mg, Response::HTTP_CONFLICT);

    } else {

        $user = User::find($request->user()->id);
//                $bt = data::where("id", $request->productid)->first();
        $wallet = wallet::where('username', $user->username)->first();


        $gt = $wallet->balance - $request->amount;


        $wallet->balance = $gt;
        $wallet->save();
        $bo = bill::create([
            'username' => $user->username,
            'product' => $product->network ,
            'amount' => $request->amount,
            'server_response' => 'ur fault',
            'status' => 1,
            'number' => $request->number,
            'transactionid' => $request->id,
            'discountamount'=>0,
            'paymentmethod'=> 'wallet',
            'balance'=>$gt,
        ]);
        $transaction=transaction::create([
            'username'=>$user->username,
            'activities'=>'Being Purchase Of Waec Result Checker',
        ]);

        $bo['name']=$user->name;
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
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('service' => 'result_checker', 'coded' => 'WAEC', 'quantity' => '1', 'phone' => $user->phone,  'reseller_price' => $product->tamount),
            CURLOPT_HTTPHEADER => array(
                'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
            )));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response, true);
        if ($data["success"]=="t1") {
            $ref=$data['ref'];
            $token=$data['token'];
                $insert=waec::create([
                    'username'=>$user->username,
                    'seria'=>"serial no from pin",
                    'pin'=>$token,
                    'ref'=>$ref,
                ]);

            $mg='Waec Checker Successful Generated, kindly check your pin';
            return response()->json([
                'status' => 'success',
                'message' => $mg,
//                            'data' => $responseData // If you want to include additional data
            ]);

        }elseif($data["success"]=="0"){
            $zo = $wallet->balance + $amount;
            $wallet->balance = $zo;
            $wallet->save();
            return response()->json([
                'status' => 'fail',
                'message' => $response,
//                            'data' => $responseData // If you want to include additional data
            ]);
        }
//return $response;
    }

}
public function neco(Request $request)
{
    $request->validate([
        'value'=>'required',
        'amount'=>'required',
    ]);
    $user = User::find($request->user()->id);
    $wallet = wallet::where('username', $user->username)->first();
    $serve = server::where('status', '1')->first();
    $product=data::where('network', 'neco')->first();

    if ($user->apikey == '') {
        $amount = $product->tamount *$request->value;
    } elseif ($user != '') {
        $amount = $product->ramount *$request->value;
    }

    if ($wallet->balance < $amount) {
        $mg = "You Cant Make Purchase Above" . "NGN" . $amount . " from your wallet. Your wallet balance is NGN $wallet->balance. Please Fund Wallet And Retry or Pay Online Using Our Alternative Payment Methods.";

       return response()->json($mg, Response::HTTP_BAD_REQUEST);

    }
    if ($request->amount < 0) {

        $mg = "error transaction";
        return response()->json($mg, Response::HTTP_BAD_REQUEST);

    }
    $bo = bill::where('transactionid', $request->refid)->first();
    if (isset($bo)) {
        $mg = "duplicate transaction";
        return response()->json($mg, Response::HTTP_CONFLICT);


    } else {

        $user = User::find($request->user()->id);
//                $bt = data::where("id", $request->productid)->first();
        $wallet = wallet::where('username', $user->username)->first();


        $gt = $wallet->balance - $request->amount;


        $wallet->balance = $gt;
        $wallet->save();
        $bo = bill::create([
            'username' => $user->username,
            'product' => $product->network ,
            'amount' => $request->amount,
            'server_response' => 'ur fault',
            'status' => 1,
            'number' => $request->number,
            'transactionid' => $request->id,
            'discountamount'=>0,
            'paymentmethod'=> 'wallet',
            'balance'=>$gt,
        ]);
        $transaction=transaction::create([
            'username'=>$user->username,
            'activities'=>'Being Purchase Of Neco Result Checker',
        ]);

        $bo['name']=$user->name;
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
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('service' => 'result_checker', 'coded' => 'NECO', 'quantity' => '1', 'phone' => $user->phone,  'reseller_price' => $product->tamount),
            CURLOPT_HTTPHEADER => array(
                'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
            )));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response, true);

        if ($data["success"]=="1") {
            $ref=$data['ref'];
            $token=$data['token'];

                $insert=necos::create([
                    'username'=>$user->username,
                    'pin'=>$token,
                    'ref'=>$ref,
                ]);

            $mg='Waec Checker Successful Generated, kindly check your pin';
            return response()->json([
                'status' => 'success',
                'message' => $mg,
//                            'data' => $responseData // If you want to include additional data
            ]);

        }elseif($data["success"]=="0"){
            $zo = $wallet->balance + $amount;
            $wallet->balance = $zo;
            $wallet->save();

            return response()->json([
                'status' => 'fail',
                'message' => $response,
//                            'data' => $responseData // If you want to include additional data
            ]);
        }
    }
}
public function allneco()
{
    $neco=necos::all();
    return view('admin/neco', compact('neco'));
}
}

