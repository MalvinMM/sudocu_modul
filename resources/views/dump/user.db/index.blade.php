@extends('template.dashboard')

@section('title')
    <h3 class="nav-link">Master Database {{ $erp->Initials }}</h3>
@endsection
@section('content')
    <div class="card">
        <div class="card-body p-4">
            <h4 class="card-title fw-semibold mb-4">Database List</h4>
            @if (auth()->user()->Role != 'User')
                <h5 class="fw-semibold mb-3"><a href="{{ route('addDB', $erp->Initials) }}">Tambahkan Database</a></h5>
            @endif
            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark
                        fs-4">
                        <tr>
                            <th class="border-bottom-0 text-center">
                                <h6 class="fw-semibold mb-0">No</h6>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h6 class="fw-semibold mb-0">Nama Database</h6>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h6 class="fw-semibold mb-0">Lokasi Database</h6>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h6 class="fw-semibold mb-0">Aksi</h6>
                            </th>
                            {{-- <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Aksi</h6>
                            </th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dbs as $db)
                            <tr>
                                <td class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-1">{{ $loop->iteration }}</h6>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-1">{{ $db->DbName }}</h6>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-1">{{ $db->DbServerLoc }}</h6>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        <span class="badge bg-primary rounded-1 fw-semibold"><a
                                                href="{{ route('editDB', ['erp' => $erp->Initials, 'dbid' => $db->DBID]) }}"
                                                style="color: aliceblue">Edit</a></span>
                                    </div>
                                </td>
                                {{-- <td class="border-bottom-0 ">
                                    <h6 class="fw-semibold mb-0 fs-4"><a
                                            href="#">Delete</a></h6>
                                    <h6 class="fw-semibold mb-0 fs-4 mt-2"><a
                                            href="#">Edit</a></h6>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
