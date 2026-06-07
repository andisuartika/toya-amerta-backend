<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'MADE SEDANA YASA',       'phone' => '081338001001'],
            ['name' => 'MADE BERATA',             'phone' => '081338001002'],
            ['name' => 'PUTU SEDANA',             'phone' => '081338001003'],
            ['name' => 'MADE YASA',               'phone' => '081338001004'],
            ['name' => 'MADE MERTAYASA',          'phone' => '081338001005'],
            ['name' => 'GEDE WIDIADA',            'phone' => '081338001006'],
            ['name' => 'KADEK SUDANA',            'phone' => '081338001007'],
            ['name' => 'GEDE ARTAYASA',           'phone' => '081338001008'],
            ['name' => 'KOMANG PUTRA SUADNYANA',  'phone' => '081338001009'],
            ['name' => 'GEDE ARTANA',             'phone' => '081338001010'],
            ['name' => 'GEDE ARTA MARA YASA',     'phone' => '081338001011'],
            ['name' => 'MADE SUDIADA',            'phone' => '081338001012'],
            ['name' => 'KOMANG BUDIARTA',         'phone' => '081338001013'],
            ['name' => 'GEDE PUTRANA',            'phone' => '081338001014'],
            ['name' => 'MADE PASEK',              'phone' => '081338001015'],
            ['name' => 'NYOMAN SUJANA',           'phone' => '081338001016'],
            ['name' => 'KETUT LAKSANA ARNIKA',    'phone' => '081338001017'],
            ['name' => 'IDA BAGUS',               'phone' => null],
            ['name' => 'MADE SUMADIA',            'phone' => '081338001019'],
            ['name' => 'GEDE GELGEL',             'phone' => '081338001020'],
        ];

        $installDates = [
            '2020-01-15', '2020-03-10', '2020-06-01', '2021-01-20', '2021-04-05',
            '2021-07-12', '2021-09-01', '2022-01-15', '2022-03-20', '2022-05-10',
            '2022-08-01', '2022-10-15', '2023-01-05', '2023-03-20', '2023-06-01',
            '2023-08-10', '2023-10-01', '2024-01-15', '2024-04-01', '2024-07-20',
        ];

        $tariffIds = [1, 1, 1, 2, 1, 1, 1, 2, 1, 1, 2, 1, 1, 1, 2, 1, 1, 1, 2, 1];

        foreach ($customers as $i => $customer) {
            $number = 'PDAM-' . date('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            DB::table('customers')->insertOrIgnore([
                'customer_number'   => $number,
                'name'              => $customer['name'],
                'address'           => 'Lingkungan Sangket',
                'phone'             => $customer['phone'],
                'zone_id'           => 1,
                'tariff_rate_id'    => $tariffIds[$i],
                'installation_date' => $installDates[$i],
                'initial_meter'     => rand(0, 50),
                'is_active'         => 1,
                'notes'             => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
