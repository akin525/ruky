<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Models\User;
use App\Models\data;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;


class BuyController
{
    public function buydata(Request  $request)
    {
        $apikey = $request->header('apikey');
        $user = User::where('apikey',$apikey)->first();
        if ($user) {
            $data = data::where('status',1 )->get();

            return response()->json([
                'message' => 'Data Fetch Successfully',
                'user' => $user, 'data' =>$data,
            ], 200);
        }

        return response()->json([
            'message' => 'You are not allowed to access'
        ], 200);
    }

    public function pre(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $this->error_processor($validator)], 403);
        }
        $apikey = $request->header('apikey');
        $user = User::where('apikey',$apikey)->first();
        if ($user) {
            $data = data::where('id',$request->id )->get();
            return response()->json([
                'message' => 'Data Fetch Successfully',
                'user' => $user, 'data' =>$data
            ], 200);
        }

        return response()->json([
            'message' => 'You are not allowed to access'
        ], 200);
    }

    public function airtime(Request  $request)
    {
        $apikey = $request->header('apikey');
        $user = User::where('apikey',$apikey)->first();
        if ($user) {
            $data = data::where('plan_id',"airtime" )->get();

            return response()->json([
                'message' => 'Data Fetch Successfully',
                'user' => $user, 'data' =>$data
            ], 200);
        }

        return response()->json([
            'message' => 'You are not allowed to access'
        ], 200);
    }
    public function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'success'=>0, 'message' => $error[0]]);
        }
        return $err_keeper;
    }
}
