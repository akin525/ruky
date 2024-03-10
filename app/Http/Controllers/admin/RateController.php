<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\bill_payment;
use App\Models\deposit;
use App\Models\safe_lock;

class RateController extends Controller
{

    function highestdeposit()
    {
        $docs = deposit::groupBy('username')
            ->selectRaw('count(username) as count, username')
            ->selectRaw('sum(amount) as amount')
            ->orderByRaw('amount DESC')
            ->get();
        return view('admin/ratedeposit', compact('docs'));
    }
    function highestpurchase()
    {
        $docs = bill_payment::groupBy('username')
            ->selectRaw('count(username) as count, username')
            ->selectRaw('sum(amount) as amount')
            ->orderByRaw('amount DESC')
            ->get();
        return view('admin/ratepurchase', compact('docs'));
    }
    function highestsafelock()
    {
        $docs = safe_lock::groupBy('username')
            ->selectRaw('count(username) as count, username')
            ->selectRaw('sum(balance) as amount')
            ->orderByRaw('amount DESC')
            ->get();
        return view('admin/ratelock', compact('docs'));
    }

}
