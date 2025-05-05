<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ImageHelper;
use Laravel\Socialite\Facades\Socialite;

class CustomerController extends Controller
{
    // ==================== ADMIN BACKEND METHODS ====================
    
    /**
     * Menampilkan daftar customer
     */
    public function index()
    {
        $customers = Customer::with('user')
            ->orderBy('id', 'desc')
            ->get();
            
        return view('backend.v_customer.index', [
            'judul' => 'Manajemen Customer',
            'sub' => 'Daftar Customer',
            'customers' => $customers
        ]);
    }

    /**
     * Menampilkan detail customer
     */
    public function show($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        
        return view('backend.v_customer.show', [
            'judul' => 'Detail Customer',
            'sub' => 'Informasi Lengkap Customer',
            'customer' => $customer
        ]);
    }

    /**
     * Menampilkan form tambah customer
     */
    public function create()
    {
        return view('backend.v_customer.create', [
            'judul' => 'Tambah Customer',
            'sub' => 'Form Registrasi Customer Baru'
        ]);
    }

    /**
     * Menyimpan data customer baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'hp' => 'required|max:15',
            'alamat' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024'
        ]);

        // Buat user baru
        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make('password123'), // Password default
            'role' => 'customer',
            'status' => 1
        ]);

        // Handle upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = ImageHelper::uploadCustomerPhoto($request->file('foto'));
        }

        // Buat data customer
        Customer::create([
            'user_id' => $user->id,
            'hp' => $validated['hp'],
            'alamat' => $validated['alamat'],
            'foto' => $fotoPath
        ]);

        return redirect()->route('backend.customer.index')
            ->with('success', 'Customer berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit customer
     */
    public function edit($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        
        return view('backend.v_customer.edit', [
            'judul' => 'Edit Customer',
            'sub' => 'Form Edit Data Customer',
            'customer' => $customer
        ]);
    }

    /**
     * Update data customer
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        
        $rules = [
            'nama' => 'required|max:255',
            'hp' => 'required|max:15',
            'alamat' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024'
        ];

        if ($request->email != $customer->user->email) {
            $rules['email'] = 'required|email|unique:users,email';
        }

        $validated = $request->validate($rules);

        // Update user data
        $customer->user->update([
            'nama' => $validated['nama'],
            'email' => $request->email ?? $customer->user->email
        ]);

        // Handle foto
        $fotoPath = $customer->foto;
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($fotoPath) {
                Storage::delete('public/img-customer/'.$fotoPath);
            }
            $fotoPath = ImageHelper::uploadCustomerPhoto($request->file('foto'));
        }

        // Update customer data
        $customer->update([
            'hp' => $validated['hp'],
            'alamat' => $validated['alamat'],
            'foto' => $fotoPath
        ]);

        return redirect()->route('backend.customer.index')
            ->with('success', 'Data customer berhasil diperbarui');
    }

    /**
     * Hapus data customer
     */
    public function destroy($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        
        // Hapus foto jika ada
        if ($customer->foto) {
            Storage::delete('public/img-customer/'.$customer->foto);
        }
        
        // Hapus user terkait
        $customer->user->delete();
        
        // Hapus customer
        $customer->delete();

        return redirect()->route('backend.customer.index')
            ->with('success', 'Customer berhasil dihapus');
    }

    // ==================== FRONTEND CUSTOMER METHODS ====================
    
    /**
     * Tampilkan halaman akun customer
     */
    public function akun($id)
    {
        // dd('masuk');
         // Verifikasi bahwa user yang login sesuai dengan yang diakses
    if (Auth::id() != $id) {
        abort(403, 'Unauthorized action.');
    }

        $customer = Customer::with('user')
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        return view('v_customer.akun', [
            'judul' => 'Akun Saya',
            'sub' => 'Kelola Profil Anda',
            'customer' => $customer
        ]);
    }

    /**
     * Update data akun customer
     */
    public function updateAkun(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'hp' => 'required|string|max:15',
        'alamat' => 'required|string|max:255',
        'pos' => 'required|string|max:10',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = User::findOrFail($id);
    $user->nama = $request->input('nama');
    $user->email = $request->input('email');
    $user->hp = $request->input('hp');
    $user->alamat = $request->input('alamat');
    $user->pos = $request->input('pos');

    // Handle foto upload
    if ($request->hasFile('foto')) {
        // Ambil file yang diupload
        $foto = $request->file('foto');
    
        // Tentukan path untuk menyimpan foto
        $fotoPath = $foto->store('img-customer', 'public');
    
        // Simpan path file ke dalam database
        $user->foto = $fotoPath;
    }
    

    $user->save();

    return redirect()->route('customer.akun', ['id' => Auth::user()->id])->with('success', 'Akun berhasil diperbarui');
}

    // ==================== AUTHENTICATION METHODS ====================
    
    /**
     * Redirect ke Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'nama' => $googleUser->getName(),
                    'password' => Hash::make(uniqid()),
                    'role' => 'customer',
                    'status' => 1
                ]
            );

            // Buat atau update data customer
            Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'google_id' => $googleUser->getId(),
                    'hp' => null,
                    'alamat' => null
                ]
            );

            Auth::login($user);

            return redirect()->intended('/beranda');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google: '.$e->getMessage());
        }
    }

    /**
     * Logout customer
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah logout');
    }
}