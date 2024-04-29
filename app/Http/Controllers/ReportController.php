<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ERP;
use App\Models\Report;
use Illuminate\Support\Str;
use App\Models\DetailReport;
use Illuminate\Http\Request;
use App\Models\ReportCategory;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    // Menampilkan halaman master report
    public function masterReport($erp)
    {
        // Ambil objek ERP berdasarkan inisial
        $erp = ERP::where('Initials', $erp)->first();

        // Ambil daftar laporan terkait ERP tersebut
        $reports = $erp->reports()->orderByRaw('LOWER(Name)')->paginate(10);

        // Jika pengguna adalah User, tampilkan tampilan sesuai dengan peran User
        $erp = $erp->Initials;
        if (auth()->user()->Role == 'User') {
            return view('erp.report.index', compact('reports', 'erp'));
        }

        // Bersihkan session detail jika ada
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');
        // Tampilkan tampilan sesuai dengan peran Admin
        return view('admin.erp.report.index', compact('reports', 'erp'));
    }

    // Menampilkan halaman detail report
    public function detailReport($erp, $id)
    {
        // Temukan laporan berdasarkan ID
        $report = Report::find($id);

        // Ambil detail laporan terkait
        $details = $report->details;

        // Tampilkan halaman detail report
        return view('erp.report.detail_report', compact('report', 'erp', 'details'));
    }

    // Menampilkan halaman tambah kategori report
    public function addCategory($erp)
    {
        // Temukan ID ERP berdasarkan inisial
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Ambil daftar kategori laporan terkait ERP tersebut
        $categories = ReportCategory::where('ERPID', $erpID)->paginate(10);

        // Tampilkan halaman tambah kategori report
        return view('admin.erp.report.add_category', compact('erp', 'categories'));
    }

    // Menyimpan kategori report baru
    public function storeCategory(Request $request, $erp)
    {
        // Temukan ID ERP berdasarkan inisial
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Validasi data input
        $request->validate([
            'Name' => ['required', Rule::unique('m_report_category')->where(function ($query) use ($request, $erpID) {
                return $query->whereRaw('LOWER(Name) = ?', [strtolower($request->Name)])->where('ERPID', $erpID);
            })]
        ]);

        // Simpan kategori report baru
        ReportCategory::create([
            'ERPID' => $erpID,
            'Name' => Str::title($request->Name),
            'CreateUserID' => auth()->user()->UserID,
            'CreateDateTime' => Carbon::now('GMT+7'),
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7')
        ]);

        // Tampilkan pesan sukses dan arahkan kembali ke halaman tambah kategori report
        session()->flash('success', 'Kategori Berhasil Ditambahkan');
        return redirect()->route('addReportCategory', compact('erp'));
    }

    // Menampilkan halaman tambah report
    public function addReport($erp)
    {
        // Temukan ID ERP berdasarkan inisial
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Ambil daftar kategori laporan terkait ERP tersebut
        $categories = ReportCategory::where('ERPID', $erpID)->get();

        // Tampilkan halaman tambah report
        return view('admin.erp.report.add_report', compact('erp', 'categories'));
    }

    // Menyimpan report baru
    public function storeReport(Request $request, $erp)
    {
        // Validasi data input
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        $validatedData = Validator::make(
            $request->all(),
            [
                'Name'    => ['required', 'max:50', Rule::unique('m_report')->where(function ($query) use ($request, $erpID) {
                    return $query->whereRaw('LOWER(Name) = ?', [strtolower($request->Name)])
                        ->where('ERPID', $erpID);
                })],
                'ReportDesc' => 'required',
                'Category' => 'required',
                'sequence.*' => 'required|numeric',
                'Description.*' => 'required',
                'filePath.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ],
            [
                'Description.*.required' => 'Deskripsi perlu untuk diisi'
            ]
        );

        // Menyimpan konten dari beberapa field dalam variabel session
        Session::put('detailCount', count($request->input('sequence')));
        for ($i = 0; $i < Session::get('detailCount', 0); $i++) {
            Session::put('detail_' . $i, [
                'sequence' => $request->input('sequence.' . $i),
                'Description' => $request->input('Description.' . $i),
                // Menambahkan nilai field lainnya yang akan disimpan dalam session
            ]);
        }

        // Jika validasi gagal, kembalikan dengan pesan error
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        // Buat token untuk pemanggilan report dari luar SUDOCU untuk melihat report.
        // Token butuh nama ERP, Nama Kategori, dan Nama Report. 
        $category = ReportCategory::find($request->Category);
        $token = 'Report' . $erp . $category->Name . $request->Name;

        // Membuat dan menyimpan report baru
        $report = Report::create([
            'Name' => $request->Name,
            'Description' => $request->ReportDesc,
            'CategoryID' => $request->Category,
            'ERPID' => $erpID,
            'CreateUserID' => auth()->user()->UserID,
            'CreateDateTime' => Carbon::now('GMT+7'),
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7'),
            'token_report' => hash('sha256', $token), // Token untuk panggilan dari luar SUDOCU untuk melihat report.
        ]);

        // Menyimpan detail report
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
            DetailReport::create([
                'ReportID' => $report->ReportID,
                'Sequence' => $sequence,
                'Description' => $descriptions[$index],
                'FilePath' => $gambar,
            ]);
        }

        // Bersihkan session detail
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');

        // Tampilkan pesan sukses dan arahkan kembali ke halaman master report
        session()->flash('success', 'Report Berhasil Ditambahkan');
        return redirect()->route('masterReport', $erp);
    }

    // Menampilkan halaman edit report
    public function editReport($erp, $id)
    {
        // Temukan report berdasarkan ID
        $report = Report::find($id);

        // Ambil detail report terkait
        $details = $report->details;

        // Ambil daftar kategori report terkait ERP tersebut
        $categories = ReportCategory::where('ERPID', $report->ERPID)->get();

        // Tampilkan halaman edit report
        return view('admin.erp.report.edit_report', compact('report', 'erp', 'details', 'categories'));
    }

    // Memperbarui report
    public function updateReport(Request $request, $erp, $id)
    {
        // Validasi data input
        $report = Report::find($id);
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;

        // Masukkan session variable baru
        Session::put('detailCount', count($request->input('sequence')));
        for ($i = 0; $i < Session::get('detailCount', 0); $i++) {
            Session::put('detail_' . $i, [
                'sequence' => $request->input('sequence.' . $i),
                'Description' => $request->input('Description.' . $i),
            ]);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'Name' => 'required|unique:m_report,Name,' . $id . ',ReportID,ERPID,' . $erpID,
                'ReportDesc' => 'required',
                'Category' => 'required',
                'sequence.*' => 'required',
                'Description.*' => 'required',
                'filePath.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ],
            [
                'Description.*.required' => 'Deskripsi perlu untuk diisi'
            ]
        );

        // Jika validasi gagal, kembalikan dengan pesan error
        if ($validator->fails()) {
            session()->flash('error', 'Update Gagal, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // Buat token untuk pemanggilan report dari luar SUDOCU untuk melihat report.
        // Token butuh nama ERP, Nama Kategori, dan Nama Report. 
        $category = ReportCategory::find($request->Category);
        $token = 'Report' . $erp . $category->Name . $request->Name;
        // Update report
        $report->update([
            'Name' => $request->Name,
            'Description' => $request->ReportDesc,
            'CategoryID' => $request->Category,
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7'),
            'token_report' => hash('sha256', $token)
        ]);

        // Memperbarui atau menghapus detail report
        // Logic untuk memperbarui atau menghapus detail report
        if (count($request->sequence) <= count($report->details)) {
            for ($i = 0; $i < count($report->details); $i++) {
                $detail = $report->details[$i];
                if ($i < count($request->sequence)) {
                    if ($request->file('filePath') and array_key_exists($i, $request->file('filePath'))) {
                        $compressedImage = Image::make($request->filePath[$i])->encode('jpg', 75);
                        $gambar = $request->filePath[$i]->store('public/gambar_sequence');
                        Storage::put($gambar, (string) $compressedImage);
                        $gambar = explode("gambar_sequence/", $gambar)[1];
                    } else {
                        $gambar = DetailReport::where('ReportDetailID', $request->detailsID[$i])->first()->FilePath;
                    }
                    $detail->update([
                        'Sequence' => $request->sequence[$i],
                        'Description' => $request->Description[$i],
                        'FilePath' => $gambar,
                    ]);
                } else {
                    if (!DetailReport::where('FilePath', $detail->FilePath)->where('ReportDetailID', '!=', $detail->ReportDetailID)->exists()) {
                        Storage::delete('public/gambar_sequence/' . $detail->FilePath);
                    }
                    $detail->delete();
                }
            }
        } else {
            for ($i = count($report->details); $i < count($request->sequence); $i++) {
                $gambar = null;
                if ($request->file('filePath') and array_key_exists($i, $request->file('filePath'))) {
                    $compressedImage = Image::make($request->filePath[$i])->encode('jpg', 75);
                    $gambar = $request->filePath[$i]->store('public/gambar_sequence');
                    Storage::put($gambar, (string) $compressedImage);
                    $gambar = explode("gambar_sequence/", $gambar)[1];
                }
                DetailReport::create([
                    'ReportID' => $id,
                    'Sequence' => $request->sequence[$i],
                    'Description' => $request->Description[$i],
                    'FilePath' => $gambar,
                ]);
            }
        }

        // Bersihkan session detail
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');

        // Tampilkan pesan sukses dan arahkan kembali ke halaman master report
        session()->flash('success', 'Report ' . $report->Name . ' Berhasil Di-update');
        return redirect()->route('masterReport', $erp);
    }

    // Menghapus report
    public function deleteReport($erp, $id)
    {
        // Temukan report berdasarkan ID
        $report = Report::find($id);

        // Hapus detail report terkait
        foreach ($report->details as $detail) {
            if ($detail->FilePath) {
                Storage::delete('public/gambar_sequence/' . $detail->FilePath);
            }
        }

        // Hapus report
        $report->delete();

        // Tampilkan pesan sukses dan arahkan kembali ke halaman master report
        session()->flash('warning', 'Report Berhasil Dihapus');
        return redirect()->route('masterReport', compact('erp'));
    }

    // Menghapus kategori report
    public function deleteCategory($erp, $id)
    {
        // Temukan kategori report berdasarkan ID
        $report = ReportCategory::find($id);

        // Hapus kategori report
        try {
            $report->delete();
            session()->flash('warning', 'Kategori Berhasil Dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('danger', 'Kategori Tidak Bisa Dihapus. Masih Ada Report Dengan Kategori ' . $report->Name . '.');
        }

        // Kembali ke halaman sebelumnya
        return redirect()->back();
    }

    // Melakukan pencarian report
    public function search(Request $request, $erp)
    {
        // Temukan objek ERP berdasarkan inisial
        $erp = ERP::where('Initials', $erp)->first();

        // Ambil kata kunci pencarian
        $search = $request->input('search');

        // Cari laporan berdasarkan kata kunci
        $reports = $erp->reports()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;

        // Tampilkan hasil pencarian
        return view('admin.erp.report.index', compact(['reports', 'search', 'erp']));
    }

    // Mendapatkan data report melalui API menggunakan token yang dimaksud.
    public function apiData($erp, $token)
    {
        $erpid = ERP::where('Initials', $erp)->first()->ERPID;
        // Temukan report berdasarkan nama dan kategori
        $report = Report::where('token_report', $token)->first();

        // Jika report tidak ditemukan, kembalikan ke halaman sebelumnya dengan pesan
        if (!$report) {
            session()->flash('alert', 'Report Tidak Ditemukan.');
            return redirect()->back()->with('alert', 'Report Tidak Ditemukan!');
        }

        // Ambil detail report
        $details = $report->details;

        // Tampilkan halaman detail report
        return view('erp.report.detail_report_guest', compact('report', 'erp', 'details'));
    }
}
