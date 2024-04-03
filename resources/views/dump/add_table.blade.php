@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">Add Table</h3>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('storeTable', $erp) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="Name" class="form-label">Nama Tabel</label>
                    <input type="text" class="form-control" id="Name" name="Name" value="{{ old('Name') }}">
                    @error('Name')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="Description" class="form-label">Deskripsi Tabel</label>
                    <textarea class="form-control" style="height:100px" id="Description" name="Description">{{ old('Description') }}</textarea>
                    @error('Description')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>

            </form>
        </div>
    </div>
@endsection
