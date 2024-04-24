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
    // Menampilkan daftar tabel dari ERP tertentu
    public function masterTable($erp)
    {
        // Mengambil objek ERP berdasarkan inisialnya
        $erp = ERP::where('Initials', $erp)->first();

        // Mengambil daftar tabel terurut berdasarkan nama dan membaginya ke dalam beberapa halaman
        $tables = $erp->tables()->orderByRaw('LOWER(Name)')->paginate(10);

        // Menampilkan halaman index tabel untuk ERP tertentu
        return view('admin.erp.table.index', compact('erp', 'tables'));
    }

    // Memperbarui deskripsi tabel
    public function updateTable(Request $request, $erp)
    {
        // Mengambil objek tabel berdasarkan ID dan memperbarui deskripsinya
        foreach ($request->tableID as $index => $id) {
            $obj = Table::find($id);
            $obj->update([
                'Description' => $request->description[$index]
            ]);
        }

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman master tabel
        session()->flash('success', 'Deskripsi Tabel Berhasil Di-update');
        return redirect()->route('masterTable', $erp);
    }

    // Menampilkan detail tabel dan fieldnya
    public function detailTable($erp, $id)
    {
        // Mengambil objek tabel berdasarkan ID dan menampilkannya beserta field dan tabel terkait
        $parent = Table::find($id);
        $fields = $parent->fields;
        $tables = Table::where('ERPID', $parent->ERPID)->get();
        return view('admin.erp.table.detail_table', compact(['erp', 'parent', 'fields', 'tables']));
    }

    // Mengambil field dari tabel tertentu menggunakan AJAX
    public function fetchFields($tableID)
    {
        // Mengambil field dari tabel tertentu dan mengembalikannya dalam format JSON
        $fields = DetailTable::where('TableID', $tableID)->get();
        return response()->json($fields);
    }

    // Memperbarui field tabel
    public function updateFields(Request $request, $erp)
    {
        // Validasi input
        $request->validate([
            'tableIDRef' => 'array',
            'fieldIDRef' => 'array',
        ]);

        // Mendapatkan array tableIDRef dan fieldIDRef dari permintaan
        $tableIDRefArray = $request->input('tableIDRef', []);
        $fieldIDRefArray = $request->input('fieldIDRef', []);

        // Melakukan iterasi melalui array tableIDRef dan memeriksa apakah fieldIDRef diperlukan
        foreach ($tableIDRefArray as $index => $tableIDRef) {
            if ($tableIDRef && !isset($fieldIDRefArray[$index])) {
                // Jika tableIDRef ada tetapi fieldIDRef tidak ada, tambahkan pesan kesalahan
                $validator = Validator::make([], []); // Membuat instance validator baru
                $validator->errors()->add('fieldIDRef.' . $index, 'fieldIDRef perlu diisi jika tableIDRef diisi.');
                // Menggabungkan kesalahan dengan kesalahan yang sudah ada dalam permintaan
                $request->merge(['errors' => $validator->errors()]);
            }
        }

        // Memeriksa jika validasi gagal
        if ($request->has('errors')) {
            session()->flash('danger', 'Field Gagal Di-update, Periksa kembali form.');
            return redirect()->back()->withErrors($request->errors)->withInput();
        }

        // Memperbarui field tabel berdasarkan input yang diberikan
        foreach ($request->fieldID as $index => $fieldID) {
            $obj = DetailTable::find($fieldID);
            $obj->update([
                'Description' => $request->description[$index],
                'TableIDRef' => $request->tableIDRef[$index],
                'FieldIDRef' => $request->fieldIDRef[$index]
            ]);
        }

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman sebelumnya
        session()->flash('success', 'Field Berhasil Di-update');
        return redirect()->back();
    }

    // Mengimpor tabel dari file Excel
    public function import(Request $request, $erp)
    {
        // Validasi file yang diimpor
        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'file|required|mimes:xlsx,xls|max:2048',
            ]
        );

        // Jika validasi gagal, kembalikan dengan pesan kesalahan
        if ($validator->fails()) {
            session()->flash('danger', 'File gagal diimport. Perhatikan kembali syarat.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Mengimpor tabel dari file Excel yang diberikan
        $file = $request->file('file');
        Excel::import(new ImportTable($erp), $file);

        // Mengarahkan kembali ke halaman sebelumnya setelah berhasil mengimpor
        return redirect()->back();
    }

    // Mencari tabel berdasarkan nama
    public function search(Request $request, $erp)
    {
        // Mencari tabel berdasarkan nama dan menampilkan hasilnya dalam beberapa halaman
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');
        $tables = $erp->tables()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        return view('admin.erp.table.index', compact(['tables', 'search', 'erp']));
    }
}
