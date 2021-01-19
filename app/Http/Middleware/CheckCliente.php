<?php

namespace App\Http\Middleware;

use Closure;

class CheckCliente
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
        if ($request->session()->get('dni') === null){
            return redirect('login')->withErrors('Sesion expirada');
        }
        return $next($request);
    }
}
