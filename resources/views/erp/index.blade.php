@extends('template.dashboard')

@section('title')
    <h3 class="nav-link">ERP Menu {{ $erp->Initials }}</h3>
@endsection
@section('content')
    <h5>{{ Breadcrumbs::render() }}</h5>

    <div class="row">
        @if (auth()->user()->Role == 'PIC')
            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('masterTable', $erp->Initials) }}" class="card-link">
                    <div class="card overflow-hidden rounded-2">
                        <div class="card-body pt-5 pb-5 p-4 d-flex flex-column align-items-center justify-content-center">
                            <h5 class="card-title fw-semibold">Table Documentation</h5>
                            {{-- <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold fs-4 mb-0">$50 <span
                                class="ms-2 fw-normal text-muted fs-3"></span></h6>
                    </div> --}}
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('masterView', $erp->Initials) }}" class="card-link">
                    <div class="card overflow-hidden rounded-2">
                        <div class="card-body pt-5 pb-5 p-4 d-flex flex-column align-items-center justify-content-center">
                            <h5 class="card-title fw-semibold">DB View Documentation</h5>
                            {{-- <div class="d-flex align-items-center justify-content-between">
                            <h6 class="fw-semibold fs-4 mb-0">$50 <span
                                    class="ms-2 fw-normal text-muted fs-3"></span></h6>
                        </div> --}}
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('masterFunction', $erp->Initials) }}" class="card-link">
                    <div class="card overflow-hidden rounded-2">
                        <div class="card-body pt-5 pb-5 p-4 d-flex flex-column align-items-center justify-content-center">
                            <h5 class="card-title fw-semibold">DB Function</h5>
                            {{-- <div class="d-flex align-items-center justify-content-between">
                            <h6 class="fw-semibold fs-4 mb-0">$50 <span
                                    class="ms-2 fw-normal text-muted fs-3"></span></h6>
                        </div> --}}
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('masterStoreProc', $erp->Initials) }}" class="card-link">
                    <div class="card overflow-hidden rounded-2">
                        <div class="card-body pt-5 pb-5 p-4 d-flex flex-column align-items-center justify-content-center">
                            <h5 class="card-title fw-semibold">DB Store Procedure</h5>
                            {{-- <div class="d-flex align-items-center justify-content-between">
                            <h6 class="fw-semibold fs-4 mb-0">$50 <span
                                    class="ms-2 fw-normal text-muted fs-3"></span></h6>
                        </div> --}}
                        </div>
                    </div>
                </a>
            </div>
        @endif
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('masterModule', $erp->Initials) }}" class="card-link">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-body pt-5 pb-5 p-4 d-flex flex-column align-items-center justify-content-center">
                        <h5 class="card-title fw-semibold">Module Documentation</h5>
                        {{-- <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold fs-4 mb-0">$50 <span
                                class="ms-2 fw-normal text-muted fs-3"></span></h6>
                    </div> --}}
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('masterReport', $erp->Initials) }}" class="card-link">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-body pt-5 pb-5 p-4 d-flex flex-column align-items-center justify-content-center">
                        <h5 class="card-title fw-semibold">Report Documentation</h5>
                        {{-- <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold fs-4 mb-0">$50 <span
                                class="ms-2 fw-normal text-muted fs-3"></span></h6>
                    </div> --}}
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="#" class="card-link">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-body pt-5 pb-5 p-4 d-flex flex-column align-items-center justify-content-center">
                        <h5 class="card-title fw-semibold">Notification</h5>
                        {{-- <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold fs-4 mb-0">$50 <span
                                class="ms-2 fw-normal text-muted fs-3"></span></h6>
                    </div> --}}
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection
