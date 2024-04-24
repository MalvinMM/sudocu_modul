<?php

namespace App\Http\Controllers;

use App\Models\DBStoreProc;
use App\Models\ERP;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DBStoreProcController extends Controller
{
    // Menampilkan daftar store procedure untuk ERP tertentu
    public function masterStoreProc($erp)
    {
        // Mengambil objek ERP berdasarkan inisialnya
        $erp = ERP::where('Initials', $erp)->first();

        // Mengambil daftar store procedure terurut berdasarkan nama dan membaginya ke dalam beberapa halaman
        $storeProcs = $erp->storeProcs()->orderByRaw('LOWER(Name)')->paginate(10);

        // Menampilkan halaman index store procedure untuk ERP tertentu
        return view('admin.erp.db_storeProc.index', ['erp' => $erp->Initials, 'storeProcs' => $storeProcs]);
    }

    // Menampilkan halaman penambahan store procedure
    public function addStoreProc($erp)
    {
        return view('admin.erp.db_storeProc.add_storeProc', compact(['erp']));
    }

    // Menyimpan store procedure yang baru ditambahkan
    public function storeStoreProc(Request $request, $erp)
    {
        // Mendapatkan ID ERP berdasarkan inisialnya
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Validasi input
        $validator = Validator::make(
            $request->all(),
            [
                'Name'    => [
                    'required',
                    'max:50',
                    'unique:db_storeProc,Name,NULL,id,ERPID,' . $erpID,
                ],
                'Description' => 'required',
                'SQL_Query' => 'required'
            ]
        );

        if ($validator->fails()) {
            // Jika validasi gagal, tampilkan pesan kesalahan
            session()->flash('danger', 'Database Store Procedure Gagal Ditambahkan, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Menyimpan store procedure baru ke dalam database
        DBStoreProc::create([
            'Name' => $request->Name,
            'ERPID' => $erpID,
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query,
        ]);

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman daftar store procedure
        session()->flash('success', 'Database Store Procedure Berhasil Ditambahkan.');
        return redirect()->route('masterStoreProc', $erp);
    }

    // Menampilkan detail store procedure
    public function detailStoreProc($erp, $id)
    {
        $storeProc = DBStoreProc::find($id);
        return view('admin.erp.db_storeProc.detail_storeProc', compact(['erp', 'storeProc']));
    }

    // Memperbarui store procedure
    public function updateStoreProc(Request $request, $erp, $id)
    {
        // Validasi input
        $validator = Validator::make(
            $request->all(),
            [
                'Description' => 'required',
                'SQL_Query' => 'required'
            ],
            [
                'SQL_Query.required' => "SQL Query perlu untuk diisi."
            ]
        );

        if ($validator->fails()) {
            // Jika validasi gagal, tampilkan pesan kesalahan
            session()->flash('danger', 'Database Store Procedure Gagal Diupdate, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Memperbarui informasi store procedure
        $obj = DBStoreProc::find($id);
        $obj->update([
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query
        ]);

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman sebelumnya
        session()->flash('success', 'Database StoreProc Telah Diupdate.');
        return redirect()->back();
    }

    // Mencari store procedure berdasarkan nama
    public function search(Request $request, $erp)
    {
        // Mencari store procedure berdasarkan nama dan menampilkan hasilnya dalam beberapa halaman
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');
        $storeProcs = $erp->storeProcs()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        return view('admin.erp.db_storeProc.index', compact(['storeProcs', 'search', 'erp']));
    }
}
