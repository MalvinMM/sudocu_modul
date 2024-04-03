@extends('template.dashboard_guest')

@section('title')
    <h3 class="nav-link">Detail Report {{ $report->Name }}</h3>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <h5 class="card-title">{{ $report->Name }} - {{ $report->category->Name }}</h5>
            </div>
            <div class="mb-3">
                <h4 class="card-text">
                    <pre class="card-text" style="font-family:var(--bs-font-sans-serif);">{{ $report->Description }}</pre>
                </h4>
            </div>
            <br>
            @if (!$details->isEmpty())
                @foreach ($details as $index => $detail)
                    <h4 class="card-text">
                        <pre class="card-text" style="font-family:var(--bs-font-sans-serif);">{{ $detail->Description }}</pre>
                    </h4>
                    @if ($detail->FilePath)
                        <img alt="Gambar Sequence" class="img-fluid" style="max-width: 400px; max-height: 400px;"
                            src="{{ asset('storage/gambar_sequence/' . $detail->FilePath) }}">
                    @endif
                    <br> </br>
                @endforeach
            @endif
        </div>
    </div>
@endsection
