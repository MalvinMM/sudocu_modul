@extends('template.dashboard')

@section('title')
    <h3 class="nav-link">Detail Module {{ $module->Name }}</h3>
@endsection

@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <h4 class="card-title">{{ $module->Name }} - {{ $module->category->Name }}</h5>
            </div>
            <div class="mb-3">
                <h4 class="card-text">
                    <pre class="card-text" style="font-family:var(--bs-font-sans-serif);">{{ $module->Description }}</pre>
                </h4>
            </div>
            @if (!$details->isEmpty())
                @foreach ($details as $index => $detail)
                    <h4 class="card-text">
                        <pre class="card-text" style="font-family:var(--bs-font-sans-serif);">{{ $detail->Description }}</pre>
                    </h4>
                    @if ($detail->FilePath)
                        <img alt="Gambar Sequence" class="img-fluid" style="max-width: 400px; max-height: 400px;"
                            src="{{ asset('storage/gambar_sequence/' . $detail->FilePath) }}">
                    @endif
                    <br> <br>
                @endforeach
            @endif
        </div>
    </div>
@endsection
