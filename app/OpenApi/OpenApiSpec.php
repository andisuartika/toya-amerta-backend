<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Toya Amerta API',
    description: 'REST API untuk aplikasi mobile (Flutter) Sistem Informasi PDAM Desa Toya Amerta. ' .
        'Semua response menggunakan envelope { success, message, data, meta }.',
    contact: new OA\Contact(name: 'Toya Amerta Dev Team', email: 'pt.andisuartika@gmail.com')
)]
#[OA\Server(
    url: '/api',
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'token',
    description: 'Gunakan token yang didapat dari endpoint /auth/login. Format header: Authorization: Bearer {token}'
)]
#[OA\Tag(name: 'Auth', description: 'Login, profil, dan logout')]
#[OA\Tag(name: 'Pelanggan', description: 'Endpoint khusus role pelanggan')]
#[OA\Tag(name: 'Petugas - Catat Meter', description: 'Pencatatan meter air oleh petugas')]
#[OA\Tag(name: 'Petugas - Pelanggan', description: 'CRUD data master pelanggan')]
#[OA\Tag(name: 'Petugas - Pembayaran', description: 'Tagihan & konfirmasi pembayaran')]
#[OA\Tag(name: 'Petugas - Maintenance', description: 'Laporan & penanganan maintenance')]

#[OA\Schema(
    schema: 'ApiEnvelope',
    title: 'API Envelope',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'OK'),
        new OA\Property(property: 'data', type: 'object', nullable: true),
        new OA\Property(property: 'meta', type: 'object', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ValidationErrorResponse',
    title: 'Validation Error Response',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Data tidak valid.'),
        new OA\Property(
            property: 'data',
            type: 'object',
            example: ['field' => ['Pesan error validasi.']]
        ),
        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
    ]
)]
#[OA\Schema(
    schema: 'UnauthorizedResponse',
    title: 'Unauthenticated Response',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated. Token tidak valid atau sudah kedaluwarsa.'),
        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
    ]
)]
#[OA\Schema(
    schema: 'ForbiddenResponse',
    title: 'Forbidden Response',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Akses ditolak. Role Anda tidak memiliki izin.'),
        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
    ]
)]
#[OA\Schema(
    schema: 'WaterReading',
    title: 'Water Reading',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 55),
        new OA\Property(property: 'customer_id', type: 'integer', example: 5),
        new OA\Property(property: 'customer_name', type: 'string', example: 'Wayan Karya'),
        new OA\Property(property: 'customer_number', type: 'string', example: 'PLG-0005'),
        new OA\Property(property: 'zone', type: 'string', example: 'Zona A'),
        new OA\Property(property: 'officer_name', type: 'string', example: 'Budi Santoso'),
        new OA\Property(property: 'period_year', type: 'integer', example: 2026),
        new OA\Property(property: 'period_month', type: 'integer', example: 6),
        new OA\Property(property: 'period_label', type: 'string', example: 'Juni 2026'),
        new OA\Property(property: 'reading_date', type: 'string', format: 'date', example: '2026-06-03'),
        new OA\Property(property: 'previous_reading', type: 'number', format: 'float', example: 132.80),
        new OA\Property(property: 'current_reading', type: 'number', format: 'float', example: 145.20),
        new OA\Property(property: 'usage_m3', type: 'number', format: 'float', example: 12.40),
        new OA\Property(property: 'price_per_m3', type: 'number', format: 'float', example: 2500),
        new OA\Property(property: 'minimum_charge', type: 'number', format: 'float', example: 15000),
        new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 31000),
        new OA\Property(property: 'payment_status', type: 'string', enum: ['belum_bayar', 'sebagian', 'lunas'], example: 'belum_bayar'),
        new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Meteran normal'),
        new OA\Property(property: 'photo_url', type: 'string', nullable: true, example: 'https://your-domain.com/storage/water-readings/abc123.jpg'),
    ]
)]
#[OA\Schema(
    schema: 'PaymentRecord',
    title: 'Payment Record',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 30),
        new OA\Property(property: 'receipt_number', type: 'string', example: 'KWT-20260609-0001'),
        new OA\Property(property: 'water_reading_id', type: 'integer', example: 55),
        new OA\Property(property: 'customer_id', type: 'integer', example: 5),
        new OA\Property(property: 'period_label', type: 'string', example: 'Juni 2026'),
        new OA\Property(property: 'amount_paid', type: 'number', format: 'float', example: 31000),
        new OA\Property(property: 'payment_date', type: 'string', format: 'date', example: '2026-06-09'),
        new OA\Property(property: 'payment_method', type: 'string', enum: ['tunai', 'transfer', 'qris'], example: 'tunai'),
        new OA\Property(property: 'status', type: 'string', enum: ['sebagian', 'lunas'], example: 'lunas'),
        new OA\Property(property: 'recorded_by', type: 'string', example: 'Budi Santoso'),
        new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Bayar lunas'),
    ]
)]
#[OA\Schema(
    schema: 'Maintenance',
    title: 'Maintenance',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 8),
        new OA\Property(property: 'title', type: 'string', example: 'Pipa bocor di Jl. Raya Desa'),
        new OA\Property(property: 'location', type: 'string', example: 'Jl. Raya Desa No. 45'),
        new OA\Property(property: 'category', type: 'string', enum: ['pipa_bocor', 'meteran_rusak', 'pompa', 'reservoir', 'instalasi_baru', 'lainnya'], example: 'pipa_bocor'),
        new OA\Property(property: 'category_label', type: 'string', example: 'Pipa Bocor'),
        new OA\Property(property: 'priority', type: 'string', enum: ['rendah', 'sedang', 'tinggi', 'darurat'], example: 'tinggi'),
        new OA\Property(property: 'priority_label', type: 'string', example: 'Tinggi'),
        new OA\Property(property: 'status', type: 'string', enum: ['dilaporkan', 'dalam_proses', 'selesai', 'ditunda'], example: 'dilaporkan'),
        new OA\Property(property: 'status_label', type: 'string', example: 'Dilaporkan'),
        new OA\Property(property: 'zone', type: 'string', nullable: true, example: 'Zona B'),
        new OA\Property(property: 'customer_name', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'reported_by', type: 'string', example: 'Budi Santoso'),
        new OA\Property(property: 'reported_date', type: 'string', format: 'date', example: '2026-06-08'),
        new OA\Property(property: 'handled_date', type: 'string', format: 'date', nullable: true, example: null),
        new OA\Property(property: 'completed_date', type: 'string', format: 'date', nullable: true, example: null),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Air merembes dari sambungan pipa bawah tanah.'),
        new OA\Property(property: 'material_cost', type: 'number', format: 'float', nullable: true, example: null),
        new OA\Property(property: 'labor_cost', type: 'number', format: 'float', nullable: true, example: null),
        new OA\Property(property: 'total_cost', type: 'number', format: 'float', example: 0),
    ]
)]
#[OA\Schema(
    schema: 'CustomerDetail',
    title: 'Customer Detail',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 5),
        new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: null),
        new OA\Property(property: 'customer_number', type: 'string', example: 'PDAM-2026-0001'),
        new OA\Property(property: 'name', type: 'string', example: 'Wayan Karya'),
        new OA\Property(property: 'address', type: 'string', example: 'Banjar Kaja No. 12'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '082233445566'),
        new OA\Property(property: 'zone_id', type: 'integer', example: 1),
        new OA\Property(property: 'zone', type: 'string', example: 'Zona A'),
        new OA\Property(property: 'tariff_rate_id', type: 'integer', example: 1),
        new OA\Property(property: 'tariff_name', type: 'string', example: 'Tarif Rumah Tangga'),
        new OA\Property(property: 'installation_date', type: 'string', format: 'date', nullable: true, example: '2026-06-24'),
        new OA\Property(property: 'initial_meter', type: 'number', format: 'float', example: 0),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
        new OA\Property(property: 'notes', type: 'string', nullable: true, example: null),
    ]
)]
#[OA\Schema(
    schema: 'NotFoundResponse',
    title: 'Not Found Response',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Data tidak ditemukan.'),
        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
    ]
)]
class OpenApiSpec
{
    // Container class untuk anotasi global OpenAPI (Info, Server, SecurityScheme, Schema bersama).
}
