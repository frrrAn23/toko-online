@extends('v_layouts.app')

@section('content')
<div class="container">
    <h1>{{ $judul }}</h1>
    
    <form action="{{ route('customer.akun.update', Auth::user()->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" 
                   value="{{ old('nama', $customer->user->nama) }}">
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" 
                   value="{{ old('email', $customer->user->email) }}">
        </div>
        
        <div class="form-group">
            <label>No. HP</label>
            <input type="text" name="hp" class="form-control" 
                   value="{{ old('hp', $customer->hp) }}">
        </div>
        
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control">{{ old('alamat', $customer->alamat) }}</textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection