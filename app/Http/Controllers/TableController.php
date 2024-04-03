<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ERP;
use App\Models\Table;
use App\Models\DetailTable;
use App\Imports\ImportTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    public function masterTable($erp)
    {
        $obj = ERP::where('Initials', $erp)->first();
        $tables = $obj->tables()->paginate(10);
        return view('admin.erp.table.index', compact('erp', 'tables'));
    }

    // ADDING TABLE CUMA UNTUK LOCAL
    // public function addTable($erp)
    // {
    //     return view('admin.erp.table.add_table', compact('erp'));
    // }

    // public function storeTable(Request $request, $erp)
    // {
    //     $erpID = ERP::where('Initials', $erp)->first()->ERPID;

    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'Name'    => [
    //                 'required', 'max:50', Rule::unique('m_table')->where(function ($query) use ($request) {
    //                     return $query->whereRaw('LOWER(Name) = ?', [strtolower($request->Name)]);
    //                 })
    //             ],
    //             'Description' => 'required'
    //         ]
    //     );

    //     if ($validator->fails()) {
    //         // flash('error')->error();
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     Table::create([
    //         'Name' => $request->Name,
    //         'Description' => $request->Description,
    //         'ERPID' => $erpID,

    //         'CreateUserID' => auth()->user()->UserID,
    //         'CreateDateTime' => Carbon::now('GMT+7'),
    //         'UpdateUserID' => auth()->user()->UserID,
    //         'UpdateDateTime' => Carbon::now('GMT+7'),
    //     ]);
    //     return redirect()->route('masterTable', $erp);
    // }


    // public function editTable($erp, $id)
    // {
    //     $parent = Table::find($id);
    //     // $fields = $parent->fields;
    //     // $tables = Table::all();
    //     return view('admin.erp.table.edit_table', compact(['erp', 'parent']));
    // }

    // EDIT DLL DENGAN ASUMSI TABLE DAN FIELD SUDAH ADA DI DATABASE (JADI GUNANYA UNTUK READ)
    public function updateTable(Request $request, $erp)
    {
        // dd($request->all());
        foreach ($request->tableID as $index => $id) {
            $obj = Table::find($id);
            $obj->update([
                'Description' => $request->description[$index]
            ]);
        }
        session()->flash('success', 'Deskripsi Tabel Berhasil Di-update');
        return redirect()->route('masterTable', $erp);
    }
    public function detailTable($erp, $id)
    {
        $parent = Table::find($id);
        $fields = $parent->fields;
        $tables = Table::where('ERPID', $parent->ERPID)->get();
        return view('admin.erp.table.detail_table', compact(['erp', 'parent', 'fields', 'tables']));
    }

    public function fetchFields($tableID)
    {
        $fields = DetailTable::where('TableID', $tableID)->get();
        return response()->json($fields);
    }

    public function updateFields(Request $request, $erp)
    {
        $request->validate([
            'tableIDRef' => 'array',
            'fieldIDRef' => 'array',
        ]);

        // Get the tableIDRef and fieldIDRef arrays from the request
        $tableIDRefArray = $request->input('tableIDRef', []);
        $fieldIDRefArray = $request->input('fieldIDRef', []);

        // Loop through the tableIDRef array and check if fieldIDRef is required
        foreach ($tableIDRefArray as $index => $tableIDRef) {
            if ($tableIDRef && !isset($fieldIDRefArray[$index])) {
                // If tableIDRef is present but fieldIDRef is missing, add an error message
                $validator = Validator::make([], []); // Create a new validator instance
                $validator->errors()->add('fieldIDRef.' . $index, 'fieldIDRef perlu untuk diisi apabila tableIDRef diisi.');
                // Merge the errors with existing errors in the request
                $request->merge(['errors' => $validator->errors()]);
            }
        }

        // Check if validation failed
        if ($request->has('errors')) {
            session()->flash('danger', 'Field Gagal Di-update, Periksa kembali form.');
            return redirect()->back()->withErrors($request->errors)->withInput();
        }

        foreach ($request->fieldID as $index => $fieldID) {
            $obj = DetailTable::find($fieldID);
            $obj->update([
                'Description' => $request->description[$index],
                'TableIDRef' => $request->tableIDRef[$index],
                'FieldIDRef' => $request->fieldIDRef[$index]
            ]);
        }
        session()->flash('success', 'Field Berhasil Di-update');
        return redirect()->back();
    }

    public function import(Request $request, $erp)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        Excel::import(new ImportTable($erp), $file);
        return redirect()->back();
    }

    public function search(Request $request, $erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');

        $tables = $erp->tables()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        // dd($tables->links());

        return view('admin.erp.table.index', compact(['tables', 'search', 'erp']));
    }
}
