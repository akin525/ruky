<?php

namespace App\Http\Controllers;

use App\Console\encription;
use App\Models\Claim;
use App\Models\data;
use App\Models\Giveaway;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class GiveawaController extends Controller
{
    function claimgiveaway()
    {
        $give=Giveaway::paginate('9');
        $givew=Giveaway::where('status', 1)->get();
//        $giveaway=Giveaway::where('status', 1)->count();
//        if ($giveaway>0){
//            Alert::image('Giveaway Time!!','Check Our Giveaway Page','https://renomobilemoney.com/give.jpg','200','200', 'Image Alt');

//        }
        return view('claimgiveaway', compact('give', 'givew'));
    }
    function claimnow($request)
    {
        $give=Giveaway::where('id', $request)->first();
        if ($give->claim==$give->limits)
        {
            $mg="Giveaway already claim finished comeback later";
            Alert::warning('😋',$mg );
            return back();
        }
       $nw= $give->click+1;
       $re=$give->limits-1;
       $give->click=$nw;;
       $give->save();
       if ($give->status='0'){
           $mg="Giveaway not available try others";
           Alert::error('Error', $mg);
           return redirect('claim');
       }
        return view('claimnow', compact('give'));
    }
    function claimgive(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'number'=>'required',
            'amount'=>'required',
            'refid'=>'required',
        ]);
        $give=Giveaway::where('id', $request->id)->first();

        $claim=Claim::where('username', Auth::user()->username)
            ->where('giveaway_id', $give->id)->first();
        if ($claim){
            $mg="You have already claim this giveaway";
            Alert::warning('😋',$mg );
            return back();
        }
        if ($give->claim==$give->limits)
        {
            $mg="Giveaway already claim finished comeback later";
            Alert::warning('😋',$mg );
            return back();
        }
        if ($give->remain==0){
            $mg="Giveaway already claim finished comeback later";
            Alert::warning('😋',$mg );
            return back();
        }
        $create=Claim::create([
            'username'=>Auth::user()->username,
            'product'=>$give->product,
            'amount'=>$request->amount,
            'number'=>$request->number,
            'giveaway_id'=>$give->id,
            'refid'=>$request->refid,
        ]);
        if ($give->type=="airtime"){
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
                CURLOPT_POSTFIELDS => array('service' => 'airtime', 'coded' => $give->product_id, 'phone' => $request->number, 'amount' => $request->amount, 'reseller_price' => $request->amount),

                CURLOPT_HTTPHEADER => array(
                    'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
                )));

            $response = curl_exec($curl);

            curl_close($curl);
            $data = json_decode($response, true);
            $success = $data["success"];
            $tran1 = $data["discountAmount"];
            if ($success == 1){

                $re=$give->limits-1;
                $cla=$give->claim+1;
                $give->remain=$re;
                $give->claim=$cla;
                $give->save();
                if ($give->limits==$give->claim){
                    $st=0;
                    $give->status=$st;
                    $give->save();
                }
                $am = "NGN $request->amount giveaway Airtime Purchase Was Successful To";
                $ph = $request->number;
                Alert::success('success', $am.' ' .$ph);
                return redirect('claim');
            }
            return $give;

        }elseif ($give->type=="data"){
            $data=data::where('id', $give->product_id)->first();
//            return $data;
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
                CURLOPT_POSTFIELDS => array('service' => 'data','coded' => $data->cat_id,'phone' => $request->number,  'reseller_price' => $data->tamount),

                CURLOPT_HTTPHEADER => array(
                    'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
                )));


            $response = curl_exec($curl);

            curl_close($curl);
                echo $response;
            $data1 = json_decode($response, true);
            $success = $data1["success"];
            if (isset($data1['success'])) {
                $dis = $data1['discountAmount'];
//                    echo $success;
                $success = "1";
                $re=$give->limits-1;
                $cla=$give->claim+1;
                $give->remain=$re;
                $give->claim=$cla;
                $give->save();
                if ($give->limits==$give->claim){
                    $st=0;
                    $give->status=$st;
                    $give->save();
                }

                $name = $data->plan;
                $am = "$data-> giveaway  was successful delivered to";
                $ph = $request->number;

                Alert::success('success', $am.' ' .$ph);
                return redirect(route('claim'))
                    ->with('success', $am.' ' .$ph);
            }
        }
    }
function bonus()
{

    $give=Giveaway::where('username', Auth::user()->username)->get();
    return view('bonus', compact( 'give'));
}
function giveaway()
{
//    Alert::toast( 'Coming soon Keep checking 👊 ', 'info');
//    return redirect('dashboard');
    $give=Giveaway::where('username', Auth::user()->username)->get();
    $product=data::where('network', 'mtn-data')
    ->orwhere('network', 'glo-data')
    ->orwhere('network', 'airtel-data')->get();
    return view('giveaway', compact( 'product'));
}
function giveawayair()
{

    $give=Giveaway::where('username', Auth::user()->username)->get();

    return view('airtimegive', compact( 'give'));
}
function creategiveawaydata(Request $request)
{
    $request->validate([
        'name'=>'required',
        'product'=>'required',
        'amount'=>'required',
        'limit'=>'required',
    ]);
    $product=data::where('id', $request->product)->first();
    $amount=$product->ramount*$request->limit;
    $wallet=wallet::where('username', Auth::user()->username)->first();
    if ($wallet->balance<$amount){
        $mg="You must have above ₦".$amount;
        Alert::error('Insufficient Balance', $mg);
        return back();
    }
    if ($amount<100){
        $mg="Check your transaction";
        Alert::error('Error', $mg);
        return back();
    }
    $balance=$wallet->balance-$amount;

    $wallet->balance=$balance;
    $wallet->save();

    $create=Giveaway::create([
        'username'=>Auth::user()->username,
        'product'=>$product->plan,
        'product_id'=>$product->id,
        'type'=>'data',
        'amount'=>$request->amount,
        'limits'=>$request->limit,
        'remain'=>$request->limit,
    ]);
    $body=encription::decryptdata($create['username'])." Create ".$create['product']." Giveaway";
    $username=$create['username'];
    $this->giveawaypush($username, "Data Giveaway!!", $body);
    Alert::success('WOW', 'Giveaway Successfully Created');
    return back();

}
function creategiveawayairtime(Request $request)
{
    $request->validate([
        'name'=>'required',
        'product'=>'required',
        'amount'=>'required',
        'body'=>'required',
        'limit'=>'required',
    ]);
    $amount=$request->amount*$request->limit;
    $wallet=wallet::where('username', Auth::user()->username)->first();
    if ($wallet->balance<$amount){
        $mg="You must have above ₦".$amount;
        Alert::error('Insufficient Balance', $mg);
        return back();
    }
    if ($amount<100){
        $mg="Check your transaction";
        Alert::error('Error', $mg);
        return back();
    }
    $balance=$wallet->balance-$amount;

    $wallet->balance=$balance;
    $wallet->save();

    $create=Giveaway::create([
        'username'=>Auth::user()->username,
        'product'=>$request->body." Airtime",
        'product_id'=>$request->product,
        'type'=>'airtime',
        'amount'=>$request->amount,
        'limits'=>$request->limit,
        'remain'=>$request->limit,
    ]);
    $user=User::all();
    $body=encription::decryptdata($create['username'])." Create ".$create['product']." Giveaway";
    $username=$create['username'];
    $this->giveawaypush($username, "Airtime Giveaway!!", $body);
    Alert::success('WOW', 'Giveaway Successfully Created');
    return back();

}
    public  function giveawaypush($username, $title, $body)
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
    "to": "/topics/giveaway",
    "notification": {
        "body": "'.$body.'",
        "title": "'.$title.'"
        "image": "https://renomobilemoney.com/images/bn.jpeg"
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
