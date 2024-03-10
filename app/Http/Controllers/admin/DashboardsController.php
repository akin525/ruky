<?php

namespace App\Http\Controllers\admin;

use App\Models\admin;
use App\Models\bill;
use App\Models\bill_payment;
use App\Models\bo;
use App\Models\charge;
use App\Models\charges;
use App\Models\charp;
use App\Models\deposit;
use App\Models\Messages;
use App\Models\profit;
use App\Models\profit1;
use App\Models\refer;
use App\Models\safe_lock;
use App\Models\User;
use App\Models\wallet;
use App\Models\webook;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class DashboardsController
{
public function dashboard(Request $request)
{
    if (Auth()->user()->role=="admin") {
        $user = User::where('username', Auth::user()->username)->where('role', 'admin')->first();
        $alluser = User::count();
        $profit = profit::sum('amount');
//        $profit1 = profit1::sum('amount');

        $wallet = wallet::sum('balance');

        $deposite = deposit::sum('amount');

    $charge=charges::sum('amount');
        $bill = bill::sum('amount');

        $resellerURL = 'https://integration.mcd.5starcompany.com.ng/api/reseller/';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $resellerURL . 'me',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('service' => 'balance'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//                                                        return $response;
        $data = json_decode($response, true);
        $success = $data["success"];
        $tran = $data["data"]["wallet"];
        $pa = $data["data"]["commission"];

        $today = Carbon::now()->format('Y-m-d');


        $data['bill'] = bill::where([['status', '=', '1'], ['timestamp', 'LIKE', $today . '%']])->count();
        $data['deposit'] = deposit::where([['status', '=', '1'], ['created_at', 'LIKE', $today . '%']])->count();
        $data['user'] = User::where([['created_at', 'LIKE', $today . '%']])->count();
        $data['nou'] = wallet::where([['updated_at', 'LIKE', $today . '%']])->count();
        $data['sum_deposits'] = deposit::where([['created_at', 'LIKE', '%' . $today . '%']])->sum('amount');
        $data['sum_bill'] = bill::where([['timestamp', 'LIKE', '%' . $today . '%']])->sum('amount');
        return view('admin/dashboard', compact('user', 'bill', 'profit', 'wallet','data', 'charge',  'tran', 'alluser', 'deposite'));

    }
    return redirect("admin/login")->with('status', 'You are not allowed to access');

}
public function mcdtran()
{
    if (Auth()->user()->role == "admin") {

        $resellerURL = 'https://integration.mcd.5starcompany.com.ng/api/reseller/';


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $resellerURL . 'me',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('service' => 'transactions'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: mcd_key_75rq4][oyfu545eyuriup1q2yue4poxe3jfd'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//echo $response;
        $data = json_decode($response, true);
        $success = $data["data"];
        return view('admin/mcdtransaction', compact('success' ));

    }
    return redirect("admin/login")->with('status', 'You are not allowed to access');
}
public function webbook()
{
    $book=webook::orderBy('id', 'desc')->paginate(30);
    return view("admin/webbook", compact("book"));
}
public function ref()
{

    $count = refer::where('username', '!=', '')->count();
$refer=refer::where('username', '!=', '')->get();


    return view('admin/refer', compact('count', 'refer' ));


}
}
