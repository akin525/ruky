<?php

namespace App\Http\Controllers\admin;

use App\Console\encription;
use App\Models\bill;
use App\Models\bill_payment;
use App\Models\bo;
use App\Models\charge;
use App\Models\charges;
use App\Models\charp;
use App\Models\deposit;
use App\Models\Messages;
use App\Models\refer;
use App\Models\server;
use App\Models\transaction;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class UsersController
{
    public function index(Request $request)
    {
$u=User::get();
        $users=User::with('parentData')->orderBy('id', 'desc')->paginate('100');
        $wallet = DB::table('wallets')->orderBy('id', 'desc')->get();
$reseller=DB::table('users')->where("apikey", "!=", "")->count();
        $t_users = DB::table('users')->count();
        $f_users = DB::table('users')->where("role","=","")->count();


        $a_users = DB::table('users')->where("role","=","users")->count();


        $title = 'Delete User!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        return view('admin.users', ['users' => $users, 'res'=>$reseller, 't_users'=>$t_users, 'wallet'=>$wallet, 'f_users'=>$f_users, 'a_users'=>$a_users]);

    }
    public function fin()
    {
        $user=User::get();
        return view('admin/finds', compact('user'));

    }
    public function finduser(Request $request){
        $input = $request->all();
        $user_name=$input['user_name'];
        $phoneno=$input['phoneno'];
        $status=$input['status'];
        $wallet=$input['wallet'];
        $email=$input['email'];
        $regdate=$input['regdate'];
        $query = User::Where('username', 'LIKE', "%$user_name%")->with('parentData')->limit(500)
            ->get();

        $cquery = User::Where('username', 'LIKE', "%$user_name%")->count();

        return view('admin/finds', ['users' => $query, 'count'=>$cquery, 'result'=>true]);
    }
    public function profile($username)
    {
        $ap = User::where('username', $username)->with('parentData')->first();

        if(!$ap){
            Alert::warning('Admin', 'user does not exist');
            return redirect('admin/finds');
        }
$wallet=wallet::where('username', $username)->first();
        $user =User::where('username', $username)->first();
        $sumtt = deposit::where('username', $ap->username)->sum('amount');
        $tt = deposit::where('username', $ap->username)->count();
        $td = deposit::where('username', $ap->username)->orderBy('id', 'desc')->paginate(10);
        $v = DB::table('bill_payments')->where('username', $ap->username)->orderBy('id', 'desc')->paginate(25);
        $tat = bill::where('username', $ap->username)->count();
        $sumbo = bill::where('username', $ap->username)->sum('amount');
        $sumch = charges::where('username', $ap->username)->sum('amount');
        $charge = charges::where('username', $ap->username)->paginate(10);
        $no=transaction::where('username', $ap->username)
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();
//return $user;
        return view('admin/profile', ['no'=>$no, 'user' => $ap, 'sumtt'=>$sumtt, 'charge'=>$charge,  'sumch'=>$sumch, 'sumbo'=>$sumbo, 'tt' => $tt, 'wallet'=>$wallet, 'td' => $td,  'version' => $v,  'tat' =>$tat]);
    }
    public function server()
    {
        $server=server::get();

        return view('admin.server.data', compact('server'));
    }

    public function up(Request $request)
    {
        $server=server::where('id', $request->id)->first();
        if ($server->status==1)
        {
            $u="0";
        }else{
            $u="1";
        }

        $server->status=$u;
        $server->save();

        return back();
//        return response()->json(['status'=>'success', 'message'=>'server update successful']);

    }
    public function mes()
    {
        $message=Messages::where('status', 1)->first();

        return view('admin/noti', compact('message'));
    }

    public function me(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'message' => 'required',
        ]);
        $message=Messages::where('id', $request->id)->first();

        $message->message=$request->message;
        $message->save();
        $username="admin22";
        $body=$request->message;
        $this->notificationpush($username, "Renomobilemoney Notification!!", $body);

        Alert::success('Admin', 'Notification Change Successful');
        return redirect(url('admin/noti'));
    }
    public  function notificationpush($username, $title, $body)
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
