<?php

namespace App\Http\Controllers;

use App\Models\DBView;
use App\Models\ERP;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DBViewController extends Controller
{
    public function masterView($erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $views = $erp->views()->paginate(10);
        // dd(auth()->user()->erps);
        return view('admin.erp.db_view.index', ['erp' => $erp->Initials, 'views' => $views]);
    }

    public function addView($erp)
    {
        return view('admin.erp.db_view.add_view', compact(['erp']));
    }

    public function storeView(Request $request, $erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
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
            // flash('error')->error();
            session()->flash('danger', 'Database View Gagal Ditambahkan, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DBView::create([
            'Name' => $request->Name,
            'ERPID' => $erpID,
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query,

            // 'CreateUserID' => auth()->user()->UserID,
            // 'CreateDateTime' => Carbon::now('GMT+7'),
            // 'UpdateUserID' => auth()->user()->UserID,
            // 'UpdateDateTime' => Carbon::now('GMT+7'),
        ]);
        session()->flash('success', 'Database View Berhasil Ditambahkan.');
        return redirect()->route('masterView', $erp);
    }

    public function detailView($erp, $id)
    {
        $view = DBView::find($id);
        return view('admin.erp.db_view.detail_view', compact(['erp', 'view']));
    }

    public function updateView(Request $request, $erp, $id)
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
            session()->flash('danger', 'Database View Gagal Diupdate, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $obj = DBView::find($id);
        $obj->update([
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query
        ]);

        session()->flash('success', 'Database View Telah Diupdate.');
        return redirect()->back();
    }

    public function search(Request $request, $erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');

        $views = $erp->views()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        // dd($views->links());

        return view('admin.erp.db_view.index', compact(['views', 'search', 'erp']));
    }
}
