@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">Change Password</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>
    @if (session('danger'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <form action="{{ route('pswChange', $id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="old_psw">Old Password</label>
                    <input type="password" class="form-control" id="old_psw" aria-describedby="old_psw"
                        placeholder="Enter Previous Password" name="old_psw">
                    @error('old_psw')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <label for="new_psw" class="form-label">New Password</label>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" id="new_psw" name="new_psw"
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
                @error('new_psw')
                    <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                @enderror

                <label for="new_psw_confirmation" class="form-label">Confirm New Password</label>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" id="new_psw_confirmation" name="new_psw_confirmation"
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
                @error('new_psw_confirmation')
                    <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                @enderror
                <button type="submit" class="btn btn-primary">Submit</button>

            </form>
        </div>
    </div>
@endsection
