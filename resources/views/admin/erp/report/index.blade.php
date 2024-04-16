@extends('template.dashboard')

@section('title')
    <h3 class="nav-link">{{ $erp }} Report</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>
    <div class="card">
        <div class="card-body p-4">
            <h4 class="card-title fw-semibold mb-4">Report List</h4>
            {{-- {{ dd(auth()->user()->Role) }} --}}
            <h5 class="fw-semibold mb-3"><a href="{{ route('addReport', $erp) }}">Tambahkan Report</a></h5>

            <form action="{{ route('searchReport', $erp) }}" method="GET" style="width:25%"
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
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">No</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Nama Report</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Deskripsi Report</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Kategori</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Aksi</h4>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $Report)
                            <tr>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $loop->iteration }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $Report->Name }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">
                                        <pre style="font-family:var(--bs-font-sans-serif);font-size: 1.09375rem;">{{ $Report->Description }}</pre>
                                    </h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $Report->category->Name }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center justify-content-center">
                                    <div class="d-flex align-items-center gap-2 justify-content-center mb-1">
                                        <span class="badge bg-info rounded-1 fw-semibold"><a
                                                href="{{ route('editReport', [$erp, $Report->ReportID]) }}"
                                                style="color: aliceblue">Edit</a></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 justify-content-center mb-1">
                                        <span class="badge bg-secondary rounded-1 fw-semibold"><a
                                                href="{{ route('detailReport', [$erp, $Report->ReportID]) }}"
                                                style="color: aliceblue">View</a></span>
                                    </div>
                                    <form action="{{ route('deleteReport', [$erp, $Report->ReportID]) }}" method="POST"
                                        id="deleteReport">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="badge bg-danger mb-1 fw-semibold"
                                            style="color:white; border:none">Delete
                                        </button>
                                    </form>
                                    {{-- <td class="border-bottom-0 ">
                                    @php
                                        $token = hash('sha256', 'ReportSLS' . $Report->category->Name);
                                    @endphp
                                    <h5 class="fw-semibold mb-0 fs-4 mt-2"><a
                                            href="/report/{{ $erp }}/{{ $token }}/{{ $Report->Name }}/{{ $Report->category->Name }}">Something</a>
                                    </h5>
                                </td> --}}
                                    {{-- <div class="d-flex align-items-center gap-2 justify-content-center mb-1">
                                        <span class="badge bg-danger rounded-1 fw-semibold"><a
                                                href="{{ route('deleteModule', [$erp, $module->ModuleID]) }}"
                                                style="color: aliceblue">Delete</a></span>
                                    </div> --}}
                                    {{-- </td> --}}
                                    {{-- <td class="border-bottom-0 ">
                                    
                                    <h5 class="fw-semibold mb-0 fs-4 mt-2"><a
                                            href="#">Edit</a></h5>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $reports->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection

<script>
    var msg = '{{ Session::get('alert') }}';
    var exist = '{{ Session::has('alert') }}';
    if (exist) {
        alert(msg);
    }
</script>
