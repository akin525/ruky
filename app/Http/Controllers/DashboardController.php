<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\airtimecons;
use App\Models\bank;
use App\Models\bill;
use App\Models\data;
use App\Models\deposit;
use App\Models\easy;
use App\Models\server;
use App\Models\transaction;
use App\Models\User;
use App\Models\wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class DashboardController
{
    public function log(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Authentication successful
            $transaction = transaction::create([
                'username' => Auth::user()->username,
                'activities' => 'You Just Login Successful on ' . Carbon::now(),
            ]);
            return redirect()->intended('/account');
        } else {
            // Invalid credentials
            return back()->withErrors(['email' => 'Invalid credentials']);
        }


    }

    public function dash(Request $request)
    {
        $user = User::where('username', Auth::user()->username)->first();
        $wallet = wallet::where('username', Auth::user()->username)->first();
        $tdepo = deposit::where('username', Auth::user()->username)->sum('amount');
        $tbill = bill::where('username', Auth::user()->username)->sum('amount');
        $trans = transaction::where('username', Auth::user()->username)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $transactions = bill::orderBy('timestamp')->get();

        $dates = $transactions->pluck('timestamp');
        $amounts = $transactions->pluck('amount');

        $time = date("H");
        $timezone = date("e");
        if ($time < "12") {
            $greet = "Good morning ☀️";
        } else
            if ($time >= "12" && $time < "17") {
                $greet = "Good afternoon 🌞";
            } else
                if ($time >= "17" && $time < "19") {
                    $greet = "Good evening 🌙";
                } else
                    if ($time >= "19") {
                        $greet = "Good night 🌚";
                    }
        return view('dashboard', compact('user', 'wallet', 'tdepo', 'tbill', 'greet', 'trans', 'dates', 'amounts'));
    }

    public function airtimeindex()
    {
        $server=airtimecons::where('status', 1)->first();
        if ($server) {
            $ads=Advert::inRandomOrder()->first();
//            return $ads;
            return view('bills.airtime', compact('server', 'ads'));
        }else{
            $mg="No service";
            Alert::success('oops', $mg);
            return back();
        }

    }
    public function dataindex()
    {
        $server=server::where('status', 1)->first();
        if ($server) {
            $ads=Advert::inRandomOrder()->first();
//            return $ads;
            return view('bills.pick', compact('server', 'ads'));
        }else{
            $mg="No service";
            Alert::success('oops', $mg);
            return back();
        }

    }

    function picknetwork($request)
    {
        $server=server::where('status', 1)->first();
        $typeo='mtn-sme';
            if($server) {
                $netm=data::where('network', 'like', '%'.$request.'%')
                    ->where('status', '1')->get();
//                $sme=data::where('network', 'like', '%mtn-sme%')->where('status', '1')->first();

//                return $sme;
                $neta=easy::where('network', 'like', '%'.$request.'%')
                    ->where('status', '1')->get();
                $net9=easy::where('network', 'like', '%'.$request.'%')
                    ->where('status', '1')->get();
                $netg=easy::where('network','like', '%'.$request.'%')
                    ->where('status', '1')->get();

//                return $net9;

                return view('bills.data', compact('net9', 'neta', 'netg', 'netm', 'server', 'request' ));

            }else{

                Alert::info('oops..', 'No service');
                return back();
            }

    }

    function netwplanrequest(Request $request, $selectedValue)
    {
        $server=server::where('status', 1)->first();

        if ($server->name =='mcd') {
            $options = data::where('network', $selectedValue)->get();
            return response()->json($options);
        }elseif($server->name == 'easyaccess'){
            $options = easy::where('network', $selectedValue)->get();
            return response()->json($options);
        }

    }

    public function invoice(Request $request)
    {
        if (Auth::check()) {
            $user = User::find($request->user()->id);
            $bill = bill::where('username', $user->username)->get();

            return view('invoice', compact('user', 'bill'));
        }

        return redirect("login")->withSuccess('You are not allowed to access');
    }

    public function getTransactions()
    {
        $transactions = deposit::where('username', Auth::user()->username)->selectRaw('DATE(created_at) as date, SUM(amount) as total_amount')
            ->groupBy('created_at')
            ->orderBy('created_at', 'ASC')
            ->get();

        $dates = $transactions->pluck('date')->toArray();
        $amounts = $transactions->pluck('total_amount')->toArray();

        return response()->json([
            'dates' => $dates,
            'amounts' => $amounts,
        ]);
    }

    public function getTransactions1()
    {
        $transactions = bill::where('username', Auth::user()->username)->selectRaw('DATE(timestamp) as date, SUM(amount) as total_amount')
            ->groupBy('timestamp')
            ->orderBy('timestamp', 'ASC')
            ->get();

        $dates = $transactions->pluck('date')->toArray();
        $amounts = $transactions->pluck('total_amount')->toArray();

        return response()->json([
            'dates' => $dates,
            'amounts' => $amounts,
        ]);
    }
}
