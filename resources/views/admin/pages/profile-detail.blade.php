@extends('admin.layouts.admin')

@section('title', 'Detail Profil & Ubah Password')
@section('page-title', 'Pengaturan Akun')

@section('content')
    <style>
        /* Styling Khusus untuk Profil di Dashboard Admin */
        :root {
            --primary-color: #FFCC01;
            --background-color: #161616;
            --text-light: #FFFFFF;
            --card-bg: #222222;
            --border-color: #444444;
        }

        .profile-container {
            width: 100%;
            max-width: 800px;
            margin: 40px auto;
        }

        .profile-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
            color: var(--text-light);
        }

        /* Container untuk Judul dan Tombol */
        .header-with-button {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }

        .header-with-button h2 {
            color: var(--primary-color);
            margin: 0;
            padding-bottom: 0;
            border-bottom: none;
            font-size: 1.8em;
        }

        /* Formulir dan Detail */
        .detail-section {
            margin-top: 15px;
        }

        .form-group-edit {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .form-group-edit:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: bold;
            color: var(--primary-color);
            text-transform: uppercase;
            width: 150px;
            /* Lebar tetap untuk label */
            flex-shrink: 0;
        }

        .detail-input-group {
            flex-grow: 1;
            margin-left: 20px;
        }

        .detail-input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            background-color: var(--background-color);
            color: var(--text-light);
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Tombol Kustom */
        .btn-primary-custom {
            background-color: var(--primary-color);
            color: var(--background-color);
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            transition: background-color 0.3s ease;
            width: auto;
            font-size: 0.9em;
            display: inline-block;
            margin-top: 20px;
            /* Jarak untuk tombol simpan */
        }

        .btn-primary-custom:hover {
            background-color: #e6b800;
        }

        .btn-save {
            width: 200px;
        }

        .text-danger {
            color: #cc0000;
            font-size: 0.9em;
            margin-top: 5px;
            display: block;
        }
    </style>

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

@push('scripts')
    {{-- JavaScript di sini --}}
@endpush
