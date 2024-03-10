<?php


namespace App\Http\Controllers;


use App\Models\Donate;
use App\Models\Plan;
use App\Models\RequestFund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class RequesfundController extends Controller
{
    public function index()
    {
        return view('request');
    }
public function fund($request)
{
    $plan=Plan::where('name', $request)->first();
//    return $plan;
    return view('requestfund', compact('plan'));
}

public function submitfund(Request $request)
{
    $request->validate([
        'id'=>'required',
        'name'=>'required',
        'address'=>'required',
        'amount'=>'required',
        'duration'=>'required',
    ]);
    $plan=Plan::where('id', $request->id)->first();

    $post=RequestFund::create([
        'username'=>Auth::user()->username,
        'plan'=>$plan->name,
        'duration'=>$request->duration,
        'amount'=>$plan->amount,
    ]);
    $mg="Request Successful Submitted, Kindly Visit us at our office for confirmation";
    Alert::info('Pending', $mg);
    return back();

}

}
