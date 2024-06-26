@extends('template.dashboard')

@section('title')
    <h3 class="nav-link">Detail Database Store Procedure {{ $storeProc->Name }}</h3>
@endsection

@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('danger') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="mb-5">
                <h4>Nama Store Procedure</h4>
                <h5>{{ $storeProc->Name }}</h5>
            </div>
            <form id="updateStoreProcForm" action="{{ route('updateStoreProc', [$erp, $storeProc->id]) }}" method="POST">
                @csrf
                @method('put')
                <div class="mb-5">
                    <h4 class="card-text">Deskripsi View</h4>
                    <textarea name="Description" class="form-control" rows="10">{{ old('Description', $storeProc->Description) }}</textarea>
                    @error('Description')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div>
                    <h4 class="card-text">SQL Query</h4>
                    <textarea name="SQL_Query" class="form-control" rows="10">{{ old('SQL_Query', $storeProc->SQL_Query) }}</textarea>
                    @error('SQL_Query')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                    {{-- <h5>{{ $view->SQL_Query }}</h5> --}}
                </div>
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
            {{-- @if (!$details->isEmpty())
                @foreach ($details as $index => $detail)
                    <h4 class="card-text">{{ $detail->Description }}</h4>
                    @if ($detail->FilePath)
                        <img alt="Gambar Sequence" class="img-fluid" style="max-width: 400px; max-height: 400px;"
                            src="{{ asset('storage/gambar_sequence/' . $detail->FilePath) }}">
                    @endif
                    <br> </br>
                @endforeach
            @endif --}}
        </div>
    </div>
@endsection
