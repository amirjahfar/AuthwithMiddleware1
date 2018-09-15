<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use App\User;
use Closure;

class verifyUserByEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = User::findOrFail(Auth::id());

        if ($user->status == 0){
            Auth::logout();
            return redirect ('login')->with('message','Please Check Your Email to Activate Your Account');
        }
        return $next($request);
    }
}
