@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">Edit Report {{ $report->Name }}</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>
    <div class="card">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('updateReport', [$erp, $report->ReportID]) }}" method="POST" id="fullForm"
                enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="mb-3">
                    <label for="Name" class="form-label">Nama Report <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="Name" name="Name"
                        value="{{ old('Name', $report->Name ?? '') }}">
                    @error('Name')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="ReportDesc" class="form-label">Deskripsi Report <span style="color: red;">*</span></label>
                    <textarea class="form-control" style="height:100px" id="ReportDesc" name="ReportDesc">{{ old('ReportDesc', $report->Description ?? '') }}</textarea>
                    @error('ReporteDesc')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="Category" class="form-label">Kategori <span style="color: red;">*</span></label>
                    <select name="Category" id="Category" class="form-select" style="width:20%">
                        <option value="" selected disabled>Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->CategoryID }}" @if (old('Category', $report->CategoryID == $category->CategoryID)) selected @endif>
                                {{ $category->Name }}</option>
                        @endforeach
                    </select>
                    @error('Category')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                {{-- TEMPLATE --}}
                <div class="card details-card position-relative template-details-card" style="display: none;">
                    <div class="card-body position-relative">
                        <span class="delete-sequence-icon position-absolute end-0 top-0" style= "padding:30px"><i
                                class="ti ti-x" style="font-size: 20px;cursor: pointer; border-radius: 50%;"></i></span>
                        <div class="mb-3">
                            <label for="sequence" class="form-label">Sequence</label>
                            <input type="hidden" class="form-control" id="sequence" name="sequence[]" style="width:10%"
                                value="{{ old('sequence.0', 1) }}">
                            <input type="number" class="form-control" id="sequence" name="sequence[]" style="width:10%"
                                value="{{ old('sequence.0', 1) }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="Description" class="form-label">Deskripsi Sequence <span
                                    style="color: red;">*</span></label>
                            <textarea class="form-control" style="height:100px" id="Description" name="Description[]">{{ old('Description.0') }}</textarea>
                            {{-- @error('Description.*')
                                <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                            @enderror --}}
                        </div>
                        <div class="mb-3">
                            <label for="filePath" class="form-label">Path Gambar</label>
                            <input type="file" accept="image/png,image/jpg,image/jpeg" class="form-control"
                                id="filePath" name="filePath[]" multiple>
                        </div>
                    </div>
                </div>
                @if (!$details->isEmpty())
                    @foreach ($details as $index => $detail)
                        <div class="card details-card">
                            <div class="card-body">
                                <span class="delete-sequence-icon position-absolute end-0 top-0" style= "padding:30px"><i
                                        class="ti ti-x"
                                        style="font-size: 20px;cursor: pointer; border-radius: 50%;"></i></span>
                                <div class="mb-3">
                                    <label for="sequence" class="form-label">Sequence</label>
                                    <input type="hidden" class="form-control" id="sequence" name="sequence[]"
                                        value="{{ $detail->Sequence }}" style="width:10%">
                                    <input type="number" class="form-control" id="sequence" name="sequence[]"
                                        value="{{ $detail->Sequence }}" style="width:10%" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="Description" class="form-label">Deskripsi Sequence <span
                                            style="color: red;">*</span></label>
                                    <textarea class="form-control" style="height:100px" id="Description" name="Description[]">{{ old('Description.' . $index, $detail->Description) }}</textarea>
                                    @error('Description.' . $index)
                                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="filePath" class="form-label">Gambar</label>
                                    <input type="file" accept="image/png,image/jpg,image/jpeg" class="form-control"
                                        id="filePath" name="filePath[]">
                                    @if ($detail->FilePath)
                                        <br>
                                        <img alt="Gambar Sequence" style="max-width: 400px; max-height: 400px;"
                                            src="{{ asset('storage/gambar_sequence/' . $detail->FilePath) }}">
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" name="detailsID[]" value="{{ $detail->ReportDetailID }}">
                        </div>
                    @endforeach
                @else
                    <div class="card details-card">
                        <div class="card-body">
                            <span class="delete-sequence-icon position-absolute end-0 top-0" style= "padding:30px"><i
                                    class="ti ti-x"
                                    style="font-size: 20px;cursor: pointer; border-radius: 50%;"></i></span>
                            <div class="mb-3">
                                <label for="sequence" class="form-label">Sequence</label>
                                <input type="hidden" class="form-control" id="sequence" name="sequence[]"
                                    style="width:10%" value="{{ old('sequence.1', 1) }}">
                                <input type="number" class="form-control" id="sequence" name="sequence[]"
                                    style="width:10%" value="{{ old('sequence.1', 1) }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="Description" class="form-label">Deskripsi Sequence <span
                                        style="color: red;">*</span></label>
                                <textarea class="form-control" style="height:100px" id="Description" name="Description[]">{{ old('Description.1') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="filePath" class="form-label">Path Gambar</label>
                                <input type="file" accept="image/png,image/jpg,image/jpeg" class="form-control"
                                    id="filePath" name="filePath[]">
                            </div>
                        </div>
                    </div>
                @endif
                <div id="detailsContainer" class="mt-3">
                </div>
                <button type="button" id="addDetailsBtn" class="btn btn-secondary mt-3">Add New Details</button>

                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>
    </div>
@endsection



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var cardCount = parseInt({{ session('detailCount', count($details)) }});
        var errorKeys = {!! json_encode($errors->keys()) !!};
        var errorMessages = {!! json_encode($errors->all()) !!};

        let errors = {};

        for (var i = 0; i < {{ count($errors->all()) }}; i++) {
            errors[errorKeys[i]] = errorMessages[i];
        }
        if (cardCount > {{ count($details) }}) {
            var sequences = [];
            var descriptions = [];

            @for ($i = 0; $i < session()->get('detailCount'); $i++)
                // console.log({{ count($details) }});
                sequences.push('{{ session('detail_' . $i . '.sequence') }}');
                descriptions.push({!! json_encode(session('detail_' . $i . '.Description')) !!});
            @endfor
            for (var i = {{ count($details) }}; i < cardCount; i++) {
                var newCard = $('.template-details-card').clone();
                newCard.removeClass('template-details-card');

                // Populate detail field values from session arrays
                newCard.find('input[name="sequence[]"]').val(sequences[i]);
                newCard.find('textarea[name="Description[]"]').val(descriptions[i]);

                // Any other field you need to populate

                $('#detailsContainer').append(newCard);
                newCard.show();

                if (errors['Description.' + i]) {
                    newCard.find('textarea[name="Description[]"]').after(
                        '<h6 class="form-helper" style="color:red">' + errors['Description.' +
                            i] +
                        '</h6>');
                }
            }
        }

        $('#addDetailsBtn').click(function() {
            // Clone the default details form card
            var newCard = $('.template-details-card').clone();
            // Remove the template class from the cloned card
            newCard.removeClass('template-details-card');
            cardCount++;

            // Update form field IDs and names to prevent conflicts
            newCard.find('input, textarea').each(function() {
                var oldId = $(this).attr('id');
                var oldName = $(this).attr('name');
                $(this).attr('id', oldId + '_' + cardCount);
                $(this).attr('name', oldName.replace(/\[\d+\]/, '[' + cardCount + ']'));
                // Clear input values
                $(this).val('');
            });

            // Set default value for sequence input field
            newCard.find('#sequence_' + cardCount).val(cardCount);

            newCard.find('input[type=file]').attr('name', 'filePath[]');
            newCard.find('img').hide();

            // Append the new card to the details container
            $('#detailsContainer').append(newCard);
            newCard.show();
        });

        $(document).on('click', '.delete-sequence-icon', function() {
            // Remove the parent card of the delete icon
            var deletedCard = $(this).closest('.details-card');
            var detailsIDInput = deletedCard.find('input[name="detailsID[]"]');
            // Remove the parent card of the delete icon
            deletedCard.remove();

            // Remove the corresponding detailsID input
            detailsIDInput.remove();
            // Renumber the remaining sequence inputs
            $('.details-card').each(function(index) {
                // console.log(index);
                $(this).find('input[name="sequence[]"]').val(index);
            });
            cardCount = cardCount - 1;
            // console.log(cardCount);
        });

        $('#fullForm').submit(function() {
            // Remove the template card before submitting the form
            $('.template-details-card').remove();
        });

        // Prevent form submission when pressing Enter key
        $('#fullForm').keypress(function(e) {
            if (e.which == 13 && e.target.nodeName === 'TEXTAREA') {
                return; // Allow Enter key press for textarea
            } else if (e.which == 13) {
                e.preventDefault();
                return false;
            }
        });

    });
</script>
