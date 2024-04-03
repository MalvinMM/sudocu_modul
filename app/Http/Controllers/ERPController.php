<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ERP;
use App\Models\Table;
use App\Models\UserERP;
use App\Models\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ERPController extends Controller
{
    // Start Function For Database Documentation
    public function erpMenu($erp)
    {
        $erp = ERP::where('Initials', $erp)->first();

        // Untuk reset detail count (pada bagaian report & module [count untuk sequence])
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');

        if (auth()->user()->Role == 'Admin') {
            return view('admin.erp.index', compact('erp'));
        } elseif (auth()->user()->Role == 'PIC' and !UserERP::where([
            ['UserID', auth()->user()->UserID],
            ['ERPID', $erp->ERPID]
        ])->first()) {
            return  redirect()->back();
        } else {
            return view('erp.index', compact('erp'));
        }
    }
}
