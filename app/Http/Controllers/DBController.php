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
    public function masterDB($erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $dbs = $erp->db()->paginate(10);
        // dd(auth()->user()->erps);
        return view('admin.erp.db.index', ['dbs' => $dbs, 'erp' => $erp->Initials]);
    }

    public function addDB($erp)
    {
        return view('admin.erp.db.add_db', compact(['erp']));
    }

    public function storeDB(Request $request, $erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
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

        if ($validator->fails()) {
            // flash('error')->error();
            session()->flash('danger', 'Penambahan Database Gagal, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

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
        session()->flash('success', 'Database Telah Berhasil Ditambahkan.');

        return redirect()->route('masterDB', $erp);
    }

    public function editDB($erp, $db)
    {
        $obj = Database::find($db);
        return view('admin.erp.db.edit_db', compact(['obj', 'erp']));
    }

    public function updateDB(Request $request, $erp, $db)
    {
        $obj = Database::find($db);
        $validator = Validator::make(
            $request->all(),
            [
                'DbName'    => ['required', 'max:50', Rule::unique('m_database')->where(function ($query) use ($request) {
                    return $query->whereRaw('LOWER(DbName) = ?', [strtolower($request->DbName)])->where('ERPID', $request->ERPID);
                })->ignore($obj->DbName, 'DbName')],
                'DbServerLoc' => 'required|max:15',
                'DbUserName' => 'required|max:15',
            ]
        );

        if ($validator->fails()) {
            // flash('error')->error();
            session()->flash('danger', 'Database ' . $obj->DBName . ' Gagal Diupdate, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

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
        session()->flash('success', 'Database ' . $obj->DBName . ' Berhasil Diupdate.');
        return redirect()->route('masterDB', $erp);
    }

    public function deleteDB($erp, $db)
    {
        $obj = Database::find($db);
        $obj->delete();

        session()->flash('warning', 'Database ' . $obj->DBName . ' Berhasil Dihapus.');
        return redirect()->route('masterDB', $erp);
    }

    public function search(Request $request, $erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');

        $dbs = $erp->db()->where('DbName', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        // dd($dbs->links());

        return view('admin.erp.db.index', compact(['dbs', 'search', 'erp']));
    }
}
