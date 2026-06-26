@extends('layouts.public', ['title' => 'Cek Tagihan Air'])

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h4 class="fs-18 fw-semibold mb-1">Cek Tagihan Air</h4>
        <p class="text-muted fs-13 mb-4">Masukkan nomor pelanggan Anda untuk melihat riwayat pencatatan meter dan status tagihan.</p>

        @if (session('error'))
            <div class="alert alert-danger fs-13">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('public.cek.search') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Nomor Pelanggan</label>
                <input type="text" name="customer_number" class="form-control form-control-lg @error('customer_number') is-invalid @enderror"
                       placeholder="cth: PDAM-2026-0001" value="{{ old('customer_number') }}" autofocus>
                @error('customer_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Cek Tagihan</button>
        </form>
    </div>
</div>
@endsection
