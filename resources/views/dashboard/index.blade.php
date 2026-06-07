@extends('layouts.auth', ['title' => 'Dashboard'])

@section('content')
<div class="col-12 text-center py-5">
    <h2 class="text-primary fw-bold mb-2">Dashboard PDAM Desa</h2>
    <p class="text-muted">Selamat datang, <strong>{{ Auth::user()->name }}</strong>!</p>
    <a href="{{ route('logout') }}"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
       class="btn btn-danger mt-3">
        <i data-feather="log-out" class="me-1" style="width:16px;height:16px;"></i> Keluar
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>
@endsection
