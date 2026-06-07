<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function adminUser(): \App\Models\User
{
    $user = \App\Models\User::factory()->create([
        'role'      => 'admin',
        'is_active' => 1,
    ]);
    $user->assignRole('admin');
    return $user;
}

function makeZone(array $override = []): \App\Models\Zone
{
    return \App\Models\Zone::create(array_merge([
        'name'      => 'Zona Test',
        'code'      => 'ZT01',
        'is_active' => 1,
    ], $override));
}

function makeTariff(array $override = []): \App\Models\TariffRate
{
    return \App\Models\TariffRate::create(array_merge([
        'name'           => 'Tarif Test',
        'price_per_m3'   => 2000,
        'minimum_charge' => 5000,
        'minimum_usage'  => 1,
        'is_active'      => 1,
    ], $override));
}
