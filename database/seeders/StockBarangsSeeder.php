<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockBarangsSeeder extends Seeder
{
    public function run()
    {
        $stokBarangs = [
            [
                'stok_barang_id' => 'STOKBARANG00001',
                'fasyankes_warehouse_id' => 'WFID00001',
                'barang_id' => 'BARANG00001',
                'stok' => 200
            ],
            [
                'stok_barang_id' => 'STOKBARANG00002',
                'fasyankes_warehouse_id' => 'WFID00001',
                'barang_id' => 'BARANG00002',
                'stok' => 50
            ],
            [
                'stok_barang_id' => 'STOKBARANG00003',
                'fasyankes_warehouse_id' => 'WFID00002',
                'barang_id' => 'BARANG00001',
                'stok' => 200
            ],
            [
                'stok_barang_id' => 'STOKBARANG00004',
                'fasyankes_warehouse_id' => 'WFID00002',
                'barang_id' => 'BARANG00002',
                'stok' => 50
            ],
            [
                'stok_barang_id' => 'STOKBARANG00005',
                'fasyankes_warehouse_id' => 'WFID00002',
                'barang_id' => 'BARANG00005',
                'stok' => 125
            ],
        ];

        DB::table('stock_barangs')->insert($stokBarangs);
    }
}
