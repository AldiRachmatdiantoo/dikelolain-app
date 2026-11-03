@extends('admin.layouts.admin')

@section('title', 'Atur Ulang Kata Sandi')
@section('page-title', 'Keamanan Akun')

@section('content')
<style>
    /* Styling Khusus untuk Formulir Ubah Password */
    :root {
        --primary-color: #FFCC01;
        --background-color: #161616;
        --text-light: #FFFFFF;
        --card-bg: #222222;
        --border-color: #444444;
    }

    .change-password-container {
        width: 100%;
        max-width: 500px;
        margin: 40px auto; /* Tambahkan margin agar ada jarak di dalam konten admin */
    }

    .password-card {
        background-color: var(--card-bg);
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        color: var(--text-light);
    }

    h2 {
        color: var(--primary-color);
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 10px;
        margin-bottom: 25px;
        font-size: 1.8em;
    }

    /* Formulir Ubah Password */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-light);
        font-weight: bold;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color);
        background-color: var(--background-color);
        color: var(--text-light);
        border-radius: 6px;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    .form-group input:focus {
        border-color: var(--primary-color);
        outline: none;
    }

    /* Tombol */
    .btn-primary-custom {
        background-color: var(--primary-color);
        color: var(--background-color);
        border: none;
        padding: 12px 25px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        text-transform: uppercase;
        transition: background-color 0.3s ease;
        width: 100%;
        margin-top: 15px;
    }

    .btn-primary-custom:hover {
        background-color: #e6b800;
    }

    /* Notifikasi dan Error */
    .alert-success {
        background-color: #38761d;
        color: var(--text-light);
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .text-danger {
        color: #cc0000;
        font-size: 0.9em;
        margin-top: 5px;
        display: block;
    }
</style>

<div class="change-password-container">
    <div class="password-card">
        <h2>Atur Ulang Kata Sandi Anda</h2>

        {{-- Menampilkan Status (Pesan Sukses/Gagal) --}}
        @if (session('status'))
            <div class="alert-success">
                <i class="fa-solid fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        {{-- Formulir Ubah Password --}}
        <form method="POST" action="{{ route('admin.password.update.dashboard') }}">
            @csrf
            {{-- Menggunakan PUT sesuai convention untuk update data --}}
            @method('PUT') 

            {{-- 1. Field Password Saat Ini --}}
            <div class="form-group">
                <label for="current_password">Password Saat Ini</label>
                <input id="current_password" type="password" name="current_password" required autocomplete="current-password" placeholder="Masukkan password Anda saat ini">
                @error('current_password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- 2. Field Password Baru --}}
            <div class="form-group">
                <label for="password">Password Baru</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Masukkan password baru (min. 8 karakter)">
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- 3. Field Konfirmasi Password Baru --}}
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru Anda">
            </div>

            <button type="submit" class="btn-primary-custom">
                <i class="fa-solid fa-key"></i> UBAH PASSWORD
            </button>
        </form>
    </div>
</div>
@endsection