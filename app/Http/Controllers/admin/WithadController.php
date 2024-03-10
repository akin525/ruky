<?php

namespace App\Http\Controllers\admin;

use App\Console\encription;
use App\Mail\done;
use App\Mail\withdraws;
use App\Models\User;
use App\Models\withdraw;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class WithadController
{
public function index()
{
    $all=withdraw::orderBy('id', 'desc')->paginate(15);
    $total=withdraw::sum('amount');
    return view("admin/request", compact("all", "total"));
}
public function approve($request)
{

    $w=withdraw::where('id', $request)->first();
    $m=1;
    $user = User::where('username', $w->username)->first();

    $w->status=$m;
    $w->save();

    $receiver = encription::decryptdata($user->email);
    $admin = 'info@renomobilemoney.com';
    $insert['username']=$w->username;
$insert['ko']="Dear ".encription::decryptdata($w->username)." your withdraw was successful processed, Thanks";

    Mail::to($receiver)->send(new done($insert));
    Mail::to($admin)->send(new done($insert));
    $mg= "Withdraw request was approve successfully";
    Alert::success('Admin', $mg);
    return back();
}
    public function disapprove($request)
    {
        $w=withdraw::where('id', $request)->first();
        $user = User::where('username', $w->username)->first();

        $m=2;

        $w->status=$m;
        $w->save();
        $receiver = encription::decryptdata($user->email);
        $admin = 'info@renomobilemoney.com';
        $insert['username']=$w->username;
        $insert['ko']="Dear ".encription::decryptdata($w->username)." your withdraw was disapproved, Thanks";

        Mail::to($receiver)->send(new done($insert));
        Mail::to($admin)->send(new done($insert));
        $mg= "Withdraw request was disapprove successfully";
        Alert::info('Admin', $mg);
        return back();
    }

}
