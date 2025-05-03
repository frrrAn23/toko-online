<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Helpers\ImageHelper;

class CustomerController extends Controller
{
    public function index()
    {
        $customer = Customer::orderBy('id', 'desc')->get();
        return view('backend.v_customer.index', [
            'judul' => 'Customer',
            'sub' => 'Halaman Customer',
            'index' => $customer
        ]);
    }

    public function show($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        return view('backend.v_customer.show', [
            'judul' => 'Detail Customer',
            'sub' => 'Halaman Detail Customer',
            'show' => $customer
        ]);
    }

    public function create()
    {
        return view('backend.v_customer.create', [
            'judul' => 'Tambah Customer',
            'sub' => 'Halaman Tambah Customer'
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|max:255',
            'email' => 'required|max:255|email|unique:users',
            'hp' => 'required|min:10|max:13',
            'alamat' => 'required',
            'foto' => 'image|mimes:jpeg,jpg,png,gif|file|max:1024',
        ], [
            'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto.max' => 'Ukuran file gambar Maksimal adalah 1024 KB.'
        ]);

        // Buat user baru
        $user = User::create([
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'role' => '2', // Role customer
            'status' => 1, // Status aktif
            'password' => Hash::make('password123'), // Password default
        ]);

        // Handle upload foto
        $fotoName = null;
        if ($request->file('foto')) {
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $fotoName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'storage/img-customer/';
            ImageHelper::uploadAndResize($file, $directory, $fotoName, 385, 400);
        }

        // Buat data customer
        Customer::create([
            'user_id' => $user->id,
            'hp' => $validatedData['hp'],
            'alamat' => $validatedData['alamat'],
            'foto' => $fotoName,
        ]);

        return redirect()->route('backend.customer.index')->with('success', 'Data customer berhasil ditambahkan');
    }

    public function edit($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        return view('backend.v_customer.edit', [
            'judul' => 'Edit Customer',
            'sub' => 'Halaman Edit Customer',
            'edit' => $customer
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        
        $rules = [
            'nama' => 'required|max:255',
            'hp' => 'required|min:10|max:13',
            'alamat' => 'required',
            'foto' => 'image|mimes:jpeg,jpg,png,gif|file|max:1024',
        ];

        if ($request->email != $customer->user->email) {
            $rules['email'] = 'required|max:255|email|unique:users';
        }

        $validatedData = $request->validate($rules, [
            'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto.max' => 'Ukuran file gambar Maksimal adalah 1024 KB.'
        ]);

        // Update data user
        $customer->user->update([
            'nama' => $validatedData['nama'],
            'email' => $request->email ?? $customer->user->email,
        ]);

        // Handle upload foto baru
        if ($request->file('foto')) {
            // Hapus foto lama jika ada
            if ($customer->foto) {
                $oldImagePath = public_path('storage/img-customer/') . $customer->foto;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $fotoName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'storage/img-customer/';
            ImageHelper::uploadAndResize($file, $directory, $fotoName, 385, 400);
            $validatedData['foto'] = $fotoName;
        }

        // Update data customer
        $customer->update([
            'hp' => $validatedData['hp'],
            'alamat' => $validatedData['alamat'],
            'foto' => $validatedData['foto'] ?? $customer->foto,
        ]);

        return redirect()->route('backend.customer.index')->with('success', 'Data customer berhasil diperbarui');
    }

    public function destroy($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        
        // Hapus foto jika ada
        if ($customer->foto) {
            $oldImagePath = public_path('storage/img-customer/') . $customer->foto;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Hapus user terkait
        $customer->user->delete();
        
        // Hapus customer
        $customer->delete();

        return redirect()->route('backend.customer.index')->with('success', 'Data customer berhasil dihapus');
    }

    // Fungsi untuk Google OAuth (tetap sama seperti sebelumnya)
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->stateless()->user();

            // Cek apakah email sudah terdaftar 
            $registeredUser = User::where('email', $socialUser->email)->first(); 
 
            if (!$registeredUser) { 
                // Buat user baru 
                $user = User::create([ 
                    'nama' => $socialUser->name, 
                    'email' => $socialUser->email, 
                    'role' => '2', // Role customer 
                    'status' => 1, // Status aktif 
                    'password' => Hash::make('default_password'), // Password default (opsional) 
                ]); 
 
                // Buat data customer 
                Customer::create([ 
                    'user_id' => $user->id, 
                    'google_id' => $socialUser->id, 
                    'google_token' => $socialUser->token,
                    'hp' => null, // Atau nilai default
                'alamat' => null // Jika diperlukan
                ]); 
 
                // Login pengguna baru 
                Auth::login($user); 
            } else { 
                // Jika email sudah terdaftar, langsung login 
                Auth::login($registeredUser); 
            } 

            return redirect()->intended('beranda');
        } catch (\Exception $e) {
            Log::error('Detail Error Google OAuth:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => request()->all()
            ]);
        
            return redirect('/')
                ->with('error', 'Gagal login dengan Google: '.$e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Logout pengguna 
        $request->session()->invalidate(); // Hapus session 
        $request->session()->regenerateToken(); // Regenerate token CSRF 

        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }
}