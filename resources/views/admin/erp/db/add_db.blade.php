@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">Add Database</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>
    <div class="card">
        <div class="card-body">
            @if (session('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('danger') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('storeDB', $erp) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="namaDB" class="form-label">Nama Database</label>
                    <input type="text" class="form-control" id="namaDB" name="DbName" value="{{ old('DbName') }}">
                    @error('DbName')
                        <h6 class="form-helper"style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="DbServerLoc" class="form-label">Lokasi Database</label>
                    <input type="text" class="form-control" id="DbServerLoc" name="DbServerLoc"
                        value="{{ old('DbServerLoc') }}">
                    @error('DbServerLoc')
                        <h6 class="form-helper"style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="DbUserName" class="form-label">Username Database</label>
                    <input type="text" class="form-control" id="DbUserName" name="DbUserName"
                        value="{{ old('DbUserName') }}">
                    @error('DbUserName')
                        <h6 class="form-helper"style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="DbPassword" class="form-label">Password Database</label>
                    <input type="password" class="form-control" id="DbPassword" name="DbPassword">
                    @error('DbPassword')
                        <h6 class="form-helper"style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>

            </form>
        </div>
    </div>
@endsection
