<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ERP;
use App\Models\Database;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DBController extends Controller
{
    // Menampilkan list database untuk ERP tertentu
    public function masterDB($erp)
    {
        // Mengambil objek ERP berdasarkan inisialnya
        $erp = ERP::where('Initials', $erp)->first();

        // Mengambil list database terurut berdasarkan nama dan membaginya ke dalam beberapa halaman
        $dbs = $erp->db()->orderByRaw('LOWER(DBName)')->paginate(10);

        return view('admin.erp.db.index', ['dbs' => $dbs, 'erp' => $erp->Initials]);
    }

    // Menampilkan halaman penambahan database
    public function addDB($erp)
    {
        return view('admin.erp.db.add_db', compact(['erp']));
    }

    // Menyimpan database yang baru ditambahkan
    public function storeDB(Request $request, $erp)
    {
        // Mendapatkan ID ERP berdasarkan inisialnya
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Validasi input
        $validator = Validator::make(
            $request->all(),
            [
                'DbName'    => [
                    'required',
                    'max:50',
                    'unique:m_database,DBName,NULL,DBID,ERPID,' . $erpID,
                ],
                'DbServerLoc' => 'required|max:15',
                'DbUserName' => 'required|max:15',
                'DbPassword' => 'required'
            ]
        );

        // Jika validasi gagal, tampilkan pesan kesalahan
        if ($validator->fails()) {
            session()->flash('danger', 'Penambahan Database Gagal, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Menyimpan database baru ke dalam database
        Database::create([
            'DbName' => $request->DbName,
            'ERPID' => $erpID,
            'DbServerLoc' => Str::title($request->DbServerLoc),
            'DbUserName' => $request->DbUserName,
            'DbPassword' => Hash::make($request->DbPassword),
            'CreateUserID' => auth()->user()->UserID,
            'CreateDateTime' => Carbon::now('GMT+7'),
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7'),
        ]);

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman list database
        session()->flash('success', 'Database Telah Berhasil Ditambahkan.');
        return redirect()->route('masterDB', $erp);
    }

    // Menampilkan halaman edit database
    public function editDB($erp, $db)
    {
        $obj = Database::find($db);
        return view('admin.erp.db.edit_db', compact(['obj', 'erp']));
    }

    // Memperbarui database yang sudah ada
    public function updateDB(Request $request, $erp, $db)
    {
        $obj = Database::find($db);

        // Validasi input
        $validator = Validator::make(
            $request->all(),
            // Perlu nama database yang unik untuk ERP ini.
            [
                'DbName'    => ['required', 'max:50', Rule::unique('m_database')->where(function ($query) use ($request) {
                    return $query->whereRaw('LOWER(DbName) = ?', [strtolower($request->DbName)])->where('ERPID', $request->ERPID);
                })->ignore($obj->DbName, 'DbName')],
                'DbServerLoc' => 'required|max:15',
                'DbUserName' => 'required|max:15',
            ]
        );

        if ($validator->fails()) {
            // Jika validasi gagal, tampilkan pesan kesalahan
            session()->flash('danger', 'Database ' . $obj->DBName . ' Gagal Diupdate, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Memperbarui informasi database
        if (!$request->passwordDB) {
            $password = $obj->DbPassword;
        } else {
            $password = Hash::make($request->passwordDB);
        }

        $obj->update([
            $obj->DbName = $request->DbName,
            $obj->DbServerLoc = Str::title($request->DbServerLoc),
            $obj->UserName => $request->DbUserName,
            $obj->DbPassword = $password,
            $obj->UpdateDateTime = Carbon::now('GMT+7'),
            $obj->UpdateUserID = auth()->user()->UserID,
        ]);

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman list database
        session()->flash('success', 'Database ' . $obj->DBName . ' Berhasil Diupdate.');
        return redirect()->route('masterDB', $erp);
    }

    // Menghapus database
    public function deleteDB($erp, $db)
    {
        $obj = Database::find($db);
        $obj->delete();

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman list database
        session()->flash('warning', 'Database ' . $obj->DBName . ' Berhasil Dihapus.');
        return redirect()->route('masterDB', $erp);
    }

    // Mencari database berdasarkan nama
    public function search(Request $request, $erp)
    {
        // Mencari database berdasarkan nama dan menampilkan hasilnya dalam beberapa halaman
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');
        $dbs = $erp->db()->where('DbName', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        return view('admin.erp.db.index', compact(['dbs', 'search', 'erp']));
    }
}
