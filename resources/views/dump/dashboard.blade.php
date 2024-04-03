@extends('template.dash-template')

@section('dash-content')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Profile</h1>
            <a href="{{ route('landing') }}">landing</a>
            <a href="{{ route('logout') }}">logout</a>
        </div>
        <div>
            <style>
                th {

                    background: #000000;
                }

                tr:hover {
                    background-color: #808080;
                }
            </style>
            <div class="container px-4">
                <div class="row gx-2">
                    {{-- <div class="col-sm-3 col-md-4">
            <img src="{{ asset('storage/foto_member/'.$member->fotoMember) }}" class="img-fluid rounded">
          </div> --}}
                    <div class="col">
                        <dl class="row">
                            <dt class="col-sm-3">Nama</dt>
                            <dd class="col-sm-9">{{ auth()->user()->FullName }}</dd>

                            <dt class="col-sm-3">NIK</dt>
                            <dd class="col-sm-9">{{ auth()->user()->NIK }}</dd>

                            <dt class="col-sm-3">Password</dt>
                            <a class="col-sm-9" href="{{ route('showPswChange') }}">Change Password</a>

                        </dl>
                        {{-- <div class="modal fade" id="delete_member_subs" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="{{ route('stop_subscription',Auth::user()->id) }}" method="POST">
                        @csrf
                        @method('delete')
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Hapus Subscription?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                              <input type="hidden" name="subs_delete_member" id="id_subs">
                            Apakah anda yakin ingin menghapus pembelian subscription? Anda masih dapat menikmati benefit dari subscription yang sudah dibeli sampai batas akhir subscription.
                          </div>
                          <div class="modal-footer">
                              <button id="btnDeleteMember" type="submit" class="btn btn-danger">Iya</button></a>
                              <a href="/dashboard-admin/member" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</a>
                          </div>
                    </form>
                  </div>
                </div>
              </div> --}}
                    </div>
                </div>
            </div>
        </div>
    @endsection
