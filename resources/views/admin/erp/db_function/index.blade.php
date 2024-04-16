@extends('template.dashboard')

@section('title')
    <h3 class="nav-link">Database Function {{ $erp }}</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="card">
        <div class="card-body p-4">
            <h4 class="card-title fw-semibold mb-4">Database Function List</h4>
            {{-- {{ dd(auth()->user()->Role) }} --}}
            @if (auth()->user()->Role != 'User')
                <h5 class="fw-semibold mb-3"><a href="{{ route('addFunction', $erp) }}">Tambahkan Function</a></h5>
            @endif

            <form action="{{ route('searchDBFunction', $erp) }}" method="GET" style="width:25%"
                class="d-flex justify-content-between align-items-center">
                <div class="form-group mb-0 flex-grow-1 me-2">
                    <input type="text" name="search" class="form-control" placeholder="Search by name">
                </div>
                <button type="submit" class="btn btn-dark">
                    Search
                </button>
            </form>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark
                        fs-4">
                        <tr>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">No</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Nama Function</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Deskripsi</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Aksi</h4>
                            </th>
                            {{-- <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Aksi</h6>
                            </th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($functions as $function)
                            <tr>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $loop->iteration }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $function->Name }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">
                                        <pre style="font-family:var(--bs-font-sans-serif);font-size: 1.09375rem;">{{ strlen($function->Description) > 75 ? substr($function->Description, 0, 75) . '...' : $function->Description }}</pre>
                                    </h5>
                                </td>
                                <td class="border-bottom-0 text-center justify-content-center">
                                    <div class="d-flex align-items-center gap-2 justify-content-center mb-1">
                                        <span class="badge bg-info rounded-1 fw-semibold"><a
                                                href="{{ route('detailFunction', ['erp' => $erp, 'id' => $function->id]) }}"
                                                style="color: aliceblue">Edit</a></span>
                                    </div>
                                    {{-- <form action="{{ route('deleteDB', ['erp' => $erp, 'dbid' => $view->DBID]) }}"
                                        method="POST">
                                        @method('delete')
                                        @csrf

                                        <div class="d-flex align-items-center gap-2 justify-content-center">
                                            <button type="submit" class="badge bg-danger rounded-1 fw-semibold"
                                                style="color:white; border:none">Delete
                                            </button>
                                        </div>
                                    </form> --}}
                                </td>
                                {{-- <td class="border-bottom-0 ">
                                    <h5 class="fw-semibold mb-0 fs-4"><a
                                            href="#">Delete</a></h5>
                                    <h5 class="fw-semibold mb-0 fs-4 mt-2"><a
                                            href="#">Edit</a></h5>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $functions->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
