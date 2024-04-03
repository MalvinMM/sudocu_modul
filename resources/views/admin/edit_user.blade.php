@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">Edit User {{ $user->FullName }}</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="card">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('updateUser', $user->UserID) }}" method="POST">
                @method('put')
                @csrf
                <div class="mb-3">
                    <label for="namaLengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="namaLengkap" name="FullName"
                        value="{{ old('FullName', $user->FullName ?? '') }}">
                    @error('FullName')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" class="form-control" id="nik" name="NIK"
                        value="{{ old('NIK', $user->NIK ?? '') }}"">
                    @error('NIK')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="userName" class="form-label">Username Akun</label>
                    <input type="text" class="form-control" id="userName" name="UserName"
                        value="{{ old('UserName', $user->UserName ?? '') }}"">
                    @error('UserName')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>

                <label for="password" class="form-label">New Password</label>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Enter New Password">
                    <a href="#" class="input-group-text text-decoration-none" data-bs-toggle="collapse"
                        data-bs-target="#passwordRequirements" aria-expanded="false" aria-controls="passwordRequirements"
                        style="font-size: 1.5rem;">
                        <i class="ti ti-help"></i>
                    </a>
                </div>
                <div class="collapse mb-3" id="passwordRequirements">
                    <div class="card card-body">
                        Password harus memiliki kriteria di bawah ini: <br>
                        - Minimal 8 karakter <br>
                        - Memiliki minimal 1 huruf kapital <br>
                        - Memiliki minimal 1 angka <br>
                    </div>
                </div>
                @error('password')
                    <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                @enderror

                <label for="confPassword" class="form-label">Confirm New Password</label>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" id="confPassword" name="confPassword"
                        placeholder="Confirm New Password">
                    <a href="#" class="input-group-text text-decoration-none" data-bs-toggle="collapse"
                        data-bs-target="#passwordRequirements2" aria-expanded="false" aria-controls="passwordRequirements2"
                        style="font-size: 1.5rem;">
                        <i class="ti ti-help"></i>
                    </a>
                </div>
                <div class="collapse mb-3" id="passwordRequirements2">
                    <div class="card card-body">
                        Password harus memiliki kriteria di bawah ini: <br>
                        - Minimal 8 karakter <br>
                        - Memiliki minimal 1 huruf kapital <br>
                        - Memiliki minimal 1 angka <br>
                    </div>
                </div>
                @error('confPassword')
                    <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                @enderror

                <div class="mb-3">
                    <label for="isActive" class="form-label">Status</label>
                    <select name="isActive" id="isActive" class="form-select" style="width:20%">
                        <option value="1" @if (old('isActive', $user->isActive) == 1) selected @endif>Active</option>
                        <option value="0" @if (old('isActive', $user->isActive) == 0) selected @endif>Inactive</option>
                    </select>
                    @error('isActive')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="Role" id="role" class="form-select" style="width:20%">
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @if (old('Role', $user->Role) == $role) selected @endif>
                                {{ $role }}</option>
                        @endforeach
                    </select>
                    @error('Role')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>

                <div id="picInput" style="display: none;">
                    <label class="form-label">ERP</label><br />
                    <div class="mb-3">
                        <div class="form-check">
                            @foreach ($erps as $erp)
                                <input class="form-check-input" type="checkbox" name="erps[]"
                                    value="{{ $erp->ERPID }}" @if ($user->erps->contains('ERPID', $erp->ERPID)) checked @endif>
                                {{ $erp->Initials }} <br />
                            @endforeach
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var roleSelect = document.getElementById('role');
            var picInput = document.getElementById('picInput');

            // Function to show/hide the pic input based on the role selection
            function togglePicInput() {
                if (roleSelect.value === 'PIC' || roleSelect.value === 'User') {
                    picInput.style.display = 'block';
                } else {
                    picInput.style.display = 'none';
                }
            }

            // Call the function immediately after defining it
            togglePicInput();

            // Listen for the change event of the role dropdown
            roleSelect.addEventListener('change', togglePicInput);
        });
    </script>
@endsection
