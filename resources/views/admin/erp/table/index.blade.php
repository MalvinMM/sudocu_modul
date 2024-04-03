@extends('template.dashboard')

@section('title')
    <h3 class="nav-link">{{ $erp }} Tables</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="card">
        <div class="card-body p-4">
            <h4 class="card-title fw-semibold mb-4">Table List</h4>
            @if (session('success'))
                <div class="mt-3 alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('info'))
                <div class="mt-3 alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('danger'))
                <div class="mt-3 alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('danger') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            {{-- {{ dd(auth()->user()->Role) }} --}}
            {{-- <h5 class="fw-semibold mb-3"><a href="{{ route('addTable', $erp) }}">Tambahkan Table</a></h5> --}}
            <h5 class="fw-semibold nav-link">Tambahkan Table</h5>
            <div class="mb-3">
                <a href="{{ Storage::url('Template Import Tabel.xlsx') }}" download> Download Excel Template </a>
            </div>

            <div class="row mt-3" style="justify-content: space-between; align-items: flex-end;">
                <div class="col-md-4"> <!-- Adjust the column width as needed -->
                    <form action="{{ route('import.excel', $erp) }}" method="POST" enctype="multipart/form-data"
                        class="d-flex">
                        @csrf
                        <div class="form-group flex-grow-1 me-2">
                            <input type="file" name="file" id="file" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success">Import</button>
                    </form>
                </div>
            </div>
            @if ($errors->any())
                <h6 class="form-helper" style="color:red">{!! implode('', $errors->all('<div>:message</div>')) !!}</h6>
            @endif

            <div class="row mt-3" style="justify-content: space-between; align-items: flex-end;">
                <div class="col-md-4"> <!-- Adjust the column width as needed -->
                    <form action="{{ route('searchTable', $erp) }}" method="GET" class="d-flex">
                        <div class="form-group flex-grow-1 me-2">
                            <input type="text" name="search" class="form-control" placeholder="Search by name">
                        </div>
                        <button type="submit" class="btn btn-dark">Search</button>
                    </form>
                </div>
            </div>

            <form action="{{ route('updateTable', [$erp]) }}" method="POST">
                @csrf
                @method('put')
                <div class="d-flex justify-content-end ms-auto" style="margin-right:1cm">
                    <button type="submit" class="btn btn-primary">Submit Changes</button>
                </div>
        </div>

        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle">
                <thead class="text-dark
                        fs-4">
                    <tr>
                        <th class="border-bottom-0 text-center">
                            <h4 class="fw-semibold mb-0">No</h4>
                        </th>
                        <th class="border-bottom-0 text-center">
                            <h4 class="fw-semibold mb-0">Nama Tabel</h4>
                        </th>
                        <th class="border-bottom-0 text-center">
                            <h4 class="fw-semibold mb-0">Deskripsi Tabel</h4>
                        </th>
                        <th class="border-bottom-0 text-center">
                            <h4 class="fw-semibold mb-0">Aksi</h4>
                        </th>
                        {{-- <th class="border-bottom-0">
                                <h5 class="fw-semibold mb-0">Aksi</h5>
                            </th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tables as $table)
                        <tr>
                            <td class="border-bottom-0 text-center">
                                <h5 class="fw-semibold mb-1">{{ $loop->iteration }}</h5>
                            </td>
                            <td class="border-bottom-0 text-center">
                                <h5 class="fw-semibold mb-1">{{ $table->Name }}</h5>
                            </td>
                            <td class="border-bottom-0 text-center">
                                <textarea name="description[]" class="form-control" rows="5" id="description_{{ $table->TableID }}">{{ $table->Description }}</textarea>
                                <input type="hidden" name="tableID[]" value="{{ $table->TableID }}">
                            </td>
                            <td class="border-bottom-0 text-center justify-content-center">
                                <div class="d-flex align-items-center gap-2 justify-content-center mb-1">
                                    <span class="badge bg-secondary rounded-1 fw-semibold"><a
                                            href="{{ route('detailTable', [$erp, $table->TableID]) }}"
                                            style="color: aliceblue">View</a></span>
                                </div>
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
            </form>
        </div>
    </div>
    </div>
@endsection

<!-- JavaScript to submit the form when Enter key is pressed -->
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        var textarea = document.getElementById('description_{{ $table->TableID }}');
        var form = document.getElementById('tableForm_{{ $table->TableID }}');

        console.log(textarea);
        textarea.addEventListener('keydown', function(event) {
            // Check if Enter key is pressed (key code 13) and Shift key is not pressed
            if (event.keyCode === 13 && !event.shiftKey) {
                event.preventDefault(); // Prevent default Enter behavior (new line)

                // Submit the form
                form.submit();
            }
        });
    });
</script> --}}
