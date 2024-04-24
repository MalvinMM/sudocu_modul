<?php

namespace App\Http\Controllers;

use App\Models\ERP;
use App\Models\DBFunction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DBFuncController extends Controller
{
    // Menampilkan daftar fungsi database untuk ERP tertentu
    public function masterFunction($erp)
    {
        // Mengambil objek ERP berdasarkan inisialnya
        $erp = ERP::where('Initials', $erp)->first();

        // Mengambil daftar fungsi database terurut berdasarkan nama dan membaginya ke dalam beberapa halaman
        $functions = $erp->functions()->orderByRaw('LOWER(Name)')->paginate(10);

        // Menampilkan halaman index fungsi database untuk ERP tertentu
        return view('admin.erp.db_function.index', ['erp' => $erp->Initials, 'functions' => $functions]);
    }

    // Menampilkan halaman penambahan fungsi database
    public function addFunction($erp)
    {
        return view('admin.erp.db_function.add_function', compact(['erp']));
    }

    // Menyimpan fungsi database yang baru ditambahkan
    public function storeFunction(Request $request, $erp)
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
                    'unique:db_function,Name,NULL,id,ERPID,' . $erpID,
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

        // Menyimpan fungsi database baru ke dalam database
        DBFunction::create([
            'Name' => $request->Name,
            'ERPID' => $erpID,
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query,
        ]);

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman daftar fungsi database
        session()->flash('success', 'Database Function Berhasil Ditambahkan.');
        return redirect()->route('masterFunction', $erp);
    }

    // Menampilkan detail fungsi database
    public function detailFunction($erp, $id)
    {
        $function = DBFunction::find($id);
        return view('admin.erp.db_function.detail_function', compact(['erp', 'function']));
    }

    // Memperbarui fungsi database
    public function updateFunction(Request $request, $erp, $id)
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
            session()->flash('danger', 'Database Function Gagal Diupdate, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Memperbarui informasi fungsi database
        $obj = DBFunction::find($id);
        $obj->update([
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query
        ]);

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman sebelumnya
        session()->flash('success', 'Database Function Telah Diupdate.');
        return redirect()->back();
    }

    // Mencari fungsi database berdasarkan nama
    public function search(Request $request, $erp)
    {
        // Mencari fungsi database berdasarkan nama dan menampilkan hasilnya dalam beberapa halaman
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');
        $functions = $erp->functions()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        return view('admin.erp.db_function.index', compact(['functions', 'search', 'erp']));
    }
}
