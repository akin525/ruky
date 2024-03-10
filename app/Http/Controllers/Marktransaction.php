<?php

namespace App\Http\Controllers;

use App\Models\bill_payment;
use RealRashid\SweetAlert\Facades\Alert;

class Marktransaction extends Controller
{

    function accepttransaction($request)
    {
        $goods=bill_payment::where('id',$request )->first();
        $update=1;

        $goods->status=$update;
        $goods->save();

        $msg="Transaction Approved";
        Alert::success('Done', $msg);
        return back();
    }
}
