<?php

namespace App\Http\Controllers\admin;

use app\Models\admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController
{
public function login(Request $request)
{

    $request->validate([
        'email' => 'required',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)
        ->where('role', 'admin')
        ->first();

    if (!isset($user) || !Hash::check($request->password, $user->password)) {
        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['admin' => 'Invalid credentials or you have not been assigned as an admin.']);
    }

    Auth::login($user);

    return redirect()->intended('admin/dashboard')->withSuccess('Signed in');

}
public function index()
{
    return view('admin.auth.login');

}
}
