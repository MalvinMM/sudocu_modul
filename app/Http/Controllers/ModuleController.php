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
use Intervention\Image\ImageManager;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class ModuleController extends Controller
{
    public function masterModule($erp)
    {
        $obj = ERP::where('Initials', $erp)->first();
        $modules = $obj->modules()->paginate(10);
        if (auth()->user()->Role == 'User') {
            return view('erp.module.index', compact('modules', 'erp'));
        }
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');
        return view('admin.erp.module.index', compact('modules', 'erp'));
    }


    public function detailModule($erp, $id)
    {
        $module = Module::find($id);
        $details = $module->details;

        return view('erp.module.detail_module', compact('module', 'erp', 'details'));
    }
    public function addCategory($erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        $categories = ModuleCategory::where('ERPID', $erpID)->paginate(10);
        return view('admin.erp.module.add_category', compact('erp', 'categories'));
    }

    public function storeCategory(Request $request, $erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        $request->validate([
            'Name' => ['required', Rule::unique('m_module_category')->where(function ($query) use ($request, $erpID) {
                return $query->whereRaw('LOWER(Name) = ?', [strtolower($request->Name)])->where('ERPID', $erpID);
            })]
        ]);

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


    public function addModule($erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        $categories = ModuleCategory::where('ERPID', $erpID)->get();
        return view('admin.erp.module.add_module', compact('erp', 'categories'));
    }

    public function storeModule(Request $request, $erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        // dd($request->all());
        Session::put('detailCount', count($request->input('sequence')));
        // Store the contents of these fields in session variables
        for ($i = 0; $i < Session::get('detailCount', 0); $i++) {
            Session::put('detail_' . $i, [
                'sequence' => $request->input('sequence.' . $i),
                'Description' => $request->input('Description.' . $i),
            ]);
        }
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
        // dd($request->file('filePath'));

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

        $sequences = $request->sequence;
        $descriptions = $request->Description;
        // dd($descriptions);
        foreach ($sequences as $index => $sequence) {
            $gambar = null;
            if ($request->file('filePath') and array_key_exists($index, $request->file('filePath'))) {

                $compressedImage = Image::make($request->filePath[$index])->encode('jpg', 75);
                $gambar = $request->filePath[$index]->store('public/gambar_sequence');
                Storage::put($gambar, (string) $compressedImage);
                $gambar = explode("gambar_sequence/", $gambar)[1];

                // FAILED ATTEMPT
                // $gambar = Str::random(35) . '.' . $request->filePath[$index]->extension();
                // $request->filePath[$index]->storeAs('public/original', $gambar);

                // $compressedImage = Image::make($request->filePath[$index])->encode('jpg', 75);
                // $compressedImage->storeAs('public/gambar_sequence');

                // Storage::put($path, (string) $compressedImage);

                // $optimizerChain = OptimizerChainFactory::create();
                // $optimizerChain->optimize('storage/original/' . $gambar, 'storage/gambar_sequence/' . $gambar);
                // unlink('storage/original/' . $gambar);
                // Storage::delete('public/original/' . $gambar);
            }
            DetailModule::create([
                'ModuleID' => $module->ModuleID,
                'Sequence' => $sequence,
                'Description' => $descriptions[$index],
                'FilePath' => $gambar,
            ]);
        }
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');
        session()->flash('success', 'Modul Berhasil Ditambahkan');
        return redirect()->route('masterModule', $erp);
    }

    public function editModule($erp, $id)
    {
        $module = Module::find($id);
        $details = $module->details;
        $categories = ModuleCategory::where('ERPID', $module->ERPID)->get();

        return view('admin.erp.module.edit_module', compact('module', 'erp', 'details', 'categories'));
    }

    public function updateModule(Request $request, $erp, $id)
    {
        // dd($request->all());
        $module = Module::find($id);
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        Session::put('detailCount', count($request->input('sequence')));
        // dd(session('detailCount'));
        for ($i = 0; $i < Session::get('detailCount', 0); $i++) {
            // dd(str_replace("\r\n", "/\n", $request->input('Description.' . $i)));
            Session::put('detail_' . $i, [
                'sequence' => $request->input('sequence.' . $i),
                'Description' => $request->input('Description.' . $i),
            ]);
        }
        // dd($request->all());
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
        if ($validator->fails()) {
            // flash('error')->error();
            session()->flash('error', 'Update Gagal, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $module->update([
            'Name' => $request->Name,
            'Description' => $request->ModuleDesc,
            'CategoryID' => $request->Category,
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7')
        ]);

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
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');
        session()->flash('success', 'Modul ' . $module->Name . ' Berhasil Di-update');
        return redirect()->route('masterModule', $erp);
    }

    public function deleteModule($erp, $id)
    {
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

    public function deleteCategory($erp, $id)
    {
        $module = ModuleCategory::find($id);
        try {
            $module->delete();
            session()->flash('warning', 'Kategori Berhasil Dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('danger', 'Kategori Tidak Bisa Dihapus. Masih Ada Modul Dengan Kategori ' . $module->Name . '.');
        }

        return redirect()->back();
    }

    public function search(Request $request, $erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');

        $modules = $erp->modules()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        // dd($modules->links());

        return view('admin.erp.module.index', compact(['modules', 'search', 'erp']));
    }
}
