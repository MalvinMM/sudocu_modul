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
            <form method="post" action="{{ route('pswChange', $id) }}">
                @csrf
                <div class="form-group">
                    <label for="old_psw">Old Password</label>
                    <input type="password" class="form-control" id="old_psw" aria-describedby="old_psw"
                        placeholder="Enter Previous Password" name="old_psw">
                    @error('old_psw')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="new_psw">New Password</label>
                    <input type="password" class="form-control" id="new_psw" placeholder="New Password" name="new_psw">
                    @error('new_psw')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="new_psw2">Confirm New Password</label>
                    <input type="password" class="form-control" id="new_psw2" placeholder="Confirm New Password"
                        name="new_psw2">
                    @error('new_psw2')
                        <h6 class="form-helper" style="color:red">{{ $message }}</h6>
                    @enderror
                </div>
                <button style="margin-top: 5px" type="submit" class="btn btn-primary">Submit</button>
            </form>
        @endsection
