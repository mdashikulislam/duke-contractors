<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!(Auth::user()->role == 'Admin')){
            return response()->json([
                'status'=>false,
                'message'=>'You are not authorize to access this route',
                'data'=>null
            ]);
        }
        return $next($request);
    }
}
