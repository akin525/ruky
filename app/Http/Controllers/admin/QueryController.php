<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\bill_payment;
use App\Models\bo;
use App\Models\deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryController extends Controller
{
function queryindex()
{
    $sum=deposit::sum('amount');

    return view('admin/depodate', compact('sum'));
}
function billdate()
{
    $sum=bill_payment::sum('amount');

    return view('admin/billdate', compact('sum'));
}
function querydeposi(Request $request)
{
    $request->validate([
        'from'=>'required',
        'to'=>'required',
    ]);

    $deposit=DB::table('deposits')
        ->whereBetween('date', [$request->from, $request->to])->get();
    $sum=deposit::sum('amount');
    $sumdate=DB::table('deposits')
        ->whereBetween('date', [$request->from, $request->to])->sum('amount');

    return view('admin/depodate', ['sum' => $sum, 'sumdate'=>$sumdate, 'deposit'=>$deposit, 'result'=>true]);


}
function querybilldate(Request $request)
{
    $request->validate([
        'from'=>'required',
        'to'=>'required',
    ]);

    $deposit=DB::table('bill_payments')
        ->whereBetween('timestamp', [$request->from, $request->to])->get();
    $sum=bill_payment::sum('amount');
    $sumdate=DB::table('bill_payments')
        ->whereBetween('timestamp', [$request->from, $request->to])->sum('amount');

    return view('admin/billdate', ['sum' => $sum, 'sumdate'=>$sumdate, 'deposit'=>$deposit, 'result'=>true]);


}
}
