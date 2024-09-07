<?php

namespace App\Http\Middleware;
use App\Traits\GeneralTrait;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfUserIsBlocked
{
    use GeneralTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (Auth::check() && Auth::user()->is_removed)
        // {
        //     Auth::logout();
        //     return $this->returnError(422,__('dashboard.user_is_removed'));
        // }
        // if (Auth::check())
        // {
        //     Auth::logout();
        //     return $this->returnError(422,__('dashboard.user_is_removed'));
        // }
        return $next($request);
    }
}
