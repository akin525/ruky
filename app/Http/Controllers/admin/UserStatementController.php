<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\bill_payment;
use App\Models\deposit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserStatementController extends Controller
{
function loadindex()
{
    $user=User::all();
    return view('admin/statement', compact('user'));
}
function loadindex1()
{
    $user=User::all();
    return view('admin/statement1', compact('user'));
}
function customerstatementfunding(Request $request)
{
    $request->validate([
        'from'=>'required',
        'to'=>'required',
        'username'=>'required',
    ]);

    $deposit=DB::table('deposits')
        ->whereBetween('date', [$request->from, $request->to])
        ->where('username', $request->username)
        ->get();
    $sum=deposit::where('username', $request->username)
        ->sum('amount');

    $user=User::all();

    return view('admin/statement', ['user' => $user, 'deposit'=>$deposit, 'sum'=>$sum, 'result'=>true]);
}

function customerstatementpurchase(Request $request)
{
    $request->validate([
        'from'=>'required',
        'to'=>'required',
        'username'=>'required',
    ]);

    $purchase=DB::table('bill_payments')
        ->whereBetween('timestamp', [$request->from, $request->to])
        ->where('username', $request->username)
        ->get();
    $sum=bill_payment::where('username', $request->username)
        ->sum('amount');

    $user=User::all();

    return view('admin/statement1',['user' => $user, 'deposit'=>$purchase, 'sum'=>$sum, 'result'=>true]);
}
}
