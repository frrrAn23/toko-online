@extends('backend.v_layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">{{ $judul }}</h4>
                    <a href="{{ route('backend.customer.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="row">
                    <!-- Kolom Foto Profil -->
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            @if($show->foto)
                                <img src="{{ asset('storage/img-customer/' . $show->foto) }}" 
                                     class="img-fluid rounded-circle" 
                                     style="width: 250px; height: 250px; object-fit: cover;"
                                     alt="Foto Profil">
                            @else
                                <img src="{{ asset('backend/assets/images/users/2.jpg') }}" 
                                     class="img-fluid rounded-circle" 
                                     style="width: 250px; height: 250px; object-fit: cover;"
                                     alt="Foto Default">
                            @endif
                        </div>
                    </div>

                    <!-- Kolom Data Customer -->
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th width="30%">Nama Lengkap</th>
                                <td>{{ $show->user->nama }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $show->user->email }}</td>
                            </tr>
                            <tr>
                                <th>Nomor HP</th>
                                <td>{{ $show->hp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>{{ $show->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status Akun</th>
                                <td>
                                    @if($show->user->status == 1)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Non-Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Daftar</th>
                                <td>{{ $show->created_at->format('d F Y H:i') }}</td>
                            </tr>
                            @if($show->google_id)
                            <tr>
                                <th>Login dengan Google</th>
                                <td>
                                    <span class="badge bg-info">Ya</span>
                                    <small class="text-muted">(ID: {{ $show->google_id }})</small>
                                </td>
                            </tr>
                            @endif
                        </table>

                        <div class="mt-4">
                            <a href="{{ route('backend.customer.edit', $show->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .table th {
        background-color: #f8f9fa;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endsection