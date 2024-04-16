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
    public function masterReport($erp)
    {
        $obj = ERP::where('Initials', $erp)->first();
        $reports = $obj->reports()->paginate(10);
        if (auth()->user()->Role == 'User') {
            return view('erp.report.index', compact('reports', 'erp'));
        }
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');
        return view('admin.erp.report.index', compact('reports', 'erp'));
    }

    public function detailReport($erp, $id)
    {
        $report = Report::find($id);
        $details = $report->details;

        return view('erp.report.detail_report', compact('report', 'erp', 'details'));
    }

    public function addCategory($erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        $categories = ReportCategory::where('ERPID', $erpID)->paginate(10);
        return view('admin.erp.report.add_category', compact('erp', 'categories'));
    }

    public function storeCategory(Request $request, $erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        $request->validate([
            'Name' => ['required', Rule::unique('m_report_category')->where(function ($query) use ($request, $erpID) {
                return $query->whereRaw('LOWER(Name) = ?', [strtolower($request->Name)])->where('ERPID', $erpID);
            })]
        ]);
        $token = 'Report' . $erp . $request->Name;

        ReportCategory::create([
            'ERPID' => $erpID,
            'Name' => Str::title($request->Name),
            'token' => hash('sha256', $token),
            'CreateUserID' => auth()->user()->UserID,
            'CreateDateTime' => Carbon::now('GMT+7'),
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7')
        ]);
        session()->flash('success', 'Kategori Berhasil Ditambahkan');
        return redirect()->route('addReportCategory', compact('erp'));
    }

    public function addReport($erp)
    {
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        $categories = ReportCategory::where('ERPID', $erpID)->get();
        // dd($categories);
        // for ($i = 0; $i < session()->get('detailCount'); $i++) {
        //     session()->forget('detail_' . $i);
        // }
        // session()->forget('detailCount');
        return view('admin.erp.report.add_report', compact('erp', 'categories'));
    }

    public function storeReport(Request $request, $erp)
    {
        // dd($request->all());
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

        Session::put('detailCount', count($request->input('sequence')));

        // Store the contents of these fields in session variables
        for ($i = 0; $i < Session::get('detailCount', 0); $i++) {
            Session::put('detail_' . $i, [
                'sequence' => $request->input('sequence.' . $i),
                'Description' => $request->input('Description.' . $i),
                // Add other field values to store in session
            ]);
        }
        if ($validatedData->fails()) {
            // flash('error')->error();
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        $report = Report::create([
            'Name' => $request->Name,
            'Description' => $request->ReportDesc,
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
            }
            DetailReport::create([
                'ReportID' => $report->ReportID,
                'Sequence' => $sequence,
                'Description' => $descriptions[$index],
                'FilePath' => $gambar,
            ]);
        }
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');
        session()->flash('success', 'Report Berhasil Ditambahkan');
        return redirect()->route('masterReport', $erp);
    }

    public function editReport($erp, $id)
    {
        $report = Report::find($id);
        $details = $report->details;
        $categories = ReportCategory::where('ERPID', $report->ERPID)->get();

        return view('admin.erp.report.edit_report', compact('report', 'erp', 'details', 'categories'));
    }

    public function updateReport(Request $request, $erp, $id)
    {
        // dd($request->all());
        $report = Report::find($id);
        $erpID = ERP::where('Initials', $erp)->first()->ERPID;
        Session::put('detailCount', count($request->input('sequence')));

        for ($i = 0; $i < Session::get('detailCount', 0); $i++) {
            // dd(str_replace("\r\n", "/\n", $request->input('Description.' . $i)));
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
        if ($validator->fails()) {
            // flash('error')->error();
            session()->flash('error', 'Update Gagal, Periksa Kembali Form.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $report->update([
            'Name' => $request->Name,
            'Description' => $request->ReportDesc,
            'CategoryID' => $request->Category,
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7')
        ]);
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
        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }
        session()->forget('detailCount');
        session()->flash('success', 'Report ' . $report->Name . ' Berhasil Di-update');
        return redirect()->route('masterReport', $erp);
    }

    public function deleteReport($erp, $id)
    {
        $report = Report::find($id);
        foreach ($report->details as $detail) {
            if ($detail->FilePath) {
                Storage::delete('public/gambar_sequence/' . $detail->FilePath);
            }
        }
        $report->delete();

        session()->flash('warning', 'Report Berhasil Dihapus');
        return redirect()->route('masterReport', compact('erp'));
    }

    public function deleteCategory($erp, $id)
    {
        $report = ReportCategory::find($id);
        try {
            $report->delete();
            session()->flash('warning', 'Kategori Berhasil Dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('danger', 'Kategori Tidak Bisa Dihapus. Masih Ada Report Dengan Kategori ' . $report->Name . '.');
        }
        return redirect()->back();
    }

    public function search(Request $request, $erp)
    {
        $erp = ERP::where('Initials', $erp)->first();
        $search = $request->input('search');

        $reports = $erp->reports()->where('Name', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);
        $erp = $erp->Initials;
        // dd($reports->links());

        return view('admin.erp.report.index', compact(['reports', 'search', 'erp']));
    }

    public function apiData($erp, $token, $name, $category)
    {
        $categoryObj = ReportCategory::where('token', $token)->first();
        if (!$categoryObj) {
            return redirect()->back()->with('alert', 'Report Tidak Ditemukan!');
        }
        $report = Report::where('Name', $name)->where('ERPID', $categoryObj->ERP->ERPID)->where('CategoryID', $categoryObj->CategoryID)->first();
        if (!$report) {
            return redirect()->back()->with('alert', 'Report Tidak Ditemukan!');
        }
        $details = $report->details;
        // $something = 'Report' . $report->ERPID . $report->Name . $report->CategoryID;
        // dd($something);

        return view('erp.report.detail_report_guest', compact('report', 'erp', 'details'));
    }
}
