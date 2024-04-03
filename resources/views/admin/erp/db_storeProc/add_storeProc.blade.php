@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">Add Database Store Procedure</h3>
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
            <form action="{{ route('storeStoreProc', $erp) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="Name" class="form-label">Nama Database Store Procedure</label>
                    <input type="text" class="form-control" id="Name" name="Name" value="{{ old('Name') }}">
                    @error('Name')
                        <h6 class="form-helper"style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="Description" class="form-label">Deskripsi Database Store Procedure</label>
                    <textarea class="form-control" style="height:100px" id="Description" name="Description">{{ old('Description') }}</textarea>
                    @error('Description')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="SQL_Query" class="form-label">SQL Query</label>
                    <textarea class="form-control" style="height:100px" id="SQL_Query" name="SQL_Query">{{ old('SQL_Query') }}</textarea>
                    @error('SQL_Query')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>

            </form>
        </div>
    </div>
@endsection
