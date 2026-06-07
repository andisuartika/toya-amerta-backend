<?php

use App\Models\Customer;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
});

function customerPayload(int $zoneId, int $tariffId, array $override = []): array
{
    return array_merge([
        'customer_number'   => 'PDAM-2026-0001',
        'name'              => 'MADE SEDANA YASA',
        'address'           => 'Lingkungan Sangket',
        'phone'             => '081338001001',
        'zone_id'           => $zoneId,
        'tariff_rate_id'    => $tariffId,
        'installation_date' => '2024-01-15',
        'is_active'         => '1',
    ], $override);
}

// ── Index ────────────────────────────────────────────────────────────
test('admin dapat melihat halaman daftar pelanggan', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertSee('Data Pelanggan');
});

test('tamu tidak dapat mengakses halaman pelanggan', function () {
    $this->get(route('admin.customers.index'))
        ->assertRedirect(route('login'));
});

// ── Generate Number ──────────────────────────────────────────────────
test('generate nomor pelanggan mengembalikan format PDAM-YYYY-XXXX', function () {
    $this->actingAs(adminUser())
        ->getJson(route('admin.customers.generate-number'))
        ->assertOk()
        ->assertJsonStructure(['number'])
        ->assertJsonFragment(['number' => 'PDAM-' . date('Y') . '-0001']);
});

// ── Create ───────────────────────────────────────────────────────────
test('admin dapat menambah pelanggan baru', function () {
    $zone   = makeZone();
    $tariff = makeTariff();

    $this->actingAs(adminUser())
        ->post(route('admin.customers.store'), customerPayload($zone->id, $tariff->id))
        ->assertRedirect();

    $this->assertDatabaseHas('customers', [
        'customer_number' => 'PDAM-2026-0001',
        'name'            => 'MADE SEDANA YASA',
    ]);
});

test('penambahan pelanggan gagal jika nama kosong', function () {
    $zone   = makeZone();
    $tariff = makeTariff();

    $this->actingAs(adminUser())
        ->post(route('admin.customers.store'), customerPayload($zone->id, $tariff->id, ['name' => '']))
        ->assertSessionHasErrors('name');
});

test('penambahan pelanggan gagal jika zona tidak valid', function () {
    $tariff = makeTariff();

    $this->actingAs(adminUser())
        ->post(route('admin.customers.store'), customerPayload(999, $tariff->id))
        ->assertSessionHasErrors('zone_id');
});

test('penambahan pelanggan gagal jika nomor pelanggan duplikat', function () {
    $zone   = makeZone();
    $tariff = makeTariff();

    Customer::create(array_merge(customerPayload($zone->id, $tariff->id), ['initial_meter' => 0]));

    $this->actingAs(adminUser())
        ->post(route('admin.customers.store'), customerPayload($zone->id, $tariff->id, [
            'customer_number' => 'PDAM-2026-0001',
            'name'            => 'GEDE ARTANA',
        ]))
        ->assertSessionHasErrors('customer_number');
});

// ── Update ───────────────────────────────────────────────────────────
test('admin dapat mengupdate data pelanggan', function () {
    $zone   = makeZone();
    $tariff = makeTariff();

    $customer = Customer::create(array_merge(customerPayload($zone->id, $tariff->id), ['initial_meter' => 0]));

    $this->actingAs(adminUser())
        ->put(route('admin.customers.update', $customer), customerPayload($zone->id, $tariff->id, [
            'name'    => 'MADE BERATA',
            'phone'   => '081338009999',
            'address' => 'Lingkungan Sangket Kaja',
        ]))
        ->assertRedirect();

    $this->assertDatabaseHas('customers', [
        'id'    => $customer->id,
        'name'  => 'MADE BERATA',
        'phone' => '081338009999',
    ]);
});

test('update pelanggan mempertahankan installation_date lama jika tidak diisi', function () {
    $zone   = makeZone();
    $tariff = makeTariff();

    $customer = Customer::create(array_merge(
        customerPayload($zone->id, $tariff->id),
        ['initial_meter' => 0, 'installation_date' => '2022-05-10']
    ));

    $this->actingAs(adminUser())
        ->put(route('admin.customers.update', $customer), customerPayload($zone->id, $tariff->id, [
            'installation_date' => '',
        ]))
        ->assertRedirect();

    $saved = \App\Models\Customer::find($customer->id)->installation_date;
    expect($saved->format('Y-m-d'))->toBe('2022-05-10');
});

// ── Delete ───────────────────────────────────────────────────────────
test('admin dapat menghapus pelanggan', function () {
    $zone   = makeZone();
    $tariff = makeTariff();

    $customer = Customer::create(array_merge(customerPayload($zone->id, $tariff->id), ['initial_meter' => 0]));

    $this->actingAs(adminUser())
        ->delete(route('admin.customers.destroy', $customer))
        ->assertRedirect();

    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
});
