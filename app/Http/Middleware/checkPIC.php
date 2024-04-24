<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ERP;
use App\Models\UserERP;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkPIC
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user seorang PIC
        $erp = ERP::where('Initials', $request->route('erp'))->first();

        if (auth()->user()->Role != 'User') {
            return $next($request);
        } else {
            return redirect()->back();
        }
    }
}
