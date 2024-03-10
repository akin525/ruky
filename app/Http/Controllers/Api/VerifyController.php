<?php

namespace App\Http\Controllers\Api;

use App\Models\bill_payment;
use App\Models\deposit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerifyController
{
//    error proccesor
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }

//    bill verification
    public function verifybill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success'=>0,
                'errors' => $this->error_processor($validator)
            ], 403);
        }
        $apikey = $request->header('apikey');
        $user = User::where('apikey', $apikey)->first();

        $bill=bill_payment::where('transactionid','api'.$request['refid'])->first();
        if (!isset($bill)){
            $msg="Transaction not found";
            return response()->json([
                'success'=>0,
                'message' => "error", 'data' => $msg
            ], 200);
        }
        if (isset($bill)){
            return response()->json([
                'success'=>1,
                'message' => "Record fetch successfully", 'data' => $bill
            ], 200);
        }
    }

//    verify funding
    public function verifyfunding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success'=>0,
                'errors' => $this->error_processor($validator)
            ], 403);
        }
        $apikey = $request->header('apikey');
        $user = User::where('apikey', $apikey)->first();

        $bill=deposit::where('payment_ref',$request['refid'])->first();
        if (!isset($bill)){
            $msg="Deposit not found";
            return response()->json([
                'success'=>0,
                'message' => "error", 'data' => $msg
            ], 200);
        }
        if (isset($bill)){
            return response()->json([
                'success'=>1,
                'message' => "Deposit fetch successfully", 'data' => $bill
            ], 200);
        }
    }

}
