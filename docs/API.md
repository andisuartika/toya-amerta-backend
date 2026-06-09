# Toya Amerta — Dokumentasi REST API Mobile

Dokumen ini menjelaskan seluruh endpoint API yang dapat dikonsumsi oleh aplikasi Flutter.

---

## Informasi Umum

| Item | Keterangan |
|------|-----------|
| Base URL | `https://your-domain.com/api` |
| Auth | Bearer Token (Laravel Sanctum) |
| Format Request | `application/json` |
| Format Response | `application/json` |
| Token Expire | 30 hari sejak login |

### Struktur Response

Semua response menggunakan envelope yang konsisten:

```json
{
  "success": true,
  "message": "Keterangan singkat",
  "data": { ... },
  "meta": { ... }
}
```

| Field | Tipe | Keterangan |
|-------|------|-----------|
| `success` | boolean | `true` jika request berhasil |
| `message` | string | Pesan ringkas untuk ditampilkan |
| `data` | object \| array \| null | Payload utama |
| `meta` | object \| null | Informasi tambahan (total, pagination, filter aktif) |

### Error Response

| HTTP Status | Kondisi |
|-------------|---------|
| 401 | Token tidak ada, tidak valid, atau sudah kedaluwarsa |
| 403 | Role tidak memiliki akses ke endpoint ini |
| 404 | Data tidak ditemukan |
| 422 | Validasi input gagal — `data` berisi detail error per field |
| 500 | Kesalahan internal server |

Contoh error 422:
```json
{
  "success": false,
  "message": "Data tidak valid.",
  "data": {
    "current_reading": ["The current reading field is required."]
  },
  "meta": null
}
```

---

## Role Pengguna

| Role | Deskripsi |
|------|-----------|
| `pelanggan` | Hanya bisa melihat tagihan & riwayat pemakaian milik sendiri |
| `petugas` | Input meter, konfirmasi bayar, laporan maintenance |
| `admin` | Akses penuh (termasuk semua endpoint petugas) |

---

## 1. Auth

### 1.1 Login

```
POST /api/auth/login
```

Tidak memerlukan token. Mengembalikan Bearer token untuk request berikutnya.

**Request Body**

```json
{
  "email": "petugas@toya.desa.id",
  "password": "rahasia123"
}
```

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|-----------|
| `email` | string | Ya | Email terdaftar |
| `password` | string | Ya | Password akun |

**Response 200 — Berhasil**

```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "user": {
      "id": 3,
      "name": "Budi Santoso",
      "email": "petugas@toya.desa.id",
      "phone": "081234567890",
      "role": "petugas"
    }
  },
  "meta": null
}
```

**Response 422 — Kredensial salah**

```json
{
  "success": false,
  "message": "Data tidak valid.",
  "data": {
    "email": ["Email atau password tidak sesuai."]
  },
  "meta": null
}
```

**Response 403 — Akun nonaktif**

```json
{
  "success": false,
  "message": "Akun Anda tidak aktif. Hubungi administrator.",
  "data": null,
  "meta": null
}
```

---

### 1.2 Profil User (Me)

```
GET /api/auth/me
```

**Header**

```
Authorization: Bearer {token}
```

**Response 200 — Role petugas / admin**

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 3,
    "name": "Budi Santoso",
    "email": "petugas@toya.desa.id",
    "phone": "081234567890",
    "role": "petugas"
  },
  "meta": null
}
```

**Response 200 — Role pelanggan** (terdapat field tambahan `customer`)

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 10,
    "name": "Wayan Karya",
    "email": "wayan@gmail.com",
    "phone": "082233445566",
    "role": "pelanggan",
    "customer": {
      "id": 5,
      "customer_number": "PLG-0005",
      "name": "Wayan Karya",
      "address": "Banjar Kaja No. 12",
      "zone": "Zona A",
      "tariff": "Tarif Rumah Tangga"
    }
  },
  "meta": null
}
```

---

### 1.3 Logout

```
POST /api/auth/logout
```

Menghapus token yang sedang digunakan.

**Response 200**

```json
{
  "success": true,
  "message": "Logout berhasil.",
  "data": null,
  "meta": null
}
```

---

## 2. Pelanggan

> Semua endpoint di bagian ini hanya bisa diakses oleh role **`pelanggan`**.

---

### 2.1 Profil Pelanggan

```
GET /api/pelanggan/profile
```

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 5,
    "customer_number": "PLG-0005",
    "name": "Wayan Karya",
    "address": "Banjar Kaja No. 12",
    "phone": "082233445566",
    "zone": "Zona A",
    "tariff_name": "Tarif Rumah Tangga",
    "price_per_m3": 2500.00,
    "minimum_charge": 15000.00,
    "minimum_usage": 5.00,
    "installation_date": "2022-03-15",
    "is_active": true
  },
  "meta": null
}
```

---

### 2.2 Tagihan Belum Lunas

```
GET /api/pelanggan/tagihan
```

Mengembalikan semua tagihan dengan status `belum_bayar` atau `sebagian` milik pelanggan yang sedang login.

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 42,
      "period_year": 2026,
      "period_month": 5,
      "period_label": "Mei 2026",
      "reading_date": "2026-05-03",
      "previous_reading": 120.50,
      "current_reading": 132.80,
      "usage_m3": 12.30,
      "total_amount": 30750.00,
      "amount_paid": 0.00,
      "remaining_amount": 30750.00,
      "payment_status": "belum_bayar"
    }
  ],
  "meta": {
    "total": 1
  }
}
```

| Field | Keterangan |
|-------|-----------|
| `payment_status` | `belum_bayar` \| `sebagian` |
| `remaining_amount` | Sisa tagihan yang belum dibayar |

---

### 2.3 Riwayat Pemakaian

```
GET /api/pelanggan/riwayat?limit=12
```

**Query Parameter**

| Parameter | Tipe | Default | Keterangan |
|-----------|------|---------|-----------|
| `limit` | integer | `12` | Jumlah data diambil, maksimal `24` |

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 42,
      "period_year": 2026,
      "period_month": 5,
      "period_label": "Mei 2026",
      "reading_date": "2026-05-03",
      "previous_reading": 120.50,
      "current_reading": 132.80,
      "usage_m3": 12.30,
      "total_amount": 30750.00,
      "amount_paid": 30750.00,
      "payment_status": "lunas"
    },
    {
      "id": 38,
      "period_year": 2026,
      "period_month": 4,
      "period_label": "April 2026",
      "reading_date": "2026-04-04",
      "previous_reading": 109.20,
      "current_reading": 120.50,
      "usage_m3": 11.30,
      "total_amount": 28250.00,
      "amount_paid": 28250.00,
      "payment_status": "lunas"
    }
  ],
  "meta": {
    "total": 2,
    "limit": 12
  }
}
```

| `payment_status` | Keterangan |
|-----------------|-----------|
| `belum_bayar` | Belum ada pembayaran |
| `sebagian` | Sudah bayar sebagian |
| `lunas` | Tagihan lunas |

---

## 3. Petugas

> Semua endpoint di bagian ini hanya bisa diakses oleh role **`petugas`** atau **`admin`**.

---

### 3.1 Daftar Pelanggan Aktif

```
GET /api/petugas/customers
```

Digunakan untuk mengisi dropdown saat input meter.

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 5,
      "customer_number": "PLG-0005",
      "name": "Wayan Karya",
      "address": "Banjar Kaja No. 12",
      "zone": "Zona A",
      "tariff": "Tarif Rumah Tangga",
      "initial_meter": 100.00
    }
  ],
  "meta": {
    "total": 45
  }
}
```

---

### 3.2 Daftar Pembacaan Meter

```
GET /api/petugas/water-readings?year=2026&month=6
```

**Query Parameter**

| Parameter | Tipe | Default | Keterangan |
|-----------|------|---------|-----------|
| `year` | integer | tahun sekarang | Filter tahun periode |
| `month` | integer | bulan sekarang | Filter bulan periode (1–12) |

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 55,
      "customer_id": 5,
      "customer_name": "Wayan Karya",
      "customer_number": "PLG-0005",
      "zone": "Zona A",
      "officer_name": "Budi Santoso",
      "period_year": 2026,
      "period_month": 6,
      "period_label": "Juni 2026",
      "reading_date": "2026-06-03",
      "previous_reading": 132.80,
      "current_reading": 145.20,
      "usage_m3": 12.40,
      "price_per_m3": 2500.00,
      "minimum_charge": 15000.00,
      "total_amount": 31000.00,
      "payment_status": "belum_bayar",
      "notes": null
    }
  ],
  "meta": {
    "year": 2026,
    "month": 6,
    "total": 1
  }
}
```

---

### 3.3 Catat Meter Baru

```
POST /api/petugas/water-readings
```

**Request Body**

```json
{
  "customer_id": 5,
  "period_year": 2026,
  "period_month": 6,
  "current_reading": 145.20,
  "reading_date": "2026-06-03",
  "notes": "Meteran normal"
}
```

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|-----------|
| `customer_id` | integer | Ya | ID pelanggan |
| `period_year` | integer | Ya | Tahun periode (min 2000) |
| `period_month` | integer | Ya | Bulan periode (1–12) |
| `current_reading` | float | Ya | Angka meter saat ini |
| `reading_date` | string (Y-m-d) | Ya | Tanggal catat meter |
| `notes` | string | Tidak | Catatan tambahan |

> **Catatan:** Sistem otomatis menghitung `previous_reading`, `usage_m3`, dan `total_amount` berdasarkan tarif pelanggan. Jika pelanggan sudah dicatat pada periode yang sama, request akan ditolak (422).

**Response 201 — Berhasil**

```json
{
  "success": true,
  "message": "Pembacaan meter berhasil dicatat.",
  "data": {
    "id": 55,
    "customer_id": 5,
    "customer_name": "Wayan Karya",
    "customer_number": "PLG-0005",
    "zone": "Zona A",
    "officer_name": "Budi Santoso",
    "period_year": 2026,
    "period_month": 6,
    "period_label": "Juni 2026",
    "reading_date": "2026-06-03",
    "previous_reading": 132.80,
    "current_reading": 145.20,
    "usage_m3": 12.40,
    "price_per_m3": 2500.00,
    "minimum_charge": 15000.00,
    "total_amount": 31000.00,
    "payment_status": "belum_bayar",
    "notes": "Meteran normal"
  },
  "meta": null
}
```

---

### 3.4 Detail Pembacaan Meter

```
GET /api/petugas/water-readings/{id}
```

**Response 200** — Struktur sama dengan item pada [3.3](#33-catat-meter-baru).

---

### 3.5 Tagihan Belum Bayar

```
GET /api/petugas/tagihan?zone_id=1&year=2026&month=6
```

**Query Parameter**

| Parameter | Tipe | Keterangan |
|-----------|------|-----------|
| `zone_id` | integer | Filter zona |
| `year` | integer | Filter tahun periode |
| `month` | integer | Filter bulan periode |

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 55,
      "customer_id": 5,
      "customer_name": "Wayan Karya",
      "customer_number": "PLG-0005",
      "zone": "Zona A",
      "period_year": 2026,
      "period_month": 6,
      "period_label": "Juni 2026",
      "total_amount": 31000.00,
      "remaining_amount": 31000.00,
      "payment_status": "belum_bayar"
    }
  ],
  "meta": {
    "total": 1
  }
}
```

---

### 3.6 Konfirmasi Pembayaran

```
POST /api/petugas/payments
```

**Request Body**

```json
{
  "water_reading_id": 55,
  "amount_paid": 31000,
  "payment_date": "2026-06-09",
  "payment_method": "tunai",
  "notes": "Bayar lunas"
}
```

| Field | Tipe | Wajib | Nilai Valid | Keterangan |
|-------|------|-------|------------|-----------|
| `water_reading_id` | integer | Ya | — | ID tagihan yang dibayar |
| `amount_paid` | float | Ya | > 0 | Jumlah yang dibayar |
| `payment_date` | string (Y-m-d) | Ya | — | Tanggal bayar |
| `payment_method` | string | Ya | `tunai`, `transfer`, `qris` | Metode pembayaran |
| `notes` | string | Tidak | — | Catatan |

> **Catatan:** Sistem mendukung pembayaran sebagian (cicilan). `amount_paid` tidak boleh melebihi sisa tagihan. Setelah konfirmasi, transaksi kas masuk otomatis tercatat.

**Response 201 — Berhasil**

```json
{
  "success": true,
  "message": "Pembayaran berhasil dikonfirmasi.",
  "data": {
    "id": 30,
    "receipt_number": "KWT-20260609-0001",
    "water_reading_id": 55,
    "customer_id": 5,
    "period_label": "Juni 2026",
    "amount_paid": 31000.00,
    "payment_date": "2026-06-09",
    "payment_method": "tunai",
    "status": "lunas",
    "recorded_by": "Budi Santoso",
    "notes": "Bayar lunas"
  },
  "meta": null
}
```

| `status` | Keterangan |
|---------|-----------|
| `sebagian` | Pembayaran sebagian, tagihan belum lunas |
| `lunas` | Tagihan lunas setelah pembayaran ini |

---

### 3.7 Detail Pembayaran

```
GET /api/petugas/payments/{id}
```

**Response 200** — Struktur sama dengan item pada [3.6](#36-konfirmasi-pembayaran).

---

### 3.8 Daftar Laporan Maintenance

```
GET /api/petugas/maintenance?status=dilaporkan&priority=darurat
```

**Query Parameter**

| Parameter | Tipe | Nilai Valid | Keterangan |
|-----------|------|------------|-----------|
| `status` | string | `dilaporkan`, `dalam_proses`, `selesai`, `ditunda` | Filter status |
| `priority` | string | `rendah`, `sedang`, `tinggi`, `darurat` | Filter prioritas |
| `category` | string | Lihat tabel kategori | Filter kategori |
| `zone_id` | integer | — | Filter zona |

**Kategori Maintenance**

| Nilai | Label |
|-------|-------|
| `pipa_bocor` | Pipa Bocor |
| `meteran_rusak` | Meteran Rusak |
| `pompa` | Pompa |
| `reservoir` | Reservoir |
| `instalasi_baru` | Instalasi Baru |
| `lainnya` | Lainnya |

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 8,
      "title": "Pipa bocor di Jl. Raya Desa",
      "location": "Jl. Raya Desa No. 45",
      "category": "pipa_bocor",
      "category_label": "Pipa Bocor",
      "priority": "tinggi",
      "priority_label": "Tinggi",
      "status": "dilaporkan",
      "status_label": "Dilaporkan",
      "zone": "Zona B",
      "customer_name": null,
      "reported_by": "Budi Santoso",
      "reported_date": "2026-06-08",
      "handled_date": null,
      "completed_date": null,
      "description": "Air merembes dari sambungan pipa bawah tanah.",
      "material_cost": null,
      "labor_cost": null,
      "total_cost": 0
    }
  ],
  "meta": {
    "total": 1
  }
}
```

---

### 3.9 Buat Laporan Maintenance

```
POST /api/petugas/maintenance
```

**Request Body**

```json
{
  "title": "Pipa bocor di Jl. Raya Desa",
  "location": "Jl. Raya Desa No. 45",
  "category": "pipa_bocor",
  "priority": "tinggi",
  "reported_date": "2026-06-08",
  "zone_id": 2,
  "customer_id": null,
  "description": "Air merembes dari sambungan pipa bawah tanah.",
  "material_cost": null,
  "labor_cost": null
}
```

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|-----------|
| `title` | string | Ya | Judul singkat laporan |
| `location` | string | Ya | Lokasi kejadian |
| `category` | string | Ya | Lihat tabel kategori di [3.8](#38-daftar-laporan-maintenance) |
| `priority` | string | Ya | `rendah`, `sedang`, `tinggi`, `darurat` |
| `reported_date` | string (Y-m-d) | Ya | Tanggal laporan |
| `zone_id` | integer | Tidak | ID zona terdampak |
| `customer_id` | integer | Tidak | ID pelanggan terkait (jika ada) |
| `description` | string | Tidak | Deskripsi detail |
| `material_cost` | float | Tidak | Estimasi biaya material |
| `labor_cost` | float | Tidak | Estimasi biaya tenaga kerja |

**Response 201** — Struktur sama dengan item pada [3.8](#38-daftar-laporan-maintenance).

---

### 3.10 Detail Maintenance

```
GET /api/petugas/maintenance/{id}
```

**Response 200** — Struktur sama dengan item pada [3.8](#38-daftar-laporan-maintenance).

---

### 3.11 Update Status Maintenance

```
PATCH /api/petugas/maintenance/{id}/status
```

**Request Body**

```json
{
  "status": "selesai",
  "material_cost": 150000,
  "labor_cost": 100000,
  "notes": "Pipa sudah diganti dan tersegel."
}
```

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|-----------|
| `status` | string | Ya | `dilaporkan`, `dalam_proses`, `selesai`, `ditunda` |
| `material_cost` | float | Tidak | Biaya material aktual |
| `labor_cost` | float | Tidak | Biaya tenaga kerja aktual |
| `notes` | string | Tidak | Catatan penyelesaian |

> **Catatan:** Saat status berubah ke `dalam_proses`, `handled_date` otomatis diisi. Saat `selesai`, `completed_date` diisi dan biaya otomatis tercatat ke kas keluar.

**Response 200**

```json
{
  "success": true,
  "message": "Status maintenance berhasil diperbarui.",
  "data": {
    "id": 8,
    "title": "Pipa bocor di Jl. Raya Desa",
    "status": "selesai",
    "status_label": "Selesai",
    "handled_date": "2026-06-08",
    "completed_date": "2026-06-09",
    "material_cost": 150000.00,
    "labor_cost": 100000.00,
    "total_cost": 250000.00,
    "...": "field lainnya"
  },
  "meta": null
}
```

---

## Alur Penggunaan Tipikal

### Petugas — Input Meter & Terima Bayar

```
1. POST /api/auth/login             → dapat token
2. GET  /api/petugas/customers      → ambil daftar pelanggan
3. POST /api/petugas/water-readings → catat meter pelanggan
4. GET  /api/petugas/tagihan        → lihat tagihan belum bayar
5. POST /api/petugas/payments       → konfirmasi pembayaran
```

### Pelanggan — Cek Tagihan

```
1. POST /api/auth/login             → dapat token
2. GET  /api/auth/me                → cek profil + nomor pelanggan
3. GET  /api/pelanggan/tagihan      → lihat tagihan aktif
4. GET  /api/pelanggan/riwayat      → lihat histori pemakaian
```

### Petugas — Laporan Maintenance

```
1. POST  /api/auth/login                          → dapat token
2. POST  /api/petugas/maintenance                 → buat laporan
3. PATCH /api/petugas/maintenance/{id}/status     → update ke dalam_proses
4. PATCH /api/petugas/maintenance/{id}/status     → update ke selesai + isi biaya
```

---

## Contoh Header Request

```http
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: application/json
Accept: application/json
```
