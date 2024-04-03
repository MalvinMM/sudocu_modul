@extends('template.dashboard')

@section('title')
    <h3 class="nav-link">{{ $erp }} - {{ $parent->Name }} Table</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="card">
        <div class="card-body p-4">
            <h4 class="card-title fw-semibold mb-4">Field List</h4>
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
            {{-- {{ dd(auth()->user()->Role) }} --}}
            <div class="table-responsive">
                <form id="updateForm" method="POST" action="{{ route('updateFields', $erp) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Submit Changes</button>

                    <table class="table text-nowrap mb-0 align-middle">
                        <thead class="text-dark
                        fs-4">
                            <tr>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">No</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Nama Field</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Deskripsi Field</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Nullable</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Default Value</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Table ID Ref</h6>
                                </th>
                                <th class="border-bottom-0 text-center">
                                    <h6 class="fw-semibold mb-0">Field ID Ref</h6>
                                </th>
                                {{-- <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Aksi</h6>
                            </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fields as $index => $field)
                                <tr>
                                    <td class="border-bottom-0 text-center">
                                        <h6 class="fw-semibold mb-1">{{ $loop->iteration }}</h6>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        <h6 class="fw-semibold mb-1">{{ $field->Name }}</h6>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        <textarea id="description_{{ $loop->iteration }}" name="description[]" class="form-control description" rows="3"
                                            data-field-id="{{ $field->FieldID }}">{{ $field->Description }}</textarea>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        @if ($field->AllowNull == true)
                                            <h6 class="fw-semibold mb-1">Nullable</h6>
                                        @else
                                            <h6 class="fw-semibold mb-1">Not Nullable</h6>
                                        @endif
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        @if ($field->DefaultValue)
                                            <h6 class="fw-semibold mb-1">{{ $field->DefaultValue }}</h6>
                                        @else
                                            <h6 class="fw-semibold mb-1">None</h6>
                                        @endif
                                    </td>
                                    <td class="border-bottom-0" style="text-align:center">
                                        <select name="tableIDRef[]" class="form-select tableIDRef"
                                            id="tableIDRef_{{ $loop->iteration }}"
                                            data-field-id="{{ $field->FieldIDRef }}">
                                            <option value="" selected>Select Table</option>
                                            @foreach ($tables as $table)
                                                <option value="{{ $table->TableID }}"
                                                    {{ old('tableIDRef.' . $index) == $table->TableID ? 'selected' : ($field->TableIDRef == $table->TableID ? 'selected' : '') }}>
                                                    {{ $table->Name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="border-bottom-0 text-center">
                                        <select name="fieldIDRef[]" class="form-select fieldIDRef"
                                            id="fieldIDRef_{{ $loop->iteration }}">
                                            <option value="" selected>Select Field</option>
                                        </select>
                                        @error('fieldIDRef.' . $index)
                                            <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                                        @enderror
                                    </td>
                                    <input type="hidden" name="fieldID[]" value="{{ $field->FieldID }}">
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
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all elements with the class 'tableIDRef'
        var tableIDRefDropdowns = document.querySelectorAll('.tableIDRef');

        // Loop through each dropdown
        tableIDRefDropdowns.forEach(function(dropdown) {
            // Add event listener to the TableIDRef dropdown
            dropdown.addEventListener('change', function() {
                var tableID = this.value; // Get the selected table ID
                var fieldIDRefDropdown = this.closest('tr').querySelector('.fieldIDRef');
                var fieldIDRefValue = dropdown.getAttribute('data-field-id');

                if (!tableID) {
                    // If tableID is empty, reset the fieldIDRefDropdown
                    fieldIDRefDropdown.innerHTML = ''; // Clear existing options
                    var opsi = document.createElement('option');
                    opsi.value = '';
                    opsi.textContent = 'Select Field';
                    opsi.selected = true;
                    fieldIDRefDropdown.appendChild(opsi);
                    return; // Exit the function early
                }

                // Fetch fields associated with the selected table via AJAX
                fetch('/fetch-fields/' + tableID)
                    .then(response => response.json())
                    .then(fields => {
                        fieldIDRefDropdown.innerHTML = '';
                        var opsi = document.createElement('option');
                        opsi.value = '';
                        opsi.textContent = 'Select Field';
                        opsi.selected = true;
                        fieldIDRefDropdown.appendChild(opsi);
                        // Populate the FieldIDRef dropdown with fetched fields
                        fields.forEach(field => {
                            var option = document.createElement('option');
                            option.value = field.FieldID;
                            option.textContent = field.Name;
                            fieldIDRefDropdown.appendChild(option);
                            if (field.FieldID == fieldIDRefValue) {
                                option.selected =
                                    true; // Set the option as selected
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching fields:', error);
                    });
            });
            dropdown.dispatchEvent(new Event('change'));
        });
    });
</script>
