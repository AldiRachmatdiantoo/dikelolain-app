<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Memproses permintaan untuk mengubah kata sandi pengguna yang sedang login.
     */
    public function updatePassword(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            // Password::defaults() memastikan minimal 8 karakter.
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ], [
            // Pesan error kustom untuk pengguna Indonesia
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password baru.',
        ]);

        // 2. Verifikasi Password Saat Ini
        // Membandingkan input 'current_password' dengan hash password di database.
        if (!Hash::check($validated['current_password'], $request->user()->password)) {
            // Jika tidak cocok, lempar ValidationException
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini salah. Mohon periksa kembali.',
            ]);
        }

        // 3. Update Password
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // 4. Redirect dengan Pesan Sukses
        // Menggunakan back() agar kembali ke halaman formulir, lalu mengirim pesan 'status'.
        return back()->with('status', 'Password berhasil diubah!');
    }
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'unique:users,email,' . $request->user()->id memastikan email unik, kecuali email milik user ini sendiri
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        $request->user()->fill($validated);

        // Jika email diubah, kirim verifikasi email baru (opsional)
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return back()->with('status', 'Detail profil berhasil diperbarui.');
    }
}
