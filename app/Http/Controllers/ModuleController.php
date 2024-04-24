<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ERP;
use App\Models\Module;
use Illuminate\Support\Str;
use App\Models\DetailModule;
use Illuminate\Http\Request;
use App\Models\ModuleCategory;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    // Menampilkan daftar modul
    public function masterModule($erp)
    {
        // Mengambil objek ERP berdasarkan inisialnya
        $erp = ERP::where('Initials', $erp)->first();

        // Mengambil modul-modul dari ERP tersebut
        $modules = $erp->modules()->orderByRaw('LOWER(Name)')->paginate(10);
        $erp = $erp->Initials;

        // Jika pengguna adalah 'User', tampilkan tampilan modul biasa
        if (auth()->user()->Role == 'User') {
            return view('erp.module.index', compact('modules', 'erp'));
        }

        // Membersihkan sesi terkait detail count untuk reset
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');

        // Jika pengguna adalah admin, tampilkan tampilan admin modul
        return view('admin.erp.module.index', compact('modules', 'erp'));
    }

    // Menampilkan detail modul
    public function detailModule($erp, $id)
    {
        // Mengambil objek modul berdasarkan ID
        $module = Module::find($id);

        // Mengambil detail-detail modul
        $details = $module->details;

        return view('erp.module.detail_module', compact('module', 'erp', 'details'));
    }

    // Menambahkan kategori modul
    public function addCategory($erp)
    {
        // Mengambil ID ERP berdasarkan inisialnya
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Mengambil kategori-kategori modul untuk ERP tersebut
        $categories = ModuleCategory::where('ERPID', $erpID)->paginate(10);

        return view('admin.erp.module.add_category', compact('erp', 'categories'));
    }

    // Menyimpan kategori modul yang baru ditambahkan
    public function storeCategory(Request $request, $erp)
    {
        // Mengambil ID ERP berdasarkan inisialnya
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Validasi data kategori modul
        $request->validate([
            'Name' => ['required', Rule::unique('m_module_category')->where(function ($query) use ($request, $erpID) {
                return $query->whereRaw('LOWER(Name) = ?', [strtolower($request->Name)])->where('ERPID', $erpID);
            })]
        ]);

        // Menyimpan kategori modul baru
        ModuleCategory::create([
            'ERPID' => $erpID,
            'Name' => Str::title($request->Name),
            'CreateUserID' => auth()->user()->UserID,
            'CreateDateTime' => Carbon::now('GMT+7'),
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7')
        ]);

        session()->flash('success', 'Kategori Berhasil Ditambahkan');
        return redirect()->route('addModuleCategory', compact('erp'));
    }

    // Menambahkan modul baru
    public function addModule($erp)
    {
        // Mengambil ID ERP berdasarkan inisialnya
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Mengambil kategori-kategori modul untuk ERP tersebut
        $categories = ModuleCategory::where('ERPID', $erpID)->get();

        return view('admin.erp.module.add_module', compact('erp', 'categories'));
    }

    // Menyimpan modul yang baru ditambahkan
    public function storeModule(Request $request, $erp)
    {
        // Mengambil ID ERP berdasarkan inisialnya
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Menyimpan data modul baru dan detail-detailnya di sequence
        Session::put('detailCount', count($request->input('sequence')));

        for ($i = 0; $i < Session::get('detailCount', 0); $i++) {
            Session::put('detail_' . $i, [
                'sequence' => $request->input('sequence.' . $i),
                'Description' => $request->input('Description.' . $i),
            ]);
        }

        // Validasi input 
        $validator = Validator::make(
            $request->all(),
            [
                'Name'    => ['required', 'max:50', Rule::unique('m_module')->where(function ($query) use ($request, $erpID) {
                    return $query->whereRaw('LOWER(Name) = ?', [strtolower($request->Name)])
                        ->where('ERPID', $erpID);
                })],
                'ModuleDesc' => 'required',
                'Category' => 'required',
                'sequence.*' => 'required',
                'Description.*' => 'required',
                'filePath.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ],
            [
                'Description.*.required' => 'Deskripsi perlu untuk diisi'
            ]
        );
        if ($validator->fails()) {
            // flash('error')->error();
            session()->flash('error', 'Update Gagal, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Insert modul
        $module = Module::create([
            'Name' => $request->Name,
            'Description' => $request->ModuleDesc,
            'CategoryID' => $request->Category,
            'ERPID' => $erpID,
            'CreateUserID' => auth()->user()->UserID,
            'CreateDateTime' => Carbon::now('GMT+7'),
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7')
        ]);

        // Insert detail modul
        $sequences = $request->sequence;
        $descriptions = $request->Description;

        foreach ($sequences as $index => $sequence) {
            $gambar = null;
            if ($request->file('filePath') and array_key_exists($index, $request->file('filePath'))) {
                $compressedImage = Image::make($request->filePath[$index])->encode('jpg', 75);
                $gambar = $request->filePath[$index]->store('public/gambar_sequence');
                Storage::put($gambar, (string) $compressedImage);
                $gambar = explode("gambar_sequence/", $gambar)[1];
            }
            DetailModule::create([
                'ModuleID' => $module->ModuleID,
                'Sequence' => $sequence,
                'Description' => $descriptions[$index],
                'FilePath' => $gambar,
            ]);
        }

        // Reset session variabel untuk next inputs.
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');
        session()->flash('success', 'Modul Berhasil Ditambahkan');
        return redirect()->route('masterModule', $erp);
    }

    // Mengedit modul
    public function editModule($erp, $id)
    {
        // Mengambil objek modul berdasarkan ID
        $module = Module::find($id);

        // Mengambil detail-detail modul
        $details = $module->details;

        // Mengambil kategori-kategori modul untuk ERP tersebut
        $categories = ModuleCategory::where('ERPID', $module->ERPID)->get();

        return view('admin.erp.module.edit_module', compact('module', 'erp', 'details', 'categories'));
    }

    // Mengupdate modul
    public function updateModule(Request $request, $erp, $id)
    {
        // Mengambil objek modul yang akan diperbarui
        $module = Module::find($id);

        // Mengambil ID ERP berdasarkan inisialnya
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Menyimpan jumlah detail modul dalam sesi
        Session::put('detailCount', count($request->input('sequence')));

        // Menyimpan setiap detail modul dalam sesi
        for ($i = 0; $i < Session::get('detailCount', 0); $i++) {
            Session::put('detail_' . $i, [
                'sequence' => $request->input('sequence.' . $i),
                'Description' => $request->input('Description.' . $i),
            ]);
        }

        // Validasi input yang diterima dari permintaan
        $validator = Validator::make(
            $request->all(),
            [
                'Name' => 'required|unique:m_module,Name,' . $id . ',ModuleID,ERPID,' . $erpID,
                'ModuleDesc' => 'required',
                'Category' => 'required',
                'sequence.*' => 'required',
                'Description.*' => 'required',
                'filePath.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ],
            [
                'Description.*.required' => 'Deskripsi perlu untuk diisi'
            ]
        );

        // Jika validasi gagal, kembalikan ke halaman sebelumnya dengan pesan kesalahan
        if ($validator->fails()) {
            session()->flash('error', 'Update Gagal, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Memperbarui informasi modul
        $module->update([
            'Name' => $request->Name,
            'Description' => $request->ModuleDesc,
            'CategoryID' => $request->Category,
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7')
        ]);

        // Memperbarui atau menghapus detail modul sesuai dengan permintaan
        if (count($request->sequence) <= count($module->details)) {
            for ($i = 0; $i < count($module->details); $i++) {
                $detail = $module->details[$i];
                if ($i < count($request->sequence)) {
                    if ($request->file('filePath') and array_key_exists($i, $request->file('filePath'))) {
                        $compressedImage = Image::make($request->filePath[$i])->encode('jpg', 75);
                        $gambar = $request->filePath[$i]->store('public/gambar_sequence');
                        Storage::put($gambar, (string) $compressedImage);
                        $gambar = explode("gambar_sequence/", $gambar)[1];
                    } else {
                        $gambar = DetailModule::where('ModuleDetailID', $request->detailsID[$i])->first()->FilePath;
                    }
                    // dd($gambar);
                    $detail->update([
                        'Sequence' => $request->sequence[$i],
                        'Description' => $request->Description[$i],
                        'FilePath' => $gambar,
                    ]);
                } else {
                    if (DetailModule::where('FilePath', $detail->FilePath)->where('ModuleDetailID', '!=', $detail->ModuleDetailID)->exists()) {
                        Storage::delete('public/gambar_sequence/' . $detail->FilePath);
                    }
                    $detail->delete();
                }
            }
        } else {
            for ($i = count($module->details); $i < count($request->sequence); $i++) {
                $gambar = null;
                if ($request->file('filePath') and array_key_exists($i, $request->file('filePath'))) {
                    $compressedImage = Image::make($request->filePath[$i])->encode('jpg', 75);
                    $gambar = $request->filePath[$i]->store('public/gambar_sequence');
                    Storage::put($gambar, (string) $compressedImage);
                    $gambar = explode("gambar_sequence/", $gambar)[1];
                }
                DetailModule::create([
                    'ModuleID' => $id,
                    'Sequence' => $request->sequence[$i],
                    'Description' => $request->Description[$i],
                    'FilePath' => $gambar,
                ]);
            }
        }

        // Membersihkan session detail modul
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');

        // Menampilkan pesan sukses dan mengarahkan kembali ke halaman daftar modul
        session()->flash('success', 'Modul ' . $module->Name . ' Berhasil Di-update');
        return redirect()->route('masterModule', $erp);
    }

    // Menghapus modul
    public function deleteModule($erp, $id)
    {
        // Menghapus modul dan detail-detailnya
        $module = Module::find($id);
        foreach ($module->details as $detail) {
            if ($detail->FilePath) {
                Storage::delete('public/gambar_sequence/' . $detail->FilePath);
            }
        }
        $module->delete();

        session()->flash('warning', 'Modul Berhasil Dihapus');
        return redirect()->route('masterModule', compact('erp'));
    }

    // Menghapus kategori modul
    public function deleteCategory($erp, $id)
    {
        // Menghapus kategori modul
        $module = ModuleCategory::find($id);
        try {
            $module->delete();
            session()->flash('warning', 'Kategori Berhasil Dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('danger', 'Kategori Tidak Bisa Dihapus. Masih Ada Modul Dengan Kategori ' . $module->Name . '.');
        }

        return redirect()->back();
    }

    // Mencari modul
    public function search(Request $request, $erp)
    {
        // Mencari modul berdasarkan nama
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');

        $modules = $erp->modules()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        // dd($modules->links());

        return view('admin.erp.module.index', compact(['modules', 'search', 'erp']));
    }
}
