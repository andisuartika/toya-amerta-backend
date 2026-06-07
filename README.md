# Toya Amerta — Sistem Manajemen PDAM Desa

Sistem informasi manajemen air bersih berbasis web dan mobile untuk PDAM tingkat desa. Menghubungkan admin, petugas lapangan, dan pelanggan dalam satu platform terintegrasi.

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13, PHP 8.3, MySQL 8 |
| Pattern | Clean Architecture + Repository Pattern |
| Auth | Laravel Sanctum (Bearer Token) |
| Admin UI | Template Dusty (Bootstrap 5) |
| Mobile | Flutter 3.44 + GetX + Clean Architecture |
| WA Notif | Fonnte API |
| Storage | Local Disk / Google Drive |

---

## Fitur Utama

- **Manajemen Pelanggan** — Registrasi, data meter, dan status langganan
- **Pencatatan Meter** — Input pemakaian air per periode oleh petugas lapangan
- **Tagihan & Pembayaran** — Generate tagihan otomatis berdasarkan pemakaian dan tarif
- **Tarif Air** — Konfigurasi tarif bertingkat per golongan pelanggan
- **Laporan** — Rekap pemakaian, pembayaran, dan tunggakan

---

## Aktor

| Aktor | Akses |
|-------|-------|
| **Admin** | Kelola semua master data, keuangan, dan laporan |
| **Petugas** | Input meter, konfirmasi pembayaran, lapor maintenance |
| **Pelanggan** | Lihat tagihan & riwayat pemakaian air (via mobile) |

---

## Arsitektur Backend

Menggunakan **Clean Architecture** dengan pemisahan layer yang ketat:

```
app/
├── Domain/
│   ├── Contracts/        # Repository & Service interfaces
│   ├── DTOs/             # Data Transfer Objects
│   └── UseCases/         # Business logic
├── Infrastructure/
│   └── Repositories/     # Implementasi Repository
└── Http/
    ├── Controllers/       # Hanya memanggil Use Case
    ├── Requests/          # Form Request (validasi)
    └── Resources/         # API Resource (transformasi response)
```

Semua response API menggunakan envelope:

```json
{
  "success": true,
  "message": "...",
  "data": { ... },
  "meta": { ... }
}
```

---

## Instalasi

```bash
# Clone repo
git clone <repo-url>
cd toya-amerta

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Migrasi & seeding
php artisan migrate --seed

# Build assets
npm run dev

# Jalankan server
php artisan serve
```

---

## Lisensi

Proyek internal — hak cipta dimiliki oleh pengembang dan pengelola PDAM Desa Toya Amerta.
