<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ERP;
use App\Models\UserERP;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $erp = ERP::where('Initials', $request->route('erp'))->first();
        // dd(UserERP::where('ERPID', 1)->first());
        if ((UserERP::where([
            ['UserID', auth()->user()->UserID],
            ['ERPID', $erp->ERPID]
        ])->first()) or auth()->user()->Role == 'Admin') {
            return $next($request);
        } else {
            return redirect()->back();
        }
    }
}
