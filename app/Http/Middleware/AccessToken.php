<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
//     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('apikey')) {
            $errors = [];
            array_push($errors, ['code' => 'apikey', 'message' => 'Api key is required!']);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
            $apikey = $request->header('apikey');

            $user = User::where('apikey',$apikey)->first();
            if (!$user){
                return response()->json([
                    'message' => "Invalid Api key"
                ], 403);
            }

        return $next($request);
    }
}
