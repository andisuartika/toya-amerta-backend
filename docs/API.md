# Toya Amerta — Dokumentasi REST API Mobile

Dokumen ini menjelaskan seluruh endpoint API yang dapat dikonsumsi oleh aplikasi Flutter.

---

## Informasi Umum

| Item            | Keterangan                     |
| --------------- | ------------------------------ |
| Base URL        | `https://your-domain.com/api`  |
| Auth            | Bearer Token (Laravel Sanctum) |
| Format Request  | `application/json`             |
| Format Response | `application/json`             |
| Token Expire    | 30 hari sejak login            |

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

| Field     | Tipe                    | Keterangan                                           |
| --------- | ----------------------- | ---------------------------------------------------- |
| `success` | boolean                 | `true` jika request berhasil                         |
| `message` | string                  | Pesan ringkas untuk ditampilkan                      |
| `data`    | object \| array \| null | Payload utama                                        |
| `meta`    | object \| null          | Informasi tambahan (total, pagination, filter aktif) |

### Error Response

| HTTP Status | Kondisi                                                     |
| ----------- | ----------------------------------------------------------- |
| 401         | Token tidak ada, tidak valid, atau sudah kedaluwarsa        |
| 403         | Role tidak memiliki akses ke endpoint ini                   |
| 404         | Data tidak ditemukan                                        |
| 422         | Validasi input gagal — `data` berisi detail error per field |
| 500         | Kesalahan internal server                                   |

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

| Role        | Deskripsi                                                    |
| ----------- | ------------------------------------------------------------ |
| `pelanggan` | Hanya bisa melihat tagihan & riwayat pemakaian milik sendiri |
| `petugas`   | Input meter, konfirmasi bayar, laporan maintenance           |
| `admin`     | Akses penuh (termasuk semua endpoint petugas)                |

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

| Field      | Tipe   | Wajib | Keterangan      |
| ---------- | ------ | ----- | --------------- |
| `email`    | string | Ya    | Email terdaftar |
| `password` | string | Ya    | Password akun   |

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
      "role": "petugas",
      "photo_url": null
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
    "role": "petugas",
    "photo_url": null
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
    "photo_url": "https://your-domain.com/storage/avatars/abc123.jpg",
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

### 1.3 Update Profil

```
POST /api/auth/profile
```

> **Content-Type:** `multipart/form-data` jika menyertakan foto, boleh `application/json` jika tanpa foto.

Semua field opsional — kirim hanya field yang ingin diubah.

**Request Body**

```json
{
  "name": "Budi Santoso",
  "phone": "081234567890",
  "email": "budi@toya.desa.id",
  "photo": "(file binary, opsional)",
  "current_password": "rahasia123",
  "password": "rahasiabaru123",
  "password_confirmation": "rahasiabaru123"
}
```

| Field                   | Tipe                | Wajib                             | Keterangan                            |
| ----------------------- | ------------------- | --------------------------------- | ------------------------------------- |
| `name`                  | string              | Tidak                             | Nama pengguna                         |
| `phone`                 | string              | Tidak                             | No. telepon                           |
| `email`                 | string              | Tidak                             | Harus unik (dicek terhadap user lain) |
| `photo`                 | file (jpg/png/webp) | Tidak                             | Foto profil, maksimal 2MB             |
| `current_password`      | string              | **Wajib jika mengganti password** | Password saat ini, untuk verifikasi   |
| `password`              | string              | Tidak                             | Password baru, minimal 6 karakter     |
| `password_confirmation` | string              | **Wajib jika mengisi `password`** | Harus sama dengan `password`          |

**Response 200 — Berhasil**

```json
{
  "success": true,
  "message": "Profil berhasil diperbarui.",
  "data": {
    "id": 3,
    "name": "Budi Santoso",
    "email": "budi@toya.desa.id",
    "phone": "081234567890",
    "role": "petugas",
    "photo_url": "https://your-domain.com/storage/avatars/abc123.jpg"
  },
  "meta": null
}
```

**Response 422 — Password saat ini salah**

```json
{
  "success": false,
  "message": "Data tidak valid.",
  "data": {
    "current_password": ["Password saat ini tidak sesuai."]
  },
  "meta": null
}
```

---

### 1.4 Logout

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
    "price_per_m3": 2500.0,
    "minimum_charge": 15000.0,
    "minimum_usage": 5.0,
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
      "previous_reading": 120.5,
      "current_reading": 132.8,
      "usage_m3": 12.3,
      "total_amount": 30750.0,
      "amount_paid": 0.0,
      "remaining_amount": 30750.0,
      "payment_status": "belum_bayar"
    }
  ],
  "meta": {
    "total": 1
  }
}
```

| Field              | Keterangan                      |
| ------------------ | ------------------------------- |
| `payment_status`   | `belum_bayar` \| `sebagian`     |
| `remaining_amount` | Sisa tagihan yang belum dibayar |

---

### 2.3 Riwayat Pemakaian

```
GET /api/pelanggan/riwayat?limit=12
```

**Query Parameter**

| Parameter | Tipe    | Default | Keterangan                         |
| --------- | ------- | ------- | ---------------------------------- |
| `limit`   | integer | `12`    | Jumlah data diambil, maksimal `24` |

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
      "previous_reading": 120.5,
      "current_reading": 132.8,
      "usage_m3": 12.3,
      "total_amount": 30750.0,
      "amount_paid": 30750.0,
      "payment_status": "lunas"
    },
    {
      "id": 38,
      "period_year": 2026,
      "period_month": 4,
      "period_label": "April 2026",
      "reading_date": "2026-04-04",
      "previous_reading": 109.2,
      "current_reading": 120.5,
      "usage_m3": 11.3,
      "total_amount": 28250.0,
      "amount_paid": 28250.0,
      "payment_status": "lunas"
    }
  ],
  "meta": {
    "total": 2,
    "limit": 12
  }
}
```

| `payment_status` | Keterangan           |
| ---------------- | -------------------- |
| `belum_bayar`    | Belum ada pembayaran |
| `sebagian`       | Sudah bayar sebagian |
| `lunas`          | Tagihan lunas        |

---

## 3. Petugas

> Semua endpoint di bagian ini hanya bisa diakses oleh role **`petugas`** atau **`admin`**.

---

### 3.1 Ringkasan Dashboard

```
GET /api/petugas/dashboard?year=2026&month=6
```

Mengembalikan statistik ringkas untuk halaman dashboard petugas: jumlah pelanggan aktif, jumlah yang sudah/belum dicatat pada periode tertentu, jumlah tagihan belum bayar, total kas yang terkumpul pada bulan tersebut, serta daftar gabungan pelanggan sudah dicatat dan belum dicatat (untuk tab "Sudah Dicatat" / "Belum Dicatat").

**Query Parameter**

| Parameter | Tipe    | Default        | Keterangan                         |
| --------- | ------- | -------------- | ---------------------------------- |
| `year`    | integer | tahun sekarang | Periode tahun yang dihitung        |
| `month`   | integer | bulan sekarang | Periode bulan yang dihitung (1–12) |

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "active_customers": 45,
    "recorded_count": 30,
    "not_recorded_count": 15,
    "unpaid_count": 12,
    "total_collected": 540000,
    "history": [
      {
        "water_reading_id": 55,
        "customer_id": 5,
        "customer_name": "Andi Suartika",
        "customer_number": "PLG-2024-001",
        "zone": "Lingkungan Sangket",
        "recorded": true,
        "reading_date": "2026-06-03",
        "total_amount": 31000,
        "amount_paid": 0,
        "payment_status": "belum_bayar"
      },
      {
        "water_reading_id": null,
        "customer_id": 9,
        "customer_name": "Wayan Karya",
        "customer_number": "PLG-2024-009",
        "zone": "Lingkungan Sangket",
        "recorded": false,
        "reading_date": null,
        "total_amount": null,
        "amount_paid": null,
        "payment_status": "belum_dicatat"
      }
    ]
  },
  "meta": {
    "year": 2026,
    "month": 6
  }
}
```

| Field                      | Keterangan                                                                                                                                                 |
| -------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `active_customers`         | Total pelanggan dengan status aktif                                                                                                                        |
| `recorded_count`           | Jumlah pelanggan yang sudah dicatat meternya pada periode ini                                                                                              |
| `not_recorded_count`       | Jumlah pelanggan aktif yang **belum** dicatat pada periode ini                                                                                             |
| `unpaid_count`             | Jumlah tagihan periode ini dengan status `belum_bayar` atau `sebagian`                                                                                     |
| `total_collected`          | Total nominal pembayaran yang masuk pada bulan ini (berdasarkan `payment_date`, bukan periode tagihan)                                                     |
| `history`                  | Daftar gabungan pelanggan sudah & belum dicatat. Gunakan field `recorded` (`true`/`false`) untuk memisahkan ke tab "Sudah Dicatat" / "Belum Dicatat" di UI |
| `history[].payment_status` | `belum_bayar`, `sebagian`, `lunas`, atau `belum_dicatat` (khusus item yang belum dicatat)                                                                  |

---

### 3.2 Daftar Pelanggan

```
GET /api/petugas/customers
GET /api/petugas/customers?is_active=true
GET /api/petugas/customers?is_active=false
```

Digunakan untuk mengisi dropdown saat input meter, dan untuk daftar pelanggan dengan filter status.

**Query Parameter**

| Parameter   | Tipe    | Default | Keterangan                                                                                                                                                                 |
| ----------- | ------- | ------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `is_active` | boolean | —       | `true` → hanya pelanggan aktif, `false` → hanya pelanggan nonaktif. Jika parameter ini **tidak dikirim**, API akan mengembalikan hanya pelanggan aktif (perilaku default). |

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
      "phone": "082233445566",
      "zone_id": 1,
      "zone": "Zona A",
      "tariff_rate_id": 1,
      "tariff": "Tarif Rumah Tangga",
      "initial_meter": 100.0,
      "is_active": true
    }
  ],
  "meta": {
    "total": 45
  }
}
```

---

### 3.3 Detail Pelanggan

```
GET /api/petugas/customers/{id}
```

Profil lengkap pelanggan, pembacaan meter terakhir, dan riwayat tagihan (5 periode sebelum pembacaan terakhir). Digunakan saat petugas membuka detail seorang pelanggan.

**Path Parameter**

| Parameter | Tipe    | Keterangan   |
| --------- | ------- | ------------ |
| `id`      | integer | ID pelanggan |

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 5,
    "name": "Andi Suartika",
    "customer_number": "PLG-2024-001",
    "zone": "Lingkungan Sangket",
    "is_active": true,
    "registered_date": "2024-01-10",
    "tariff_group": "Rumah Tangga",
    "price_per_m3": 8000,
    "minimum_usage": 5,
    "minimum_charge": 15000,
    "last_reading": {
      "current_reading": 145.5,
      "usage_m3": 12.4,
      "period_label": "Juni 2026",
      "reading_date": "2025-06-14",
      "payment_status": "lunas"
    },
    "billing_history": [
      {
        "period_label": "Mei 2026",
        "usage_m3": 27.4,
        "total_amount": 68500,
        "payment_status": "belum_bayar"
      },
      {
        "period_label": "April 2026",
        "usage_m3": 21.5,
        "total_amount": 72500,
        "payment_status": "sebagian"
      }
    ]
  },
  "meta": null
}
```

| Field             | Keterangan                                                                                                                                                                                                                              |
| ----------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `registered_date` | Tanggal pemasangan/instalasi pelanggan                                                                                                                                                                                                  |
| `tariff_group`    | Nama golongan tarif pelanggan                                                                                                                                                                                                           |
| `minimum_usage`   | Batas minimal pemakaian (m3) yang tetap dikenakan tarif penuh                                                                                                                                                                           |
| `minimum_charge`  | Tagihan minimum walaupun pemakaian di bawah `minimum_usage`. Gunakan field ini bersama `price_per_m3` & `minimum_usage` untuk menghitung estimasi tagihan di Flutter sebelum submit, supaya hasilnya konsisten dengan kalkulasi backend |
| `last_reading`    | Pembacaan meter paling baru. `null` jika belum pernah dicatat sama sekali                                                                                                                                                               |
| `billing_history` | Maksimal 5 periode tagihan sebelum `last_reading`, urut dari terbaru                                                                                                                                                                    |

**Response 404 — Pelanggan tidak ditemukan**

```json
{
  "success": false,
  "message": "Data tidak ditemukan.",
  "data": null,
  "meta": null
}
```

---

### 3.4 Opsi Zona & Tarif (Form Tambah/Edit Pelanggan)

```
GET /api/petugas/customers/form-options
```

Mengembalikan daftar zona aktif dan golongan tarif aktif untuk mengisi dropdown pada form tambah/edit pelanggan.

**Response 200**

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "zones": [
      { "id": 1, "name": "Zona A", "code": "ZN-A" },
      { "id": 2, "name": "Zona B", "code": "ZN-B" }
    ],
    "tariffs": [
      {
        "id": 1,
        "name": "Tarif Rumah Tangga",
        "price_per_m3": 2500.0,
        "minimum_charge": 15000.0,
        "minimum_usage": 5.0
      }
    ]
  },
  "meta": null
}
```

| Field     | Keterangan                                                                                                                                       |
| --------- | ------------------------------------------------------------------------------------------------------------------------------------------------ |
| `zones`   | Hanya zona dengan `is_active = true`                                                                                                             |
| `tariffs` | Hanya golongan tarif dengan `is_active = true`. Gunakan `price_per_m3`, `minimum_charge`, `minimum_usage` untuk preview estimasi tagihan di form |

---

### 3.5 Tambah Pelanggan

```
POST /api/petugas/customers
```

**Request Body**

```json
{
  "customer_number": null,
  "name": "Wayan Karya",
  "address": "Banjar Kaja No. 12",
  "phone": "082233445566",
  "zone_id": 1,
  "tariff_rate_id": 1,
  "installation_date": "2026-06-24",
  "initial_meter": 0,
  "is_active": true,
  "notes": null,
  "user_id": null
}
```

| Field               | Tipe           | Wajib | Keterangan                                                                   |
| ------------------- | -------------- | ----- | ---------------------------------------------------------------------------- |
| `customer_number`   | string         | Tidak | Dikosongkan (`null`) untuk generate otomatis format `PDAM-2026-0001`         |
| `name`              | string         | Ya    | Nama pelanggan                                                               |
| `address`           | string         | Ya    | Alamat lengkap                                                               |
| `phone`             | string         | Tidak | Format nomor telepon (`0-9`, `+`, `-`, spasi)                                |
| `zone_id`           | integer        | Ya    | ID zona, harus ada di tabel `zones`                                          |
| `tariff_rate_id`    | integer        | Ya    | ID golongan tarif, harus ada di tabel `tariff_rates`                         |
| `installation_date` | string (Y-m-d) | Tidak | Tanggal pemasangan                                                           |
| `initial_meter`     | float          | Tidak | Angka meter awal, default `0`                                                |
| `is_active`         | boolean        | Tidak | Status aktif, default `true`                                                 |
| `notes`             | string         | Tidak | Catatan tambahan                                                             |
| `user_id`           | integer        | Tidak | Hubungkan ke akun login pelanggan (untuk fitur cek tagihan di app pelanggan) |

**Response 201 — Berhasil**

```json
{
  "success": true,
  "message": "Pelanggan berhasil ditambahkan.",
  "data": {
    "id": 5,
    "user_id": null,
    "customer_number": "PDAM-2026-0001",
    "name": "Wayan Karya",
    "address": "Banjar Kaja No. 12",
    "phone": "082233445566",
    "zone_id": 1,
    "zone": "Zona A",
    "tariff_rate_id": 1,
    "tariff_name": "Tarif Rumah Tangga",
    "installation_date": "2026-06-24",
    "initial_meter": 0,
    "is_active": true,
    "notes": null
  },
  "meta": null
}
```

---

### 3.6 Update Pelanggan

```
PUT /api/petugas/customers/{id}
```

**Path Parameter**

| Parameter | Tipe    | Keterangan   |
| --------- | ------- | ------------ |
| `id`      | integer | ID pelanggan |

**Request Body** — sama seperti [3.5](#35-tambah-pelanggan), semua field wajib diisi ulang (bukan partial update).

**Response 200 — Berhasil**

```json
{
  "success": true,
  "message": "Pelanggan berhasil diperbarui.",
  "data": {
    "id": 5,
    "user_id": null,
    "customer_number": "PDAM-2026-0001",
    "name": "Wayan Karya",
    "address": "Banjar Kaja No. 12 (Update)",
    "phone": "082233445566",
    "zone_id": 1,
    "zone": "Zona A",
    "tariff_rate_id": 1,
    "tariff_name": "Tarif Rumah Tangga",
    "installation_date": "2026-06-24",
    "initial_meter": 0,
    "is_active": true,
    "notes": null
  },
  "meta": null
}
```

**Response 404 — Pelanggan tidak ditemukan**

```json
{
  "success": false,
  "message": "Data tidak ditemukan.",
  "data": null,
  "meta": null
}
```

---

### 3.7 Hapus Pelanggan

```
DELETE /api/petugas/customers/{id}
```

> Soft delete — data tidak dihapus permanen dari database, hanya ditandai terhapus (`deleted_at`).

**Path Parameter**

| Parameter | Tipe    | Keterangan   |
| --------- | ------- | ------------ |
| `id`      | integer | ID pelanggan |

**Response 200 — Berhasil**

```json
{
  "success": true,
  "message": "Pelanggan berhasil dihapus.",
  "data": null,
  "meta": null
}
```

---

### 3.8 Daftar Pembacaan Meter

```
GET /api/petugas/water-readings?year=2026&month=6
```

**Query Parameter**

| Parameter | Tipe    | Default        | Keterangan                  |
| --------- | ------- | -------------- | --------------------------- |
| `year`    | integer | tahun sekarang | Filter tahun periode        |
| `month`   | integer | bulan sekarang | Filter bulan periode (1–12) |

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
      "previous_reading": 132.8,
      "current_reading": 145.2,
      "usage_m3": 12.4,
      "price_per_m3": 2500.0,
      "minimum_charge": 15000.0,
      "total_amount": 31000.0,
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

### 3.9 Catat Meter Baru

```
POST /api/petugas/water-readings
```

> **Content-Type:** `multipart/form-data` (wajib jika menyertakan foto meter, boleh `application/json` jika tanpa foto).

**Request Body**

```json
{
  "customer_id": 5,
  "period_year": 2026,
  "period_month": 6,
  "current_reading": 145.2,
  "reading_date": "2026-06-03",
  "notes": "Meteran normal",
  "photo": "(file binary, opsional)",
  "send_whatsapp": true
}
```

| Field             | Tipe                | Wajib | Keterangan                                                                                          |
| ----------------- | ------------------- | ----- | ----------------------------------------------------------------------------------------------------- |
| `customer_id`     | integer             | Ya    | ID pelanggan                                                                                         |
| `period_year`     | integer             | Ya    | Tahun periode (min 2000)                                                                             |
| `period_month`    | integer             | Ya    | Bulan periode (1–12)                                                                                 |
| `current_reading` | float               | Ya    | Angka meter saat ini                                                                                 |
| `reading_date`    | string (Y-m-d)      | Ya    | Tanggal catat meter                                                                                  |
| `notes`           | string              | Tidak | Catatan tambahan                                                                                     |
| `photo`           | file (jpg/png/webp) | Tidak | Foto meter, maksimal 5MB                                                                             |
| `send_whatsapp`   | boolean             | Tidak | Kirim notifikasi tagihan via WhatsApp setelah berhasil dicatat. Default `true` jika tidak dikirim    |

> **Catatan:** Sistem otomatis menghitung `previous_reading`, `usage_m3`, dan `total_amount` berdasarkan tarif pelanggan. Jika pelanggan sudah dicatat pada periode yang sama, request akan ditolak (422). Notifikasi WhatsApp dikirim asinkron lewat job queue — kirim `send_whatsapp: false` untuk melewati pengiriman.

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
    "previous_reading": 132.8,
    "current_reading": 145.2,
    "usage_m3": 12.4,
    "price_per_m3": 2500.0,
    "minimum_charge": 15000.0,
    "total_amount": 31000.0,
    "payment_status": "belum_bayar",
    "notes": "Meteran normal",
    "photo_url": "https://your-domain.com/storage/water-readings/abc123.jpg"
  },
  "meta": null
}
```

| Field       | Keterangan                                                      |
| ----------- | --------------------------------------------------------------- |
| `photo_url` | URL publik foto meter. `null` jika tidak ada foto yang diunggah |

---

### 3.10 Detail Pembacaan Meter

```
GET /api/petugas/water-readings/{id}
```

**Response 200** — Struktur sama dengan item pada [3.9](#39-catat-meter-baru).

---

### 3.11 Tagihan Belum Bayar

```
GET /api/petugas/tagihan?zone_id=1&year=2026&month=6
```

**Query Parameter**

| Parameter | Tipe    | Keterangan           |
| --------- | ------- | -------------------- |
| `zone_id` | integer | Filter zona          |
| `year`    | integer | Filter tahun periode |
| `month`   | integer | Filter bulan periode |

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
      "total_amount": 31000.0,
      "remaining_amount": 31000.0,
      "payment_status": "belum_bayar"
    }
  ],
  "meta": {
    "total": 1
  }
}
```

---

### 3.12 Konfirmasi Pembayaran

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

| Field              | Tipe           | Wajib | Nilai Valid                 | Keterangan              |
| ------------------ | -------------- | ----- | --------------------------- | ----------------------- |
| `water_reading_id` | integer        | Ya    | —                           | ID tagihan yang dibayar |
| `amount_paid`      | float          | Ya    | > 0                         | Jumlah yang dibayar     |
| `payment_date`     | string (Y-m-d) | Ya    | —                           | Tanggal bayar           |
| `payment_method`   | string         | Ya    | `tunai`, `transfer`, `qris` | Metode pembayaran       |
| `notes`            | string         | Tidak | —                           | Catatan                 |

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
    "amount_paid": 31000.0,
    "payment_date": "2026-06-09",
    "payment_method": "tunai",
    "status": "lunas",
    "recorded_by": "Budi Santoso",
    "notes": "Bayar lunas"
  },
  "meta": null
}
```

| `status`   | Keterangan                               |
| ---------- | ---------------------------------------- |
| `sebagian` | Pembayaran sebagian, tagihan belum lunas |
| `lunas`    | Tagihan lunas setelah pembayaran ini     |

---

### 3.13 Detail Pembayaran

```
GET /api/petugas/payments/{id}
```

**Response 200** — Struktur sama dengan item pada [3.12](#312-konfirmasi-pembayaran).

---

### 3.14 Daftar Laporan Maintenance

```
GET /api/petugas/maintenance?status=dilaporkan&priority=darurat
```

**Query Parameter**

| Parameter  | Tipe    | Nilai Valid                                        | Keterangan       |
| ---------- | ------- | -------------------------------------------------- | ---------------- |
| `status`   | string  | `dilaporkan`, `dalam_proses`, `selesai`, `ditunda` | Filter status    |
| `priority` | string  | `rendah`, `sedang`, `tinggi`, `darurat`            | Filter prioritas |
| `category` | string  | Lihat tabel kategori                               | Filter kategori  |
| `zone_id`  | integer | —                                                  | Filter zona      |

**Kategori Maintenance**

| Nilai            | Label          |
| ---------------- | -------------- |
| `pipa_bocor`     | Pipa Bocor     |
| `meteran_rusak`  | Meteran Rusak  |
| `pompa`          | Pompa          |
| `reservoir`      | Reservoir      |
| `instalasi_baru` | Instalasi Baru |
| `lainnya`        | Lainnya        |

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

### 3.15 Buat Laporan Maintenance

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

| Field           | Tipe           | Wajib | Keterangan                                                      |
| --------------- | -------------- | ----- | --------------------------------------------------------------- |
| `title`         | string         | Ya    | Judul singkat laporan                                           |
| `location`      | string         | Ya    | Lokasi kejadian                                                 |
| `category`      | string         | Ya    | Lihat tabel kategori di [3.14](#314-daftar-laporan-maintenance) |
| `priority`      | string         | Ya    | `rendah`, `sedang`, `tinggi`, `darurat`                         |
| `reported_date` | string (Y-m-d) | Ya    | Tanggal laporan                                                 |
| `zone_id`       | integer        | Tidak | ID zona terdampak                                               |
| `customer_id`   | integer        | Tidak | ID pelanggan terkait (jika ada)                                 |
| `description`   | string         | Tidak | Deskripsi detail                                                |
| `material_cost` | float          | Tidak | Estimasi biaya material                                         |
| `labor_cost`    | float          | Tidak | Estimasi biaya tenaga kerja                                     |

**Response 201** — Struktur sama dengan item pada [3.14](#314-daftar-laporan-maintenance).

---

### 3.16 Detail Maintenance

```
GET /api/petugas/maintenance/{id}
```

**Response 200** — Struktur sama dengan item pada [3.14](#314-daftar-laporan-maintenance).

---

### 3.17 Update Status Maintenance

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

| Field           | Tipe   | Wajib | Keterangan                                         |
| --------------- | ------ | ----- | -------------------------------------------------- |
| `status`        | string | Ya    | `dilaporkan`, `dalam_proses`, `selesai`, `ditunda` |
| `material_cost` | float  | Tidak | Biaya material aktual                              |
| `labor_cost`    | float  | Tidak | Biaya tenaga kerja aktual                          |
| `notes`         | string | Tidak | Catatan penyelesaian                               |

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
    "material_cost": 150000.0,
    "labor_cost": 100000.0,
    "total_cost": 250000.0,
    "...": "field lainnya"
  },
  "meta": null
}
```

---

## Alur Penggunaan Tipikal

### Petugas — Buka Dashboard

```
1. POST /api/auth/login        → dapat token
2. GET  /api/petugas/dashboard → tampilkan statistik & history pencatatan
```

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
