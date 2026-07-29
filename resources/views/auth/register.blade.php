@extends('layouts.auth')

@section('title')
  Kost Astoria - Register Page
@endsection
@section('content')
<div class="col-lg-6 col-12 p-0">
    <div class="card rounded-0 mb-0 px-2">
        <div class="card-header pb-1">
            <div class="card-title">
                <h4 class="mb-0" style="text-align: center">Silahkan Registrasi Untuk Masuk Kost Astoria</h4>
            </div>
        </div>
        <div class="card-content mb-2">
            <div class="card-body pt-1">
                <form action="{{route('register')}}" method="POST" enctype="multipart/form-data">
                  @csrf
                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" id="name" placeholder="Masukan Nama Kamu ...">
                        @error('name')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                        <div class="form-control-position">
                            <i class="feather icon-pencil"></i>
                        </div>
                    </fieldset>

                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value=" {{old("email")}} " id="email" placeholder="Masukan Email Kamu ...">
                        @error('email')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                        <div class="form-control-position">
                            <i class="feather icon-user"></i>
                        </div>
                    </fieldset>
                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                      <input type="text" name="no_wa" class="form-control @error('no_wa') is-invalid @enderror" value="{{old('no_wa')}}" id="no_wa" placeholder="Masukan Nomor WA Kamu ...">
                      @error('no_wa')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                        <div class="form-control-position">
                            <i class="feather icon-phone"></i>
                        </div>
                    </fieldset>

                    <div class="form-group">
                      <div class="controls">
                        <label for="foto">Foto Profile</label>
                        <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror">
                        @error('foto')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="controls">
                        <label for="foto_ktp">Foto KTP</label>
                        <input type="file" name="foto_ktp" id="foto_ktp" class="form-control @error('foto_ktp') is-invalid @enderror">
                        @error('foto_ktp')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>

                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                          <option value="">-- Mendaftar Sebagai --</option>
                          <option value="Pencari">Pencari Kost</option>
                        </select>
                        @error('role')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                        <div class="form-control-position">
                            <i class="feather icon-user"></i>
                        </div>
                    </fieldset>

                    <fieldset class="form-label-group position-relative has-icon-left">
                        <input type="password" name="password" class="form-control  @error('password') is-invalid @enderror" id="password" placeholder="Password">
                        @error('password')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                        <div class="form-control-position">
                            <i class="feather icon-lock"></i>
                        </div>
                    </fieldset>

                    <fieldset class="form-label-group position-relative has-icon-left">
                        <input type="password" name="password_confirmation" class="form-control  @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="Konfirmasi Password">
                        @error('password_confirmation')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                        <div class="form-control-position">
                            <i class="feather icon-lock"></i>
                        </div>
                    </fieldset>

                    <div class="form-group d-flex justify-content-between align-items-center">
                        <div class="text-left">
</div>
                    <!-- <a href="{{route('login')}}" class="btn btn-outline-primary float-left btn-inline">Login</a> -->
                    <button type="submit" class="btn btn-primary float-right btn-inline">Register</button>
                                        </div>
                </form>
            </div>
        </div>
        <div class="login-footer">
            <div class="divider">
                <div class="divider-text"><i class="feather icon-home"></i></div>
            </div>
            <div class="footer-btn d-inline">
                {{-- <a href="#" class="btn btn-facebook"><span class="fa fa-facebook"></span></a>
                <a href="#" class="btn btn-twitter white"><span class="fa fa-twitter"></span></a>
                <a href="#" class="btn btn-google"><span class="fa fa-google"></span></a>
                <a href="#" class="btn btn-github"><span class="fa fa-github-alt"></span></a> --}}
                 <a href="/login"><h5 style="text-align: center">Kembali</h5></a>
            </div>
        </div>
    </div>
</div>
@endsection