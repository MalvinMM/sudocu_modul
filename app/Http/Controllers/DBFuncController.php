<?php

namespace App\Http\Controllers;

use App\Models\ERP;
use App\Models\DBFunction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DBFuncController extends Controller
{
    public function masterFunction($erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $functions = $erp->functions()->paginate(10);;
        // dd(auth()->user()->erps);
        return view('admin.erp.db_function.index', ['erp' => $erp->Initials, 'functions' => $functions]);
    }

    public function addFunction($erp)
    {
        return view('admin.erp.db_function.add_function', compact(['erp']));
    }

    public function storeFunction(Request $request, $erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
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
            session()->flash('danger', 'Database View Gagal Ditambahkan, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DBFunction::create([
            'Name' => $request->Name,
            'ERPID' => $erpID,
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query,

            // 'CreateUserID' => auth()->user()->UserID,
            // 'CreateDateTime' => Carbon::now('GMT+7'),
            // 'UpdateUserID' => auth()->user()->UserID,
            // 'UpdateDateTime' => Carbon::now('GMT+7'),
        ]);
        session()->flash('success', 'Database Function Berhasil Ditambahkan.');
        return redirect()->route('masterFunction', $erp);
    }

    public function detailFunction($erp, $id)
    {
        $function = DBFunction::find($id);
        return view('admin.erp.db_function.detail_function', compact(['erp', 'function']));
    }

    public function updateFunction(Request $request, $erp, $id)
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
            session()->flash('danger', 'Database Function Gagal Diupdate, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $obj = DBFunction::find($id);
        $obj->update([
            'Description' => $request->Description,
            'SQL_Query' => $request->SQL_Query
        ]);
        session()->flash('success', 'Database Function Telah Diupdate.');

        return redirect()->back();
    }

    public function search(Request $request, $erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');

        $functions = $erp->functions()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        // dd($functions->links());

        return view('admin.erp.db_function.index', compact(['functions', 'search', 'erp']));
    }
}
