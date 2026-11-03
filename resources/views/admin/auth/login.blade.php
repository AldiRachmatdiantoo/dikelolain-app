<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dikelolain VCMS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-dikelolain.png') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login-form.css') }}?v={{ filemtime(public_path('css/login-form.css')) }}">
</head>

<body>

    <div class="login-card">
        <div class="text-center mb-5">
            <img src="{{ asset('images/logo-dikelolain-potrait.webp') }}" alt="Dikelolain Logo" class="login-logo">
            <h1 class="h3 text-uppercase fw-light login-title">LOGIN</h1>
            <p class="text-muted">Akses ke Panel Admin</p>
        </div>
        @if (session('error'))
            <div class="alert alert-danger text-center" role="alert">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ route('admin.handle-login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email"
                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                    placeholder="Masukkan alamat email" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password"
                    class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi"
                    required>
            </div>
            <div class="form-check mb-5">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label text-muted" for="remember">
                    Ingat Saya
                </label>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-dark-accent">
                    L O G I N
                </button>
            </div>
        </form>
        @if (Route::has('password.request'))
            <div class="text-center mt-4">
                <a href="{{ route('password.request') }}" class="small">Lupa Password?</a>
            </div>
        @endif

    </div>

</body>

</html>
