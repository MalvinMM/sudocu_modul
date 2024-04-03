@extends('template.dashboard')

@section('title')
    <h3 class="nav-link">{{ $erp }} Module</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="card">
        <div class="card-body p-4">
            <h4 class="card-title fw-semibold mb-4">Module List</h4>
            {{-- {{ dd(auth()->user()->Role) }} --}}
            {{-- <h5 class="fw-semibold mb-3"><a href="{{ route('addModule', $erp) }}">Tambahkan Module</a></h5> --}}
            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">No</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Nama Modul</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Deskripsi Modul</h4>
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
                        @foreach ($modules as $module)
                            <tr>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $loop->iteration }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $module->Name }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $module->Description }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $module->category->Name }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center justify-content-center">
                                    <div class="d-flex align-items-center gap-2 justify-content-center mb-1">
                                        <span class="badge bg-secondary rounded-1 fw-semibold"><a
                                                href="{{ route('detailModule', [$erp, $module->ModuleID]) }}"
                                                style="color: aliceblue">View</a></span>
                                    </div>
                                </td>
                                {{-- <td class="border-bottom-0 ">
                                    
                                    <h5 class="fw-semibold mb-0 fs-4 mt-2"><a
                                            href="#">Edit</a></h5>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
