@extends('layouts.auth', ['title' => 'Login'])

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
                                <a href="{{ url('/') }}">
                                    <img src="/images/logo-dark.png" alt="Toya Amerta" height="70">
                                </a>
                            </div>

                            {{-- Title --}}
                            <div class="auth-title-section mb-4 text-center">
                                <h3 class="text-primary fw-semibold mb-1">Selamat Datang!</h3>
                                <p class="text-muted fs-14 mb-0">Masuk ke Sistem PDAM Toya Amerta</p>
                            </div>

                            {{-- Form --}}
                            <div class="pt-0">
                                <form method="POST" action="{{ route('login') }}" class="my-4">
                                    @csrf

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                            <i data-feather="alert-circle" class="me-1" style="width:16px;height:16px;"></i>
                                            {{ $errors->first() }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    @if (session('status'))
                                        <div class="alert alert-success mb-3">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Alamat Email</label>
                                        <input class="form-control @error('email') is-invalid @enderror"
                                               type="email" name="email" id="email"
                                               value="{{ old('email') }}"
                                               required autofocus
                                               placeholder="Masukkan email Anda">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input class="form-control @error('password') is-invalid @enderror"
                                                   type="password" name="password" id="password"
                                                   required placeholder="Masukkan password Anda">
                                            <button class="btn btn-outline-secondary" type="button" id="toggle-password" tabindex="-1">
                                                <i data-feather="eye" style="width:16px;height:16px;"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group d-flex mb-3">
                                        <div class="col-sm-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="remember"
                                                       id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="remember">Ingat saya</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 text-end">
                                            <a class="text-muted fs-14" href="{{ route('password.request') }}">Lupa password?</a>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <div class="d-grid">
                                            <button class="btn btn-primary fw-semibold" type="submit">
                                                <i data-feather="log-in" class="me-1" style="width:16px;height:16px;"></i>
                                                Masuk
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                {{-- Info Box --}}
                                <div class="alert alert-soft-info text-center mt-2 mb-0 py-2">
                                    <small class="text-muted">
                                        Hubungi administrator jika Anda mengalami masalah saat login.
                                    </small>
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
            <div class="row mt-4 text-white">
                <div class="col-4">
                    <div class="p-3">
                        <iconify-icon icon="solar:waterdrop-bold-duotone" class="fs-28 text-white"></iconify-icon>
                        <p class="mb-0 fs-13">Pencatatan<br>Meter Digital</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3">
                        <iconify-icon icon="solar:chat-round-bold-duotone" class="fs-28 text-white"></iconify-icon>
                        <p class="mb-0 fs-13">Notifikasi<br>WhatsApp</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3">
                        <iconify-icon icon="solar:graph-up-bold-duotone" class="fs-28 text-white"></iconify-icon>
                        <p class="mb-0 fs-13">Laporan<br>Keuangan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
<script>
    // Toggle password visibility
    document.getElementById('toggle-password')?.addEventListener('click', function () {
        const pwd = document.getElementById('password');
        const icon = this.querySelector('[data-feather]');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.setAttribute('data-feather', 'eye-off');
        } else {
            pwd.type = 'password';
            icon.setAttribute('data-feather', 'eye');
        }
        feather.replace();
    });
</script>
@endsection
