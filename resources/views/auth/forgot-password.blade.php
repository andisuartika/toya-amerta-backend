@extends('layouts.auth', ['title' => 'Lupa Password'])

@section('content')

<div class="col-xl-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="mb-0 p-0 p-lg-3">
                        <div class="mb-0 border-0 p-md-4 p-lg-0">

                            {{-- Logo --}}
                            <div class="mb-4 text-center">
                                <div class="auth-brand">
                                    <a href="{{ url('/') }}" class="logo logo-light">
                                        <span class="logo-lg">
                                            <img src="/images/logo-light.png" alt="Toya Amerta" height="28">
                                        </span>
                                    </a>
                                    <a href="{{ url('/') }}" class="logo logo-dark">
                                        <span class="logo-lg">
                                            <img src="/images/logo-dark.png" alt="Toya Amerta" height="28">
                                        </span>
                                    </a>
                                </div>
                            </div>

                            {{-- Title --}}
                            <div class="auth-title-section mb-4 text-center">
                                <div class="avatar-md mx-auto mb-3">
                                    <div class="avatar-title bg-soft-primary rounded-circle">
                                        <i data-feather="lock" style="width:28px;height:28px;" class="text-primary"></i>
                                    </div>
                                </div>
                                <h3 class="text-primary fw-semibold mb-1">Reset Password</h3>
                                <p class="text-muted fs-14 mb-0">
                                    Masukkan email terdaftar Anda untuk mendapatkan tautan reset password.
                                </p>
                            </div>

                            <div class="pt-0">

                                @if (session('status'))
                                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                        <i data-feather="check-circle" class="me-1" style="width:16px;height:16px;"></i>
                                        {{ session('status') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('password.email') }}" class="my-4">
                                    @csrf

                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Alamat Email</label>
                                        <input class="form-control @error('email') is-invalid @enderror"
                                               type="email" name="email" id="email"
                                               value="{{ old('email') }}"
                                               required autofocus
                                               placeholder="Masukkan email terdaftar Anda">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-0">
                                        <div class="d-grid">
                                            <button class="btn btn-primary fw-semibold" type="submit">
                                                <i data-feather="send" class="me-1" style="width:16px;height:16px;"></i>
                                                Kirim Link Reset Password
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div class="text-center text-muted mt-3">
                                    <p class="mb-0">
                                        Ingat password Anda?
                                        <a class="text-primary ms-1 fw-medium" href="{{ route('login') }}">Kembali Login</a>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Right Panel --}}
<div class="col-xl-7 d-none d-xl-inline-block">
    <div class="account-page-bg rounded-4">
        <div class="text-center p-5">
            <div class="mb-4">
                <h2 class="text-white fw-bold">Sistem PDAM Desa</h2>
                <p class="text-white-50 fs-15">
                    Platform digital pengelolaan air bersih desa yang modern,<br>
                    transparan, dan mudah digunakan.
                </p>
            </div>
            <div class="auth-image">
                <img src="/images/auth-images.svg" class="mx-auto img-fluid" alt="PDAM Desa" style="max-height: 380px;">
            </div>
        </div>
    </div>
</div>

@endsection
