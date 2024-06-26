<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ERP;
use App\Models\User;
use App\Models\UserERP;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // public function landing()
    // {
    //     return view('welcome');
    // }

    public function dashboard() // Menampilkan dashboard (admin maupun user)
    {
        // ambil 5 user (alphabetical order) untuk ditampilkan di dashboard.
        $users = User::where('isActive', 1)->orderByRaw('LOWER(UserName)')->take(5)->get();
        $erps = ERP::all();

        for ($i = 0; $i < session()->get('detailCount'); $i++) {
            session()->forget('detail_' . $i);
        }

        session()->forget('detailCount'); // session variabel untuk keperluan mmodul & report. Reset variabel.

        if (Auth::user()->Role == 'Admin') {
            return view('admin.dashboard', compact(['users', 'erps']));
        } else {
            return view('dashboard', compact(['users', 'erps']));
        }
    }

    public function register() // Show add user page
    {
        $erps = ERP::all();

        $roles = collect(['User', 'PIC', 'Admin']); // Untuk keperluan dropdown

        return view("admin.register", compact(['roles', 'erps']));
    }

    public function store(Request $request) // Store user
    {
        // Validasi request
        $validator = Validator::make(
            $request->all(),
            [
                'FullName'    => 'required|max:100',
                'NIK' => 'required|digits between:2,10|unique:m_user',
                'UserName' => 'required|max:20|unique:m_user',
                'password' => ['required', Password::min(8)->mixedCase()->numbers()],
                'confPassword' => ['required', Password::min(8)->mixedCase()->numbers()],
                'Role' => 'required',
            ],
            [
                'NIK.unique' => 'NIK ini sudah memiliki akun',
            ]
        );
        if ($validator->fails()) {
            // flash('error')->error();
            session()->flash('error', 'Input User Gagal.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->confPassword !== $request->password) {
            session()->flash('error', 'Pasword Tidak Sama');
            return redirect()->back()->withInput();
        }
        // dd($request->Role);

        // Insert user ke DB.
        $user = User::create([
            'FullName' => $request->FullName,
            'NIK' => $request->NIK,
            'UserName' => $request->UserName,
            'password' => Hash::make($request->password),
            'Role' => $request->Role,
            'CreateUserID' => auth()->user()->UserID,
            'CreateDateTime' => Carbon::now('GMT+7'),
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7')
        ]);

        // Untuk user & PIC. setiap ERP yang dipilih, insert ke tabel relasi di DB.
        if ($request->erps) {
            $data = [];
            foreach ($request->erps as $task) {
                $isi = [
                    'UserID' => $user->UserID,
                    'ERPID' => (int) $task,
                    'CreateUserID' => auth()->user()->UserID,
                    'CreateDateTime' => Carbon::now('GMT+7'),
                    'UpdateUserID' => auth()->user()->UserID,
                    'UpdateDateTime' => Carbon::now('GMT+7')
                ];
                array_push($data, $isi);
            }
            $user->erps()->attach($data);
            // UserERP::insert($data);
        }
        session()->flash('success', 'User Berhasil Ditambahkan.');
        return redirect()->route('userList');
    }

    public function editUser($id) // Show edit page
    {

        $user = User::find($id);
        $erps = ERP::all();
        $roles = collect(['User', 'PIC', 'Admin']); // Untuk keperluan dropdown

        return view('admin.edit_user', compact(['user', 'roles', 'erps']));
    }

    public function updateUser(Request $request, $id) // Update user
    {
        // validasi request (NIK unik, username unik)
        $validator = Validator::make(
            $request->all(),
            [
                'FullName'    => 'required|max:100',
                'NIK' => ['required', 'digits between:2,10', Rule::unique('m_user')->ignore($id, 'UserID')],
                'UserName' => ['required', 'max:20', Rule::unique('m_user')->ignore($id, 'UserID')],
                'Role' => 'required',
                'isActive' => 'required',
                // 'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            ],
            [
                'NIK.unique' => 'NIK ini sudah memiliki akun',
            ]
        );

        if ($validator->fails()) {
            // flash('error')->error();
            session()->flash('error', 'Update Gagal.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cek syarat password
        $user = User::find($id);
        if (!$request->password) {
            $password = $user->password;
        } else {
            $request->validate([
                'password' => [Password::min(8)->mixedCase()->numbers()],
                'confPassword' => ['required', Password::min(8)->mixedCase()->numbers()]
            ]);
            if ($request->confPassword == $request->password) {
                $password = Hash::make($request->password);
            } else {
                return redirect()->back()->withErrors([
                    'diffPass' => 'password tidak sama'
                ]);
            }
        }

        // Update user (dengan password yang sudah lolos syarat)
        $user->update([
            'FullName' => $request->FullName,
            'NIK' => $request->NIK,
            'UserName' => $request->UserName,
            'password' => $password,
            'Role' => $request->Role,
            'isActive' => $request->isActive,
            'UpdateUserID' => auth()->user()->UserID,
            'UpdateDateTime' => Carbon::now('GMT+7')
        ]);

        // Untuk user & PIC. setiap ERP yang dipilih, update ke tabel relasi di DB.
        if ($request->erps) {
            $user->erps()->detach(); // Detach semua relasi ERP untuk user ini.
            $data = [];
            foreach ($request->erps as $task) {
                $isi = [
                    'UserID' => $user->UserID,
                    'ERPID' => (int) $task,
                    'CreateUserID' => auth()->user()->UserID,
                    'CreateDateTime' => Carbon::now('GMT+7'),
                    'UpdateUserID' => auth()->user()->UserID,
                    'UpdateDateTime' => Carbon::now('GMT+7')
                ];
                array_push($data, $isi);
            }
            $user->erps()->attach($data); // Attach relasi ERP baru.
        }

        session()->flash('success', 'User ' . $user->FulName . ' Telah Diupdate.');
        return redirect()->route('userList');
    }

    public function deactivateUser($id) // Update kolom isActive user
    {
        $obj = User::find($id);
        $obj->update([
            'isActive' => false
        ]);
        session()->flash('warning', $obj->FullName . ' Berhasil Di-nonaktifkan.');

        return redirect()->back();
    }

    public function activateUser($id) // Update kolom isActive user
    {
        $obj = User::find($id);
        $obj->update([
            'isActive' => true
        ]);
        session()->flash('success', $obj->FullName . ' Berhasil Diaktifkan.');

        return redirect()->back();
    }

    public function index() // Show login page
    {
        return view('login');
    }

    public function login(Request $request) // Login procedure
    {
        // Validasi input
        $validator = Validator::make(
            $request->all(),
            [
                'UserName'    => 'required|max:100',
                'password' => 'required'
            ]
        );
        if ($validator->fails()) {
            // flash('error')->error();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('UserName', 'password');

        // Login attempt
        if (Auth::attempt($credentials) and auth()->user()->isActive == true) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        } elseif (Auth::attempt($credentials) and auth()->user()->isActive == false) {
            return redirect()->back()->withErrors([
                'Login' => 'Login gagal, status akun tidak aktif. Silahkan hubungi admin'
            ]);
        } else {
            return redirect()->back()->withErrors([
                'Login' => 'Login gagal, username atau password salah'
            ]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('landing');
    }

    public function pswForm($id)
    {
        return view('psw_change', compact('id'));
    }

    public function pswChange(Request $request, $id) // Prosedur ubah password
    {
        // dd(Hash::check('user1', auth()->user()->password));

        $user = Auth::user();

        $validator = Validator::make(
            $request->all(),
            [
                //check password sama atau tidak dengan password yang sekarang
                'old_psw' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Password lama tidak sesuai.');
                    }
                }],
                'new_psw' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'password' => Hash::make($request->new_psw),
            'updated_at' => Carbon::now('GMT+7'),
            'updated_by' => $user->UserID,
        ]);

        session()->flash('success_psw', 'Password telah berhasil diubah.');
        return redirect()->route('dashboard');
    }

    public function userList() // Show user page
    {
        $users = User::where('isActive', true)->orderBy('isActive', 'desc')->paginate(10);
        session()->forget('search');
        Session::put('status', 'active');
        $status = 'active';
        // dd($users);

        return view('admin.user_list', compact('users', 'status'));
    }

    public function search(Request $request) // Cari user
    {
        $search = $request->input('search');

        $status = session('status'); // Cek status yang difilter

        $users = User::orderBy('isActive', 'desc');

        if ($status === 'active') {
            $users = User::where('isActive', true);
        } elseif ($status === 'inactive') {
            $users = User::where('isActive', false);
        }

        $users = $users->where('FullName', 'like', '%' . $search . '%')->paginate(10)->appends(['search' => $search]);

        session(['search' => $search]);

        return view('admin.user_list', compact(['users', 'status', 'search']));
    }

    public function filter(Request $request) // Filter status aktif (get/post)
    {
        $status = $request->input('status');

        $search = session('search'); // Cek keyword yang dicari

        $users = User::orderBy('isActive', 'desc');

        if (!empty($search)) {
            $users->where('FullName', 'like', '%' . $search . '%');
        }

        if ($status === 'active') {
            $users->where('isActive', true);
        } elseif ($status === 'inactive') {
            $users->where('isActive', false);
        }

        $users = $users->paginate(10);

        session(['status' => $status]);

        if ($request->isMethod('post')) {
            $users->appends(['status' => $status]);
        }

        return view('admin.user_list', compact('users', 'status'));
    }
}







// --------------------------------- DUMP ---------------------------------
// Change password.
// $validator = Validator::make(
        //     $request->all(),
        //     [
        //         'old_psw'    => 'required',
        //         'new_psw' => ['required', Password::min(8)->mixedCase()->numbers()],
        //         'new_psw2' =>  ['required', Password::min(8)->mixedCase()->numbers()],
        //     ]
        // );

        // if ($validator->fails()) {
        //     // flash('error')->error();
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        // $user = Auth::user();
        // if (Hash::check($request->old_psw, $user->password)) {
        //     if ($request->new_psw == $request->new_psw2) {
        //         $user->update([
        //             $user->password = Hash::make($request->new_psw),
        //             $user->UpdateDateTime = Carbon::now('GMT+7'),
        //             $user->UpdateUserID = $user->UserID,
        //         ]);
        //         session()->flash('success_psw', 'Password telah berhasil diubah.');
        //         return redirect()->route('dashboard');
        //     } else {
        //         session()->flash('danger', 'Pengubahan Password Gagal.');
        //         return redirect()->back()->withErrors(['new_psw', 'Password baru tidak sama']);
        //     }
        // } else {
        //     session()->flash('danger', 'Pengubahan Password Gagal.');
        //     return redirect()->back()->withErrors(['old_psw' => 'Password lama tidak sesuai']);
        // }