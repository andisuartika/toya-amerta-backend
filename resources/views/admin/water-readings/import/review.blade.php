@extends('layouts.vertical', ['title' => 'Review Import'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Review &amp; Konfirmasi Import</h4>
            <p class="text-muted mb-0 fs-13">
                Periode: <strong>{{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}</strong>
                — {{ count($rows) }} baris terbaca dari file
            </p>
        </div>
        <a href="{{ route('admin.water-readings.import.create') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i data-feather="arrow-left" style="width:14px;height:14px;" class="me-1"></i>Upload Ulang
        </a>
    </div>

    <div class="alert alert-warning py-2 fs-13">
        <i data-feather="alert-triangle" style="width:14px;height:14px;" class="me-1"></i>
        Periksa kolom <strong>Pelanggan (Sistem)</strong> untuk setiap baris. Baris bertanda merah belum cocok otomatis — pilih pelanggannya secara manual atau hilangkan centang untuk melewati baris tersebut.
    </div>

    @if ($biayaAdmin || $pdamPayment)
        <div class="alert alert-info py-2 fs-13">
            <i data-feather="info" style="width:14px;height:14px;" class="me-1"></i>
            @if ($biayaAdmin)
                Biaya Admin: <strong>Rp {{ number_format($biayaAdmin, 0, ',', '.') }}</strong>
            @endif
            @if ($biayaAdmin && $pdamPayment) &mdash; @endif
            @if ($pdamPayment)
                Pembayaran ke PDAM Pusat: <strong>Rp {{ number_format($pdamPayment, 0, ',', '.') }}</strong>
            @endif
            akan otomatis tercatat saat data ini disimpan.
        </div>
    @endif

    <form method="POST" action="{{ route('admin.water-readings.import.store') }}">
        @csrf
        <input type="hidden" name="year" value="{{ $year }}">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="biaya_admin" value="{{ $biayaAdmin }}">
        <input type="hidden" name="pdam_payment" value="{{ $pdamPayment }}">

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:30px;"><i data-feather="check-square" style="width:14px;height:14px;"></i></th>
                                <th>Nama (Excel)</th>
                                <th>Pelanggan (Sistem)</th>
                                <th class="text-end">Meter Awal</th>
                                <th class="text-end">Meter Akhir</th>
                                <th class="text-end">Pemakaian</th>
                                <th class="text-end">Harga/m3</th>
                                <th class="text-end">Total</th>
                                <th>Status Bayar</th>
                                <th>Jumlah Dibayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $i => $row)
                                <tr class="{{ ! $row['matched_customer_id'] ? 'table-danger' : ($row['already_recorded'] ? 'table-secondary' : '') }}">
                                    <td>
                                        <input type="hidden" name="rows[{{ $i }}][name]" value="{{ $row['name'] }}">
                                        <input type="hidden" name="rows[{{ $i }}][previous_reading]" value="{{ $row['previous_reading'] }}">
                                        <input type="hidden" name="rows[{{ $i }}][current_reading]" value="{{ $row['current_reading'] }}">
                                        <input type="hidden" name="rows[{{ $i }}][price_per_m3]" value="{{ $row['price_per_m3'] }}">
                                        <input type="hidden" name="rows[{{ $i }}][total_amount]" value="{{ $row['total_amount'] }}">
                                        <input type="checkbox" class="form-check-input" name="rows[{{ $i }}][include]" value="1"
                                               {{ ($row['matched_customer_id'] && ! $row['already_recorded']) ? 'checked' : '' }}>
                                    </td>
                                    <td class="fs-13">{{ $row['name'] }}</td>
                                    <td>
                                        <select name="rows[{{ $i }}][customer_id]" class="form-select form-select-sm">
                                            <option value="">-- Pilih Pelanggan --</option>
                                            @foreach ($customers as $c)
                                                <option value="{{ $c->id }}" {{ $row['matched_customer_id'] == $c->id ? 'selected' : '' }}>
                                                    {{ $c->customer_number }} – {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if (! $row['matched_customer_id'] && $row['suggestions']->isNotEmpty())
                                            <div class="form-text fs-11">
                                                Mirip: @foreach ($row['suggestions'] as $s) {{ $s['customer']->name }} ({{ $s['score'] }}%){{ ! $loop->last ? ',' : '' }} @endforeach
                                            </div>
                                        @endif
                                        @if ($row['already_recorded'])
                                            <div class="form-text text-warning fs-11">Sudah ada pencatatan periode ini — akan dilewati otomatis.</div>
                                        @endif
                                    </td>
                                    <td class="text-end fs-13">{{ number_format($row['previous_reading'], 2) }}</td>
                                    <td class="text-end fs-13">{{ number_format($row['current_reading'], 2) }}</td>
                                    <td class="text-end fs-13">{{ number_format($row['usage_m3'], 2) }}</td>
                                    <td class="text-end fs-13">Rp {{ number_format($row['price_per_m3'], 0, ',', '.') }}</td>
                                    <td class="text-end fs-13">Rp {{ number_format($row['total_amount'], 0, ',', '.') }}</td>
                                    <td>
                                        <select name="rows[{{ $i }}][payment_status]" class="form-select form-select-sm select-status" data-index="{{ $i }}">
                                            <option value="lunas" selected>Lunas</option>
                                            <option value="sebagian">Sebagian</option>
                                            <option value="belum_bayar">Belum Bayar</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm input-ribuan input-amount-paid"
                                               inputmode="numeric" autocomplete="off"
                                               data-index="{{ $i }}" data-target="#amountPaid{{ $i }}"
                                               value="{{ number_format($row['total_amount'], 0, ',', '.') }}" style="display:none;">
                                        <input type="hidden" name="rows[{{ $i }}][amount_paid]" id="amountPaid{{ $i }}" value="{{ $row['total_amount'] }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="fs-13 text-muted">
                Total baris: {{ count($rows) }} —
                Cocok otomatis: {{ collect($rows)->whereNotNull('matched_customer_id')->count() }} —
                Perlu dipilih manual: {{ collect($rows)->where('matched_customer_id', null)->count() }}
            </div>
            <button type="submit" class="btn btn-primary px-4">
                <i data-feather="save" style="width:15px;height:15px;" class="me-1"></i>Simpan Data Terpilih
            </button>
        </div>
    </form>

</div>
@endsection

@section('script-bottom')
<script>
    document.querySelectorAll('.select-status').forEach(function (select) {
        select.addEventListener('change', function () {
            toggleAmountPaid(this);
        });
        toggleAmountPaid(select);
    });

    function toggleAmountPaid(select) {
        var index = select.dataset.index;
        var amountInput = document.querySelector('.input-amount-paid[data-index="' + index + '"]');
        amountInput.style.display = select.value === 'sebagian' ? 'block' : 'none';
    }
</script>
@endsection
