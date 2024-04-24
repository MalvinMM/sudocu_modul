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
    // Fungsi untuk menampilkan menu ERP
    public function erpMenu($erp)
    {
        // Mengambil objek ERP berdasarkan inisialnya
        $erp = ERP::where('Initials', $erp)->first();

        // Menghapus data session terkait detail count untuk reset
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');

        // Jika pengguna adalah admin, tampilkan tampilan admin ERP
        if (auth()->user()->Role == 'Admin') {
            return view('admin.erp.index', compact('erp'));
        }
        // Jika pengguna adalah PIC (Person In Charge) dan tidak memiliki akses ke ERP yang dipilih, redirect kembali
        elseif (auth()->user()->Role == 'PIC' and !UserERP::where([
            ['UserID', auth()->user()->UserID],
            ['ERPID', $erp->ERPID]
        ])->first()) {
            return  redirect()->back();
        }
        // Jika pengguna adalah bukan admin dan bukan PIC, tampilkan tampilan ERP biasa
        else {
            return view('erp.index', compact('erp'));
        }
    }
}
