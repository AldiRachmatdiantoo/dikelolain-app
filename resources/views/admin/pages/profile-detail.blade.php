@extends('admin.layouts.admin')

@section('title', 'Detail Profil & Ubah Password')
@section('page-title', 'Pengaturan Akun')

@section('content')
    <div class="profile-container">
        <div class="profile-card">

            {{-- Header dengan Tombol Ubah Password --}}
            <div class="header-with-button">
                <h2>Detail Pengguna</h2>

                {{-- Tombol Navigasi ke Halaman Ubah Password --}}
                {{-- Menggunakan route('admin.change-password') agar lebih sesuai dengan nama view --}}
                <a href="{{ route('admin.reset-password') }}" class="btn-primary-custom" id="resetPasswordButton">
                    <i class="fa-solid fa-key"></i> Ubah Password
                </a>
            </div>

            {{-- Formulir untuk Update Nama dan Email --}}
            <form method="POST" action="{{ route('admin.profile.update') }}">
                @if (session('status'))
                    <div class="alert-success">
                        <i class="fa-solid fa-check-circle"></i> {{ session('status') }}
                    </div>
                @endif
                @csrf
                {{-- Menggunakan PUT/PATCH untuk update data --}}
                @method('PATCH')

                <div class="detail-section">

                    {{-- Field Nama --}}
                    <div class="form-group-edit">
                        <label for="name" class="detail-label">Nama</label>
                        <div class="detail-input-group">
                            <input id="name" type="text" name="name"
                                value="{{ old('name', Auth::user()->name ?? 'Nama Pengguna') }}" required autofocus>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Field Email --}}
                    <div class="form-group-edit">
                        <label for="email" class="detail-label">Email</label>
                        <div class="detail-input-group">
                            <input id="email" type="email" name="email"
                                value="{{ old('email', Auth::user()->email ?? 'user@example.com') }}" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary-custom btn-save">
                    <i class="fa-solid fa-save"></i> SIMPAN PROFIL
                </button>
            </form>
        </div>
    </div>

@endsection

