<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.title-meta', ['title' => $title ?? 'Dashboard'])
    @include('layouts.partials.head-css')
</head>

<body data-menu-color="dark" data-sidebar="default">

<div id="app-layout">

    @include('layouts.partials.topbar')
    @include('layouts.partials.sidebar')

    <div class="content-page">
        <div class="content">

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
                    <i data-feather="check-circle" class="me-1" style="width:16px;height:16px;"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
                    <i data-feather="alert-circle" class="me-1" style="width:16px;height:16px;"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')

        </div>

        @include('layouts.partials.footer')
    </div>

</div>

@include('layouts.partials.vendor')

</body>
</html>
