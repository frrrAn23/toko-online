@extends('v_layouts.app')

@section('content')
<!-- template -->

<div class="row">
    <div class="col-md-12">
        <div class="billing-details">
            <div class="section-title">
                <h3 class="title">{{ $judul }}</h3>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- msgError -->
                    @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>{{ session('success') }}</strong>
                    </div>
                    @endif
                    <!-- end msgError -->
                    <!-- msgError -->
                    @if(session()->has('msgError'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>{{ session('msgError') }}</strong>
                    </div>
                    @endif
                    <!-- end msgError -->
                </div>
                <form action="{{ route('customer.akun.update', Auth::user()->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Foto</label>
                            {{-- view image --}}
                            @if (Auth::user()->foto)
                            <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="foto-preview" width="100%">
                            @else
                            <img src="{{ asset('storage/img-user/img-default.jpg') }}" class="foto-preview" width="100%">
                            @endif
                            <p></p>
                            {{-- file foto --}}
                            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" onchange="previewFoto()">
                            @error('foto')
                            <div class="invalid-feedback alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', Auth::user()->nama) }}" placeholder="Masukkan Nama">
                            @error('nama')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" placeholder="Masukkan Email">
                            @error('email')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>No. HP</label>
                            <input type="text" name="hp" class="form-control @error('hp') is-invalid @enderror" value="{{ old('hp', Auth::user()->hp) }}" placeholder="Masukkan Nomor HP" onkeypress="return hanyaAngka(event)">
                            @error('hp')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Alamat</label><br>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', Auth::user()->alamat) }}</textarea>
                            @error('alamat')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Kode Pos</label>
                            <input type="text" name="pos" class="form-control @error('pos') is-invalid @enderror" value="{{ old('pos', Auth::user()->pos) }}" placeholder="Masukkan Nomor Resi">
                            @error('pos')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <br>
                        <div class="pull-right">
                            <button type="submit" class="primary-btn">Simpan Perubahan</button>
                        </div>

                        <div class="pull-left">
                            <a href="{{ route('beranda') }}" class="primary-btn" style="margin-left: 10px;">Kembali ke Beranda</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- end template-->
@endsection