<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts.partials.title-meta', ['title' => $title ?? 'Cek Tagihan'])
    @include('layouts.partials.head-css')
</head>

<body>
    <div class="d-flex flex-column min-vh-100 bg-light">
        <header class="bg-white border-bottom py-3">
            <div class="container" style="max-width:680px;">
                <a href="{{ route('public.cek.index') }}" class="d-flex align-items-center text-decoration-none">
                    <img src="/images/logo-sm.png" alt="Toya Amerta" height="32" class="me-2">
                    <span class="fw-semibold fs-16 text-dark">PDAM Desa Sangket</span>
                </a>
            </div>
        </header>

        <main class="flex-grow-1 py-4">
            <div class="container" style="max-width:680px;">
                @yield('content')
            </div>
        </main>

        <footer class="text-center text-muted fs-12 py-3">
            &copy; {{ date('Y') }} Toya Amerta — PDAM Desa Sangket
        </footer>
    </div>

    @include('layouts.partials.vendor')
</body>

</html>
