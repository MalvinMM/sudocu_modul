<?php

namespace App\Http\Controllers;

use App\Models\DBView;
use App\Models\ERP;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DBViewController extends Controller
{
    // Menampilkan daftar view database untuk ERP tertentu
    public function masterView($erp)
    {
        // Mengambil objek ERP berdasarkan inisialnya
        $erp = ERP::where('Initials', $erp)->first();

        // Mengambil daftar view database terurut berdasarkan nama dan membaginya ke dalam beberapa halaman
        $views = $erp->views()->orderByRaw('LOWER(Name)')->paginate(10);

        // Menampilkan halaman index view database untuk ERP tertentu
        return view('admin.erp.db_view.index', ['erp' => $erp->Initials, 'views' => $views]);
    }

    // Menampilkan halaman penambahan view database
    public function addView($erp)
    {
        return view('admin.erp.db_view.add_view', compact(['erp']));
    }

    // Menyimpan view database yang baru ditambahkan
    public function storeView(Request $request, $erp)
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
                    'unique:db_views,Name,NULL,id,ERPID,' . $erpID,
                ],
                'Description' => 'required',
                'SQL_Query' => 'required'
            ]
        );

        if ($validator->fails()) {
            // Jika validasi gagal, tampilkan pesan kesalahan
            session()->flash('danger', 'Database View Gagal Ditambahkan, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Menyimpan view database baru ke dalam database
        DBView::create([
            'Name' => $request->Name,
            'ERPID' => $erpID,
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query,
        ]);

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman daftar view database
        session()->flash('success', 'Database View Berhasil Ditambahkan.');
        return redirect()->route('masterView', $erp);
    }

    // Menampilkan detail view database
    public function detailView($erp, $id)
    {
        $view = DBView::find($id);
        return view('admin.erp.db_view.detail_view', compact(['erp', 'view']));
    }

    // Memperbarui view database
    public function updateView(Request $request, $erp, $id)
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
            session()->flash('danger', 'Database View Gagal Diupdate, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Memperbarui informasi view database
        $obj = DBView::find($id);
        $obj->update([
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query
        ]);

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman sebelumnya
        session()->flash('success', 'Database View Telah Diupdate.');
        return redirect()->back();
    }

    // Mencari view database berdasarkan nama
    public function search(Request $request, $erp)
    {
        // Mencari view database berdasarkan nama dan menampilkan hasilnya dalam beberapa halaman
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');
        $views = $erp->views()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        return view('admin.erp.db_view.index', compact(['views', 'search', 'erp']));
    }
}
