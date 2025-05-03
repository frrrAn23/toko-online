@extends('backend.v_layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $judul }}</h4>
                <h6 class="card-subtitle">{{ $sub }}</h6>

                <form action="{{ route('backend.customer.update', $edit->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <!-- Input Nama -->
                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" id="nama" name="nama" 
                                       class="form-control @error('nama') is-invalid @enderror" 
                                       value="{{ old('nama', $edit->user->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Input Email -->
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $edit->user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Input Nomor HP -->
                            <div class="form-group">
                                <label for="hp">Nomor HP</label>
                                <input type="text" id="hp" name="hp" 
                                       class="form-control @error('hp') is-invalid @enderror" 
                                       value="{{ old('hp', $edit->hp) }}" required>
                                @error('hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <!-- Input Alamat -->
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea id="alamat" name="alamat" rows="3" 
                                          class="form-control @error('alamat') is-invalid @enderror" 
                                          required>{{ old('alamat', $edit->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Input Status -->
                            <div class="form-group">
                                <label for="status">Status Akun</label>
                                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="1" {{ old('status', $edit->user->status) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status', $edit->user->status) == 0 ? 'selected' : '' }}>Non-Aktif</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Input Foto -->
                            <div class="form-group">
                                <label for="foto">Foto Profil</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('foto') is-invalid @enderror" 
                                           id="foto" name="foto" accept="image/*">
                                    <label class="custom-file-label" for="foto">Pilih file...</label>
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Format: JPEG, JPG, PNG (Maks. 1MB)
                                </small>
                                
                                <!-- Preview Foto -->
                                @if($edit->foto)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/img-customer/' . $edit->foto) }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 150px; max-height: 150px;"
                                         id="foto-preview">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" 
                                               id="hapus_foto" name="hapus_foto">
                                        <label class="form-check-label text-danger" for="hapus_foto">
                                            Hapus foto saat disimpan
                                        </label>
                                    </div>
                                </div>
                                @else
                                <div class="mt-2">
                                    <img src="{{ asset('images/default-profile.png') }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 150px; max-height: 150px;"
                                         id="foto-preview">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('backend.customer.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Preview gambar sebelum upload
    document.getElementById('foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('foto-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
            document.querySelector('.custom-file-label').textContent = file.name;
        }
    });

    // Validasi form sebelum submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const hpInput = document.getElementById('hp');
        const hpValue = hpInput.value.trim();
        
        // Validasi nomor HP
        if (hpValue.length < 10 || hpValue.length > 13) {
            e.preventDefault();
            alert('Nomor HP harus antara 10-13 digit');
            hpInput.focus();
        }
    });
</script>
@endsection