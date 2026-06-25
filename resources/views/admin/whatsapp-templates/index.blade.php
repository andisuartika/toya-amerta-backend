@extends('layouts.vertical', ['title' => 'Template Pesan WhatsApp'])

@section('content')
<div class="container-fluid">

    <div class="py-3">
        <h4 class="fs-18 fw-semibold m-0">Template Pesan WhatsApp</h4>
        <p class="text-muted mb-0 fs-13">Atur format pesan WA yang dikirim otomatis ke pelanggan via Fonnte.</p>
    </div>

    <div class="row">
        {{-- Template Tagihan --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="fs-15 fw-semibold mb-1">
                        <i data-feather="file-text" style="width:16px;height:16px;" class="me-1"></i>
                        Tagihan Air Baru
                    </h5>
                    <p class="text-muted fs-13 mb-3">Dikirim otomatis saat petugas mencatat meter pelanggan.</p>

                    <form method="POST" action="{{ route('admin.whatsapp-templates.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="tagihan">

                        <textarea name="template" class="form-control font-monospace fs-13" rows="14"
                                  id="templateTagihan">{{ old('template', $tagihan->template) }}</textarea>
                        @error('template')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror

                        <div class="mt-3">
                            <p class="fw-medium fs-13 mb-1">Placeholder yang bisa dipakai:</p>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach (['{nama}', '{no_pelanggan}', '{periode}', '{meter_lalu}', '{meter_ini}', '{pemakaian}', '{harga_m3}', '{total}'] as $ph)
                                    <code class="badge bg-secondary-subtle text-secondary-emphasis fw-normal placeholder-chip" style="cursor:pointer" data-target="templateTagihan" data-ph="{{ $ph }}">{{ $ph }}</code>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 mt-3">Simpan Template Tagihan</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Template Konfirmasi Bayar --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="fs-15 fw-semibold mb-1">
                        <i data-feather="check-circle" style="width:16px;height:16px;" class="me-1"></i>
                        Konfirmasi Pembayaran
                    </h5>
                    <p class="text-muted fs-13 mb-3">Dikirim otomatis saat petugas konfirmasi pembayaran pelanggan.</p>

                    <form method="POST" action="{{ route('admin.whatsapp-templates.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="konfirmasi_bayar">

                        <textarea name="template" class="form-control font-monospace fs-13" rows="14"
                                  id="templateKonfirmasi">{{ old('template', $konfirmasiBayar->template) }}</textarea>
                        @error('template')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror

                        <div class="mt-3">
                            <p class="fw-medium fs-13 mb-1">Placeholder yang bisa dipakai:</p>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach (['{nama}', '{no_kwitansi}', '{periode}', '{jumlah_bayar}', '{status}', '{sisa_block}'] as $ph)
                                    <code class="badge bg-secondary-subtle text-secondary-emphasis fw-normal placeholder-chip" style="cursor:pointer" data-target="templateKonfirmasi" data-ph="{{ $ph }}">{{ $ph }}</code>
                                @endforeach
                            </div>
                            <p class="text-muted fs-12 mt-2 mb-0">
                                <code>{sisa_block}</code> otomatis terisi baris "Sisa Tagihan: Rp ..." jika pembayaran sebagian,
                                dan kosong jika sudah lunas.
                            </p>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 mt-3">Simpan Template Konfirmasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script-bottom')
<script>
(function () {
    document.querySelectorAll('.placeholder-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var textarea = document.getElementById(chip.dataset.target);
            var start = textarea.selectionStart ?? textarea.value.length;
            var end = textarea.selectionEnd ?? textarea.value.length;
            var text = textarea.value;
            textarea.value = text.slice(0, start) + chip.dataset.ph + text.slice(end);
            textarea.focus();
            textarea.selectionStart = textarea.selectionEnd = start + chip.dataset.ph.length;
        });
    });

    if (typeof feather !== 'undefined') feather.replace();
}());
</script>
@endsection
