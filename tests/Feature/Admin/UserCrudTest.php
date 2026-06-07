<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin',     'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'petugas',   'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'pelanggan', 'guard_name' => 'web']);
});

function userPayload(array $override = []): array
{
    return array_merge([
        'name'                  => 'I Gede Arta',
        'email'                 => 'arta@example.com',
        'phone'                 => '081338001001',
        'role'                  => 'petugas',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'is_active'             => '1',
    ], $override);
}

// ── Index ────────────────────────────────────────────────────────────
test('admin dapat melihat halaman daftar pengguna', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Kelola Pengguna');
});

test('tamu tidak dapat mengakses halaman pengguna', function () {
    $this->get(route('admin.users.index'))
        ->assertRedirect(route('login'));
});

// ── Create ───────────────────────────────────────────────────────────
test('admin dapat membuat pengguna baru', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.users.store'), userPayload())
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'email' => 'arta@example.com',
        'role'  => 'petugas',
    ]);
});

test('pengguna baru otomatis mendapat role yang sesuai', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.users.store'), userPayload(['role' => 'admin', 'email' => 'newadmin@example.com']))
        ->assertRedirect();

    $user = User::where('email', 'newadmin@example.com')->first();
    expect($user->hasRole('admin'))->toBeTrue();
});

test('pembuatan pengguna gagal jika email duplikat', function () {
    User::factory()->create(['email' => 'arta@example.com']);

    $this->actingAs(adminUser())
        ->post(route('admin.users.store'), userPayload())
        ->assertSessionHasErrors('email');
});

test('pembuatan pengguna gagal jika role tidak valid', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.users.store'), userPayload(['role' => 'superadmin']))
        ->assertSessionHasErrors('role');
});

test('pembuatan pengguna gagal jika password tidak diisi', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.users.store'), userPayload(['password' => '']))
        ->assertSessionHasErrors('password');
});

// ── Update ───────────────────────────────────────────────────────────
test('admin dapat mengupdate pengguna', function () {
    $user = User::factory()->create(['role' => 'petugas', 'email' => 'arta@example.com']);
    $user->assignRole('petugas');

    $this->actingAs(adminUser())
        ->put(route('admin.users.update', $user), userPayload([
            'name'                  => 'I Gede Artana',
            'email'                 => 'arta@example.com',
            'password'              => '',
            'password_confirmation' => '',
        ]))
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'id'   => $user->id,
        'name' => 'I Gede Artana',
    ]);
});

test('password tidak berubah jika dikosongkan saat update', function () {
    $user = User::factory()->create(['role' => 'petugas']);
    $user->assignRole('petugas');
    $oldHash = $user->password;

    $this->actingAs(adminUser())
        ->put(route('admin.users.update', $user), userPayload([
            'email'                 => $user->email,
            'password'              => '',
            'password_confirmation' => '',
        ]))
        ->assertRedirect();

    expect($user->fresh()->password)->toBe($oldHash);
});

// ── Delete ───────────────────────────────────────────────────────────
test('admin dapat menghapus pengguna', function () {
    $user = User::factory()->create(['role' => 'petugas']);
    $user->assignRole('petugas');

    $this->actingAs(adminUser())
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect();

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
