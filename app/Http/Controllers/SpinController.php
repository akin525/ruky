<?php

namespace App\Http\Controllers;

use App\Models\activities;
use Illuminate\Support\Facades\Auth;

class SpinController
{
    public function loadspin()
    {

        $all=activities::where('username', Auth::user()->username)->get();

        return view('task.spin', compact('all'));

    }

}
