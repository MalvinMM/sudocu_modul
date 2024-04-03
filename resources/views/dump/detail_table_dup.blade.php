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
            {{-- {{ dd(auth()->user()->Role) }} --}}
            {{-- <h5 class="fw-semibold mb-3"><a href="{{ route('addTable', $erp) }}">Tambahkan Table</a></h5> --}}
            <div class="table-responsive">
                <form id="updateForm" method="POST" action="{{ route('updateFields', $erp) }}">
                    @csrf
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
                            @foreach ($fields as $field)
                                <tr>
                                    <td class="border-bottom-0 text-center">
                                        <h6 class="fw-semibold mb-1">{{ $loop->iteration }}</h6>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        <h6 class="fw-semibold mb-1">{{ $field->Name }}</h6>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        <textarea id="description_{{ $loop->iteration }}" name="description" class="form-control description" rows="3"
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
                                        <select name="tableIDRef" class="form-select tableIDRef"
                                            id="tableIDRef_{{ $loop->iteration }}"
                                            data-field-id="{{ $field->FieldIDRef }}">
                                            <option value=""
                                                @if (!$field->TableIDRef) selected disabled @endif>Select Table
                                            </option>
                                            @foreach ($tables as $table)
                                                <option value="{{ $table->TableID }}"
                                                    @if ($table->TableID == old('tableIDRef', $field->TableIDRef)) selected @endif>{{ $table->Name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        <select name="fieldIDRef" class="form-select fieldIDRef"
                                            id="fieldIDRef_{{ $loop->iteration }}" onchange="submitForm(this)"
                                            data-field-id="{{ $field->FieldID }}">
                                            {{-- <option value="" selected disabled>Select Field</option> --}}
                                        </select>
                                    </td>
                                    {{-- <input type="hidden" name="fieldID" value="{{ $field->FieldID }}"> --}}
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
        var descriptionTextareas = document.querySelectorAll('.description');

        // Add event listener to each textarea
        descriptionTextareas.forEach(function(textarea) {
            textarea.addEventListener('keydown', function(event) {
                // Check if the pressed key is Enter (key code 13)
                if (event.keyCode === 13 && !event.shiftKey) {
                    event.preventDefault(); // Prevent default Enter behavior (new line)
                    var form = textarea.closest('form');
                    var fieldID = textarea.getAttribute('data-field-id');
                    var closestRow = this.closest('tr');

                    var hiddenFieldID = document.createElement('input');
                    var hiddenDescription = document.createElement('input');

                    hiddenDescription.type = 'hidden';
                    hiddenDescription.name = 'description';
                    hiddenDescription.value = closestRow.querySelector('.description').value;
                    form.appendChild(hiddenDescription);

                    hiddenFieldID.type = 'hidden';
                    hiddenFieldID.name = 'fieldID';
                    hiddenFieldID.value = fieldID;
                    form.appendChild(hiddenFieldID);


                    var fieldIDRefDropdown = closestRow.querySelector('.fieldIDRef');
                    var selectedFieldIDRef = fieldIDRefDropdown.value;
                    var hiddenFieldIDRef = form.querySelector('input[name="fieldIDRef_' +
                        fieldID + '"]');

                    if (!hiddenFieldIDRef) {
                        hiddenFieldIDRef = document.createElement('input');
                        hiddenFieldIDRef.type = 'hidden';
                        hiddenFieldIDRef.name = 'fieldIDRef';
                        form.appendChild(hiddenFieldIDRef);
                    }
                    hiddenFieldIDRef.value = selectedFieldIDRef;

                    // Find the tableIDRef dropdown within the row
                    var tableIDRefDropdown = closestRow.querySelector('.tableIDRef');
                    // Get the selected value from the tableIDRef dropdown
                    var selectedTableIDRef = tableIDRefDropdown.value;
                    // Find or create the hidden input field for tableIDRef
                    var hiddenTableIDRef = form.querySelector('input[name="tableIDRef_' +
                        fieldID + '"]');
                    if (!hiddenTableIDRef) {
                        hiddenTableIDRef = document.createElement('input');
                        hiddenTableIDRef.type = 'hidden';
                        hiddenTableIDRef.name = 'tableIDRef';
                        form.appendChild(hiddenTableIDRef);
                    }
                    // Update the value of the hidden input field
                    hiddenTableIDRef.value = selectedTableIDRef;

                    form.submit(); // Submit the form
                }
            });
        });

        // Loop through each dropdown
        tableIDRefDropdowns.forEach(function(dropdown) {
            // Add event listener to the TableIDRef dropdown
            dropdown.addEventListener('change', function() {
                var tableID = this.value; // Get the selected table ID
                var fieldIDRefDropdown = this.closest('tr').querySelector('.fieldIDRef');
                var fieldIDRefValue = dropdown.getAttribute('data-field-id');
                // Clear existing options
                fieldIDRefDropdown.innerHTML = '';

                // Fetch fields associated with the selected table via AJAX
                fetch('/fetch-fields/' + tableID)
                    .then(response => response.json())
                    .then(fields => {
                        var opsi = document.createElement('option');
                        opsi.value = '';
                        opsi.textContent = 'Select Field';
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

    function submitForm(selectElement) {
        var fieldID = selectElement.getAttribute('data-field-id');
        var form = selectElement.closest('form');

        // Find the closest row containing the selectElement
        var closestRow = selectElement.closest('tr');

        // Find the fieldIDRef dropdown within the row
        var fieldIDRefDropdown = closestRow.querySelector('.fieldIDRef');

        // Get the selected value from the fieldIDRef dropdown
        var selectedFieldIDRef = fieldIDRefDropdown.value;

        // Find or create the hidden input field for fieldIDRef
        var hiddenFieldIDRef = form.querySelector('input[name="fieldIDRef_' + fieldID + '"]');
        if (!hiddenFieldIDRef) {
            hiddenFieldIDRef = document.createElement('input');
            hiddenFieldIDRef.type = 'hidden';
            hiddenFieldIDRef.name = 'fieldIDRef';
            form.appendChild(hiddenFieldIDRef);
        }

        // Update the value of the hidden input field
        hiddenFieldIDRef.value = selectedFieldIDRef;

        // Find the tableIDRef dropdown within the row
        var tableIDRefDropdown = closestRow.querySelector('.tableIDRef');

        // Get the selected value from the tableIDRef dropdown
        var selectedTableIDRef = tableIDRefDropdown.value;

        // Find or create the hidden input field for tableIDRef
        var hiddenTableIDRef = form.querySelector('input[name="tableIDRef_' + fieldID + '"]');
        if (!hiddenTableIDRef) {
            hiddenTableIDRef = document.createElement('input');
            hiddenTableIDRef.type = 'hidden';
            hiddenTableIDRef.name = 'tableIDRef';
            form.appendChild(hiddenTableIDRef);
        }

        // Update the value of the hidden input field
        hiddenTableIDRef.value = selectedTableIDRef;

        // Add hidden input field to hold the FieldID
        var hiddenFieldID = document.createElement('input');
        hiddenFieldID.type = 'hidden';
        hiddenFieldID.name = 'fieldID';
        hiddenFieldID.value = fieldID;

        // Append the hidden fields to the form
        form.appendChild(hiddenFieldID);


        var hiddenDescription = document.createElement('input');
        hiddenDescription.type = 'hidden';
        hiddenDescription.name = 'description';
        hiddenDescription.value = closestRow.querySelector('.description').value;
        form.appendChild(hiddenDescription);

        // Submit the form
        form.submit();
    }



    // document.addEventListener('DOMContentLoaded', function() {
    //     var updateForm = document.getElementById('updateForm');
    //     var fieldId = '{{ $field->FieldID }}';
    //     updateForm.action = updateForm.action.replace(/\/\d+$/, '/' + fieldId);
    // });
</script>
