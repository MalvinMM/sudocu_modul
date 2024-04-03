@extends('template.dashboard')
@section('title')
    <h3 class="nav-link">Dashboard</h3>
@endsection
@section('content')
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
                <div class="card-body p-4" style="margin-top: 1cm">
                    <div class="row mx-auto">
                        @if (auth()->user()->Role != 'Admin')
                            @foreach (auth()->user()->erps as $erp)
                                <div class="col-sm-6 col-xl-6">
                                    <a href="{{ route('erpMenu', $erp->Initials) }}" class="card-link">
                                        <div class="card overflow-hidden rounded-2">
                                            <div
                                                class="card-body pt-3 pb-3 p-4 d-flex flex-column align-items-center justify-content-center">
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
                        @else
                            @foreach ($erps as $erp)
                                <div class="col-sm-6 col-xl-6">
                                    <a href="{{ route('erpMenu', $erp->Initials) }}" class="card-link">
                                        <div class="card overflow-hidden rounded-2">
                                            <div
                                                class="card-body pt-3 pb-3 p-4 d-flex flex-column align-items-center justify-content-center">
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
