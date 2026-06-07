<?php

use App\Models\TariffRate;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
});

// ── Index ────────────────────────────────────────────────────────────
test('admin dapat melihat halaman daftar tarif', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.tariff-rates.index'))
        ->assertOk()
        ->assertSee('Tarif Air');
});

// ── Create ───────────────────────────────────────────────────────────
test('admin dapat membuat tarif baru', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.tariff-rates.store'), [
            'name'           => 'Tarif Rumah Tangga',
            'price_per_m3'   => 3000,
            'minimum_charge' => 10000,
            'minimum_usage'  => 1,
            'is_active'      => '1',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('tariff_rates', [
        'name'         => 'Tarif Rumah Tangga',
        'price_per_m3' => 3000,
    ]);
});

test('pembuatan tarif gagal jika harga tidak diisi', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.tariff-rates.store'), [
            'name'          => 'Tarif Test',
            'minimum_usage' => 1,
        ])
        ->assertSessionHasErrors('price_per_m3');
});

test('pembuatan tarif gagal jika harga negatif', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.tariff-rates.store'), [
            'name'         => 'Tarif Test',
            'price_per_m3' => -500,
            'minimum_usage'=> 1,
        ])
        ->assertSessionHasErrors('price_per_m3');
});

// ── Update ───────────────────────────────────────────────────────────
test('admin dapat mengupdate tarif', function () {
    $tariff = makeTariff();

    $this->actingAs(adminUser())
        ->put(route('admin.tariff-rates.update', $tariff), [
            'name'           => 'Tarif Sosial',
            'price_per_m3'   => 1500,
            'minimum_charge' => 7500,
            'minimum_usage'  => 1,
            'is_active'      => '1',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('tariff_rates', [
        'id'           => $tariff->id,
        'name'         => 'Tarif Sosial',
        'price_per_m3' => 1500,
    ]);
});

// ── Delete ───────────────────────────────────────────────────────────
test('admin dapat menghapus tarif yang tidak dipakai pelanggan', function () {
    $tariff = makeTariff();

    $this->actingAs(adminUser())
        ->delete(route('admin.tariff-rates.destroy', $tariff))
        ->assertRedirect();

    $this->assertSoftDeleted('tariff_rates', ['id' => $tariff->id]);
});

test('tarif tidak dapat dihapus jika masih dipakai pelanggan', function () {
    $zone   = makeZone();
    $tariff = makeTariff();

    \App\Models\Customer::create([
        'customer_number'   => 'PDAM-2026-0001',
        'name'              => 'Test Pelanggan',
        'address'           => 'Lingkungan Sangket',
        'zone_id'           => $zone->id,
        'tariff_rate_id'    => $tariff->id,
        'installation_date' => '2024-01-01',
        'initial_meter'     => 0,
        'is_active'         => 1,
    ]);

    $this->actingAs(adminUser())
        ->delete(route('admin.tariff-rates.destroy', $tariff))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('tariff_rates', ['id' => $tariff->id, 'deleted_at' => null]);
});
