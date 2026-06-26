<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'MADE SEDANA YASA',       'phone' => null],
            ['name' => 'MADE BERATA',             'phone' => null],
            ['name' => 'PUTU SEDANA',             'phone' => null],
            ['name' => 'MADE YASA',               'phone' => null],
            ['name' => 'MADE MERTAYASA',          'phone' => null],
            ['name' => 'GEDE WIDIADA',            'phone' => null],
            ['name' => 'KADEK SUDANA',            'phone' => null],
            ['name' => 'GEDE ARTAYASA',           'phone' => null],
            ['name' => 'KOMANG PUTRA SUADNYANA',  'phone' => null],
            ['name' => 'GEDE ARTANA',             'phone' => null],
            ['name' => 'GEDE ARTA MARA YASA',     'phone' => null],
            ['name' => 'MADE SUDIADA',            'phone' => null],
            ['name' => 'KOMANG BUDIARTA',         'phone' => null],
            ['name' => 'GEDE PUTRANA',            'phone' => null],
            ['name' => 'MADE PASEK',              'phone' => null],
            ['name' => 'NYOMAN SUJANA',           'phone' => null],
            ['name' => 'KETUT LAKSANA ARNIKA',    'phone' => null],
            ['name' => 'IDA BAGUS',               'phone' => null],
            ['name' => 'MADE SUMADIA',            'phone' => null],
            ['name' => 'GEDE GELGEL',             'phone' => null],
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
