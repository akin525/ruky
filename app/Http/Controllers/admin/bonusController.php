<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Giveaway;

class bonusController extends Controller
{
function giveawayall()
{
    $give=Giveaway::all();
    return view('admin/giveaway', ["give"=>$give]);
}
function claimby()
{
    $claim=Claim::with('parentData')->get();
    return view('admin/claim', compact('claim'));
}

}
