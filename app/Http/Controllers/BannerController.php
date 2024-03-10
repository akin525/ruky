<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class BannerController extends Controller
{
function loadbanner()
{
    $banner=Banner::all();
    return view('admin/banner', compact('banner'));
}
function uploadbanner(Request $request)
{
    $request->validate([
        'pic'=>'required',
        'page'=>'required',
    ]);
    $banner=Banner::where('page', $request->page)->first();
    if($banner){
        Alert::warning('Ooops', 'Kindly delete the old one before uploading the new one');
        return back();
    }
    $b=Storage::put('banner0', $request['pic']);
    $create=Banner::create([
        'picture'=>$b,
        'page'=>1,
    ]);
    Alert::success('Uploaded', 'Banner for page 1 upload successful');
    return back();

}
function uploadbanner1(Request $request)
{
    $request->validate([
        'pic'=>'required',
        'page'=>'required',
    ]);
    $banner=Banner::where('page', $request->page)->first();
    if($banner){
        Alert::warning('Ooops', 'Kindly delete the old one before uploading the new one');
        return back();
    }
    $b=Storage::put('banner0', $request['pic']);
    $create=Banner::create([
        'picture'=>$b,
        'page'=>2,
    ]);
    Alert::success('Uploaded', 'Banner for page 2 upload successful');
    return back();

}
function uploadbanner2(Request $request)
{
    $request->validate([
        'pic'=>'required',
        'page'=>'required',
    ]);
    $banner=Banner::where('page', $request->page)->first();
    if($banner){
        Alert::warning('Ooops', 'Kindly delete the old one before uploading the new one');
        return back();
    }
    $b=Storage::put('banner0', $request['pic']);
    $create=Banner::create([
        'picture'=>$b,
        'page'=>3,
    ]);
    Alert::success('Uploaded', 'Banner for page 3 upload successful');
    return back();

}
function removebp($request)
{
    $banner=Banner::where('page', $request)->first();
    if(Storage::exists($banner->picture)){
        Storage::delete($banner->picture);
        /*
            Delete Multiple File like this way
            Storage::delete(['upload/test.png', 'upload/test2.png']);
        */
    }else{
        Alert::error('Ooops', 'File does not exists');
        return back();
//            dd('File does not exists.');
    }
    $banner=Banner::where('page', $request)->delete();
    $msg="Banner Photo Remove Successful";
    Alert::success('Deleted', $msg);
    return back();
}
}
