<?php

namespace App\Http\Controllers;

use App\Models\web;

class ResponseController extends Controller
{
function responsefunding()
{
    $vertual=web::latest()->get();
        return view('admin/response', compact('vertual'));
}
}
