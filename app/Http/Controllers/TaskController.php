<?php

namespace App\Http\Controllers;

use App\Models\activities;
use Illuminate\Support\Facades\Auth;

class TaskController
{
    public function loadalltask()
    {
        $all=activities::where('username', Auth::user()->username)->get();

        return view('task.task', compact('all'));

    }

}
