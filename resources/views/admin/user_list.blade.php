@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">User List</h3>
@endsection

@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="card">
        <div class="card-body p-4">
            <h4 class="card-title fw-semibold mb-4">User List</h4>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    {{-- {{ dd(serialize($users->items())) }} --}}
                    <h5 class="fw-semibold mb-3"><a href="{{ route('registerUser') }}">Tambahkan User</a></h5>
                </div>
                <form action="{{ route('filter.users') }}" method="POST" style="width: 20%">
                    @csrf
                    <select name="status" id="filter" class="form-select" onchange="submit()">
                        <option value="" selected disabled>Select Filter</option>
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Users</option>
                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active Users</option>
                        <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive Users</option>
                    </select>
                    {{-- <button type="submit">Filter</button> --}}
                </form>
            </div>

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
            <form action="{{ route('user.search') }}" method="GET" style="width:25%"
                class="d-flex justify-content-between align-items-center" id ="searchForm">
                @csrf
                <div class="form-group mb-0 flex-grow-1 me-2">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Search by name"
                        value="{{ session('search') }}">
                </div>
                <button type="submit" class="btn btn-dark">
                    Search
                </button>
            </form>
            {{-- <form action="{{ route('user.search') }}" method="GET" class="d-flex align-items-center">
                <div class="input-group me-2">
                    <input type="text" name="search" class="form-control" style="width: 200px;"
                        placeholder="Search by name">
                </div>
                <button class="btn btn-primary" type="submit">Search</button>
            </form> --}}
            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark
                            fs-4">
                        <tr>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Nama Lengkap</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">NIK</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Username</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Akses</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Aktif</h4>
                            </th>
                            <th class="border-bottom-0 text-center">
                                <h4 class="fw-semibold mb-0">Aksi</h4>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class= 'user-row' data-is-active="{{ $user->isActive ? '1' : '0' }}">
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $user->FullName }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $user->NIK }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $user->UserName }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center">
                                    <h5 class="fw-semibold mb-1">{{ $user->Role }}</h5>
                                </td>
                                <td class="border-bottom-0 text-center active-status">
                                    <h5 class="fw-semibold mb-1">{{ $user->isActive ? 'Active' : 'Inactive' }}</h5>
                                </td>
                                {{-- <td class="border-bottom-0 text-center">
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        <span
                                            class="badge bg-primary rounded-1 fw-semibold">{{ $user->Role }}</span>
                                    </div>
                                </td> --}}
                                <td class="border-bottom-0  text-center justify-content-center">
                                    @if ($user->isActive == 0)
                                        <form action="{{ route('activateUser', $user) }}" method="POST"
                                            id="activateUser-{{ $user->UserID }}">
                                            @method('put')
                                            @csrf
                                            {{-- <h5 class="fw-semibold mb-0 fs-4" style="color: red; cursor:pointer"
                                            onclick="submit()">Delete</h5> --}}
                                            <button type="submit" class="badge bg-success mb-1 fw-semibold"
                                                style="color:white; border:none"
                                                id="activate-button-{{ $user->UserID }}">Activate
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('deactivateUser', $user) }}" method="POST"
                                            id="deactivateUser-{{ $user->UserID }}">
                                            @method('put')
                                            @csrf
                                            {{-- <h5 class="fw-semibold mb-0 fs-4" style="color: red; cursor:pointer"
                                        onclick="submit()">Delete</h5> --}}
                                            <button type="submit" class="badge bg-danger mb-1 fw-semibold"
                                                style="color:white; border:none"
                                                id="delete-button-{{ $user->UserID }}">Deactivate
                                            </button>
                                        </form>
                                    @endif
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
            <div class="d-flex justify-content-center mt-3">
                {{ $users->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach ($users as $user)
            document.getElementById('delete-button-{{ $user->UserID }}').addEventListener('click', function(
                event) {
                event.preventDefault();
                if (confirm("Are you sure you want to delete this paket?")) {
                    document.getElementById('deactivateUser-{{ $user->UserID }}').submit();
                }
            });
        @endforeach
    });
</script>
