<?php

namespace App\Http\Controllers\admin;

use App\Models\airtimecons;
use App\Models\big;
use App\Models\data;
use App\Models\easy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController
{
public function index()
{
    $product=data::paginate(30);

    return view('admin.product.product', compact('product'));
}
    public function index1()
    {
        $product=big::paginate(50);

        return view('admin/product1', compact('product'));
    }
    public function index2()
    {
        $product=easy::paginate(50);

        return view('admin/product/product2', compact('product'));
    }
public function on(Request $request)
{
    $product = data::where('id', $request->id)->first();

    if ($product->status == "1") {
        $give = "0";
    } else {
        $give = "1";
    }
    $product->status = $give;
    $product->save();
    Alert::success('Admin', 'Product update successfully');

    return redirect('admin/product');

}
    public function on1(Request $request)
    {
        $product = big::where('id', $request->id)->first();

        if ($product->status == "1") {
            $give = "0";
        } else {
            $give = "1";
        }
        $product->status = $give;
        $product->save();
        Alert::success('Admin', 'Product update successfully');

        return redirect('admin/product1');

    }
    public function on2(Request $request)
    {
        $product = easy::where('id', $request->id)->first();

        if ($product->status == "1") {
            $give = "0";
        } else {
            $give = "1";
        }
        $product->status = $give;
        $product->save();
        Alert::success('Admin', 'Product update successfully');

        return redirect('admin/product/product2');

    }
public function in(Request $request)
{

    $pro=data::where('id', $request->id)->first();

return view('admin.product.editproduct', compact('pro'));
}
    public function in1(Request $request)
    {

        $pro=big::where('id', $request->id)->first();

        return view('admin/editproduct1', compact('pro'));
    }
    public function in2(Request $request)
    {

        $pro=easy::where('id', $request->id)->first();

        return view('admin/product/editproduct2', compact('pro'));
    }
public function edit(Request $request)
{
    $request->validate([
        'id' => 'required',
        'tamount' => 'required',
        'ramount' => 'required',
        'name' => 'required',
    ]);
    $pro=data::where('id', $request->id)->first();
    $pro->name=$request->name;
    $pro->tamount=$request->tamount;
    $pro->ramount=$request->ramount;
    $pro->save();
    return response()->json([
        'status'=>'success',
        'message'=>'Product update successfully',
    ]);


}
    public function edit1(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'amount' => 'required',
            'tamount' => 'required',
            'ramount' => 'required',
            'name' => 'required',
        ]);
        $pro=big::where('id', $request->id)->first();
        $pro->plan=$request->name;
        $pro->amount=$request->amount;
        $pro->tamount=$request->tamount;
        $pro->ramount=$request->ramount;
        $pro->save();
        return response()->json([
            'status'=>'success',
            'message'=>'Product update successfully',
        ]);

    }
    public function edit2(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'tamount' => 'required',
            'ramount' => 'required',
            'name' => 'required',
        ]);
        $pro=easy::where('id', $request->id)->first();
        $pro->plan=$request->name;
        $pro->tamount=$request->tamount;
        $pro->ramount=$request->ramount;
        $pro->save();
        return response()->json([
            'status'=>'success',
            'message'=>'Product update successfully',
        ]);

    }


public function air()
{
    $air=DB::table('airtimecons')->get();

    return view('admin.server.airtime', compact("air"));
}

public function pair(Request $request)
{
    $air = airtimecons::where('id', $request->id)->first();
    if ($air->status == 1){
        $na= '0';
    }elseif ($air->status == 0){
        $na='1';
    }

    $air->status=$na;
    $air->save();

    return redirect('admin/air')->with('status', 'Server update successfully');

}


}
