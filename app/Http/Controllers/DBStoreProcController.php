<?php

namespace App\Http\Controllers;

use App\Models\DBStoreProc;
use App\Models\ERP;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DBStoreProcController extends Controller
{
    public function masterStoreProc($erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $storeProcs = $erp->storeProcs()->paginate(10);        // dd(auth()->user()->erps);
        return view('admin.erp.db_storeProc.index', ['erp' => $erp->Initials, 'storeProcs' => $storeProcs]);
    }

    public function addStoreProc($erp)
    {
        return view('admin.erp.db_storeProc.add_storeProc', compact(['erp']));
    }

    public function storeStoreProc(Request $request, $erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
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
            // flash('error')->error();
            session()->flash('danger', 'Database Store Procedure Gagal Ditambahkan, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DBStoreProc::create([
            'Name' => $request->Name,
            'ERPID' => $erpID,
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query,

            // 'CreateUserID' => auth()->user()->UserID,
            // 'CreateDateTime' => Carbon::now('GMT+7'),
            // 'UpdateUserID' => auth()->user()->UserID,
            // 'UpdateDateTime' => Carbon::now('GMT+7'),
        ]);
        session()->flash('success', 'Database Store Procedure Berhasil Ditambahkan.');
        return redirect()->route('masterStoreProc', $erp);
    }

    public function detailStoreProc($erp, $id)
    {
        $storeProc = DBStoreProc::find($id);
        return view('admin.erp.db_storeProc.detail_storeProc', compact(['erp', 'storeProc']));
    }

    public function updateStoreProc(Request $request, $erp, $id)
    {

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
            // flash('error')->error();
            session()->flash('danger', 'Database Store Procedure Gagal Diupdate, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $obj = DBStoreProc::find($id);
        $obj->update([
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query
        ]);

        // Flash a success message
        session()->flash('success', 'Database StoreProc Telah Diupdate.');

        return redirect()->back();
    }

    public function search(Request $request, $erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');

        $storeProcs = $erp->storeProcs()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        // dd($storeProcs->links());

        return view('admin.erp.db_storeProc.index', compact(['storeProcs', 'search', 'erp']));
    }
}
