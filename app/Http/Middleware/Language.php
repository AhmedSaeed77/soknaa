<?php

namespace App\Http\Middleware;

use Closure;

class Language
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
       // app()->setLocale();
         $lang = ($request->hasHeader('localization')) ? $request->header('localization') : 'ar';
        //Set laravel localization
        app()->setLocale($lang);

        //Continue request
        return $next($request);
    }
}