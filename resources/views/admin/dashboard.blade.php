@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">Dashboard</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="row">
        <div class="col-lg-4 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="p-4">
                    <h5 class="card-title fw-semibold">Profil</h5>
                </div>
                @if (session('success_psw'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success_psw') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card-body p-4 d-flex flex-column justify-content-center" style="margin-top: -18px">
                    <ul class="timeline-widget mb-0 position-relative text-center">
                        <li class="timeline-item d-flex">
                            <div class="timeline-time text-dark flex-shrink-0 text-start">Nama Lengkap
                            </div>
                            <div class="timeline-desc text-dark fw-semibold">{{ auth()->user()->FullName }}
                            </div>
                        </li>
                        <li class="timeline-item d-flex">
                            <div class="timeline-time text-dark flex-shrink-0 text-start">NIK</div>
                            <div class="timeline-desc text-dark fw-semibold">{{ auth()->user()->NIK }}
                            </div>
                        </li>
                        <li class="timeline-item d-flex">
                            <div class="timeline-time text-dark flex-shrink-0 text-start">Role</div>
                            <div class="timeline-desc text-dark fw-semibold">{{ auth()->user()->Role }}
                            </div>
                        </li>
                        <li class="timeline-item d-flex">
                            <div class="timeline-time text-dark flex-shrink-0 text-start">Username</div>
                            <div class="timeline-desc text-dark fw-semibold">{{ auth()->user()->UserName }}
                            </div>
                        </li>
                        <li class="timeline-item d-flex">
                            <div class="timeline-time text-dark flex-shrink-0 text-start">Password</div>
                            <div class="timeline-desc text-dark fw-semibold"><a
                                    href="{{ route('showPswChange', auth()->user()->UserID) }}">Ubah
                                    Password</a></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <h4 class="card-title fw-semibold mb-4">User List</h4>
                    <h5 class="fw-semibold mb-3"><a href="{{ route('registerUser') }}">Tambahkan User</a></h5>
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
                            <thead class="text-dark
                                    fs-4">
                                <tr>
                                    <th class="border-bottom-0 text-center">
                                        <h6 class="fw-semibold mb-0">No</h6>
                                    </th>
                                    <th class="border-bottom-0 text-center">
                                        <h6 class="fw-semibold mb-0">Nama Lengkap</h6>
                                    </th>
                                    <th class="border-bottom-0 text-center">
                                        <h6 class="fw-semibold mb-0">NIK</h6>
                                    </th>
                                    <th class="border-bottom-0 text-center">
                                        <h6 class="fw-semibold mb-0">Akses</h6>
                                    </th>
                                    <th class="border-bottom-0 text-center">
                                        <h6 class="fw-semibold mb-0">Aksi</h6>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="border-bottom-0 text-center">
                                            <h6 class="fw-semibold mb-1">{{ $loop->iteration }}</h6>
                                        </td>
                                        <td class="border-bottom-0 text-center">
                                            <h6 class="fw-semibold mb-1">{{ $user->FullName }}</h6>
                                            <span class="fw-normal">{{ $user->UserName }}</span>
                                        </td>
                                        <td class="border-bottom-0 text-center">
                                            <h6 class="fw-semibold mb-1">{{ $user->NIK }}</h6>
                                        </td>
                                        <td class="border-bottom-0 text-center">
                                            <h6 class="fw-semibold mb-1">{{ $user->Role }}</h6>
                                        </td>
                                        {{-- <td class="border-bottom-0 text-center">
                                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                                <span
                                                    class="badge bg-primary rounded-1 fw-semibold">{{ $user->Role }}</span>
                                            </div>
                                        </td> --}}
                                        <td class="border-bottom-0  text-center justify-content-center">
                                            <form action="{{ route('deactivateUser', $user) }}" method="POST"
                                                id="deleteForm-{{ $user->UserID }}">
                                                @method('put')
                                                @csrf
                                                {{-- <h6 class="fw-semibold mb-0 fs-4" style="color: red; cursor:pointer"
                                                    onclick="submit()">Delete</h6> --}}
                                                <button type="submit" class="badge bg-danger mb-1 fw-semibold"
                                                    style="color:white; border:none"
                                                    id="delete-button-{{ $user->UserID }}">Deactivate
                                                </button>
                                            </form>
                                            <div class="d-flex align-items-center gap-2 justify-content-center mb-1">
                                                <span class="badge bg-primary rounded-1 fw-semibold"><a
                                                        href="{{ route('editUser', $user->UserID) }}"
                                                        style="color: aliceblue">Edit</a></span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="position-relative" style="margin-top: 20px; text-align:end">
                        <!-- Add a margin to separate the link from the table -->
                        <h6 class="fw-semibold mb-3"><a href="{{ route('userList') }}">Show All</a></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach ($erps as $erp)
            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('erpMenu', $erp->Initials) }}" class="card-link">
                    <div class="card overflow-hidden rounded-2">
                        <div class="card-body pt-3 pb-3 p-4 d-flex flex-column align-items-center justify-content-center">
                            <h5 class="card-title fw-semibold">{{ $erp->Initials }}</h5>
                            {{-- <div class="d-flex align-items-center justify-content-between">
                            <h6 class="fw-semibold fs-4 mb-0">$50 <span
                                    class="ms-2 fw-normal text-muted fs-3"></span></h6>
                        </div> --}}
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach ($users as $user)
            document.getElementById('delete-button-{{ $user->UserID }}').addEventListener('click', function(
                event) {
                event.preventDefault();
                if (confirm("Are you sure you want to deactivate this user?")) {
                    document.getElementById('deleteForm-{{ $user->UserID }}').submit();
                }
            });
        @endforeach
    });
</script>
