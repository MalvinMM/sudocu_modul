@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">Kategori Report {{ $erp }}</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('addReportCategory', $erp) }}" method="POST" id="fullForm">
                @csrf
                <label for="Name" class="form-label">Tambah Kategori</label>
                <div class="d-flex align-items-center">
                    <div>
                        <input type="text" class="form-control" id="Name" name="Name" value="{{ old('Name') }}">
                        @error('Name')
                            <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary ms-2">Submit</button>
                </div>
            </form>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($categories->isNotEmpty())
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle">
                        <thead class="text-dark
                            fs-4">
                            <tr>
                                <th class="border-bottom-0 text-center">
                                    <h4 class="fw-semibold mb-0">Nama Kategori</h4>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h4 class="fw-semibold mb-0">Aksi</h4>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $item)
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $item->Name }}</h5>
                                </td>
                                <td class="border-bottom-0  text-center justify-content-center">
                                    <form action="{{ route('delReportCategory', [$erp, $item->CategoryID]) }}"
                                        method="POST">
                                        @method('delete')
                                        @csrf
                                        {{-- <h5 class="fw-semibold mb-0 fs-4" style="color: red; cursor:pointer"
                                        onclick="submit()">Delete</h5> --}}
                                        <button type="submit" class="badge bg-danger mb-1 fw-semibold"
                                            style="color:white; border:none">Delete
                                        </button>
                                    </form>
                                    {{-- <div class="d-flex align-items-center gap-2 justify-content-center mb-1">
                                        <span class="badge bg-primary rounded-1 fw-semibold"><a
                                                href="{{ route('editUser', $user->UserID) }}"
                                                style="color: aliceblue">Edit</a></span>
                                    </div> --}}
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
