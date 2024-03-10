<?php

namespace App\Http\Controllers\admin;

use App\Console\encription;
use App\Mail\Emailclon;
use App\Mail\Emailtrans;
use App\Models\interest;
use App\Models\safe_lock;
use App\Models\User;
use App\Models\wallet;
use App\Models\wi;
use Illuminate\Support\Facades\Mail;

class LockController
{
public function index()
{
    $lock=safe_lock::latest()->paginate(9);
    return view("admin/allock", compact("lock"));
}
public function interest()
{
    $interest=10;
$user=safe_lock::where('status', '1')->get();
foreach ($user as $row) {
    $calA = $interest / 100 * $row["balance"];

    $calPday = round(($calA / 365), 2);
    $username = $row["username"];

    $insect=interest::create([
        'username'=>$username,
        'profit'=>$calPday,
    ]);
    $mo=$row['balance'] + $calPday;
    $row->balance=$mo;
    $row->save();
}
}
public function colo()
{
    $to= date("Y-m-d");
    $mo=safe_lock::get();
    foreach ($mo as $row){
        $date=$row["date"];

        if ($date==$to){
            $lo=safe_lock::where('date', $to)
                ->where('status', '1')->get();
            foreach ($lo as $lop){
                $username=$lop["username"];
                $balance=$lop["balance"];
            }
            $in=wi::create([
               'username'=>$username,
               'amount'=>$balance,
            ]);
            $kin=$row['balance'] - $balance;
            $row->balance=$kin;
            $row->status=0;
            $row->save();

            $wallet=wallet::where('username', $username)->get();
            foreach ($wallet as $wall){
                $wa=$wall->balance + $balance;
                $wall->balance=$wa;
                $wall->save();

                $userp=User::where('username', $username)->get();
                foreach ($userp as $user){
                    $receiver = encription::decryptdata($user->email);
                    $admin = 'info@renomobilemoney.com';


                    Mail::to($receiver)->send(new Emailclon($in));
                    Mail::to($admin)->send(new Emailclon($in));
                }
            }


        }
    }
}
public function lit()
{
    $in=interest::orderBy('id', 'desc')->paginate(30);
    return view("admin/interest", compact("in"));
}
public function wi()
{
    $wi=wi::orderBy('id', 'desc')->paginate(30);
        return view("admin/com", compact("wi"));
}
}
