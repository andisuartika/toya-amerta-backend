<?php

use App\Models\Zone;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
});

// ── Index ────────────────────────────────────────────────────────────
test('admin dapat melihat halaman daftar zona', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.zones.index'))
        ->assertOk()
        ->assertSee('Zona Wilayah');
});

test('tamu tidak dapat mengakses halaman zona', function () {
    $this->get(route('admin.zones.index'))
        ->assertRedirect(route('login'));
});

// ── Create ───────────────────────────────────────────────────────────
test('admin dapat membuat zona baru', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.zones.store'), [
            'name'      => 'Zona A – Banjar Kaja',
            'code'      => 'ZA01',
            'is_active' => '1',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('zones', [
        'name' => 'Zona A – Banjar Kaja',
        'code' => 'ZA01',
    ]);
});

test('pembuatan zona gagal jika nama kosong', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.zones.store'), [
            'code' => 'ZA01',
        ])
        ->assertSessionHasErrors('name');
});

test('pembuatan zona gagal jika kode duplikat', function () {
    makeZone(['code' => 'ZA01']);

    $this->actingAs(adminUser())
        ->post(route('admin.zones.store'), [
            'name' => 'Zona Lain',
            'code' => 'ZA01',
        ])
        ->assertSessionHasErrors('code');
});

// ── Update ───────────────────────────────────────────────────────────
test('admin dapat mengupdate zona', function () {
    $zone = makeZone();

    $this->actingAs(adminUser())
        ->put(route('admin.zones.update', $zone), [
            'name'      => 'Zona B – Banjar Kelod',
            'code'      => 'ZB01',
            'is_active' => '1',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('zones', [
        'id'   => $zone->id,
        'name' => 'Zona B – Banjar Kelod',
        'code' => 'ZB01',
    ]);
});

// ── Delete ───────────────────────────────────────────────────────────
test('admin dapat menghapus zona yang tidak punya pelanggan', function () {
    $zone = makeZone();

    $this->actingAs(adminUser())
        ->delete(route('admin.zones.destroy', $zone))
        ->assertRedirect();

    $this->assertDatabaseMissing('zones', ['id' => $zone->id]);
});

test('zona tidak dapat dihapus jika masih punya pelanggan', function () {
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
        ->delete(route('admin.zones.destroy', $zone))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('zones', ['id' => $zone->id]);
});
