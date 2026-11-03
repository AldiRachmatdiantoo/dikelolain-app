@extends('admin.layouts.admin')

@section('title', 'Atur Ulang Kata Sandi')
@section('page-title', 'Keamanan Akun')

@section('content')
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