<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-medium">Judul Laporan <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" required
               placeholder="cth: Pipa bocor di depan rumah Wayan Ardika"
               value="{{ old('title') }}">
    </div>

    <div class="col-md-8">
        <label class="form-label fw-medium">Lokasi <span class="text-danger">*</span></label>
        <input type="text" name="location" class="form-control" required
               placeholder="Jalan, gang, atau nama titik lokasi"
               value="{{ old('location') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-medium">Tanggal Lapor <span class="text-danger">*</span></label>
        <input type="date" name="reported_date" class="form-control" required
               value="{{ old('reported_date', now()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
        <select name="category" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="pipa_bocor">Pipa Bocor</option>
            <option value="meteran_rusak">Meteran Rusak</option>
            <option value="pompa">Pompa</option>
            <option value="reservoir">Reservoir</option>
            <option value="instalasi_baru">Instalasi Baru</option>
            <option value="lainnya">Lainnya</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-medium">Prioritas <span class="text-danger">*</span></label>
        <select name="priority" class="form-select" required>
            <option value="rendah">Rendah</option>
            <option value="sedang" selected>Sedang</option>
            <option value="tinggi">Tinggi</option>
            <option value="darurat">Darurat</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-medium">Zona</label>
        <select name="zone_id" class="form-select">
            <option value="">— Pilih Zona —</option>
            @foreach ($zones as $z)
                <option value="{{ $z->id }}">{{ $z->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-medium">Biaya Material</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text" class="form-control input-ribuan" inputmode="numeric" autocomplete="off"
                   placeholder="0" data-target="material_cost">
            <input type="hidden" name="material_cost">
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-medium">Biaya Tenaga</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text" class="form-control input-ribuan" inputmode="numeric" autocomplete="off"
                   placeholder="0" data-target="labor_cost">
            <input type="hidden" name="labor_cost">
        </div>
    </div>

    <div class="col-12">
        <label class="form-label fw-medium">Deskripsi</label>
        <textarea name="description" class="form-control" rows="3"
                  placeholder="Detail kerusakan, kondisi, atau catatan penting...">{{ old('description') }}</textarea>
    </div>
</div>
