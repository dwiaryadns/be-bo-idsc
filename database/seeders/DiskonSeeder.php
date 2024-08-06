<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiskonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'type' => 'Percentage',
                'stok_barang_id' => 'STOKBARANG00001',
                'percent_disc' => '10',
                'amount_disc' => null,
                'expired_disc' => Carbon::now()->addMonth()
            ],
            [
                'type' => 'Amount',
                'stok_barang_id' => 'STOKBARANG00002',
                'percent_disc' => null,
                'amount_disc' => 20000.00,
                'expired_disc' => Carbon::now()->addMonth()
            ],
        ];
        DB::table('diskons')->insert($data);
    }
}
