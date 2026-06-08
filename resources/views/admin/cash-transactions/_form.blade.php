<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-medium">Tanggal <span class="text-danger">*</span></label>
        <input type="date" name="transaction_date" class="form-control" required
               value="{{ now()->format('Y-m-d') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-medium">Jenis <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            <option value="masuk">Pemasukan</option>
            <option value="keluar">Pengeluaran</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
        <select name="category" class="form-select" required>
            <option value="">-- Pilih Kategori --</option>
        </select>
        <input type="text" class="form-control mt-2 category-custom" name="category_custom"
               style="display:none" placeholder="Ketik kategori manual..." maxlength="100">
    </div>
    <div class="col-12">
        <label class="form-label fw-medium">Jumlah <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text fw-semibold">Rp</span>
            <input type="text" class="form-control amount-display" inputmode="numeric"
                   autocomplete="off" placeholder="0" required>
            <input type="hidden" name="amount">
        </div>
    </div>
    <div class="col-12">
        <label class="form-label fw-medium">Keterangan</label>
        <input type="text" name="description" class="form-control" placeholder="Opsional">
    </div>
</div>
