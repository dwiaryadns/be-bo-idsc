<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplierBarangsSeeder extends Seeder
{
    public function run()
    {
        $supplierBarangs = [
            [
                'supplier_barang_id' => 'SUPPLIERBARANG00001',
                'supplier_id' => 'SUPPLIER00001',
                'barang_id' => 'BARANG00001',
                'harga' => 12000.00,
            ],
            [
                'supplier_barang_id' => 'SUPPLIERBARANG00002',
                'supplier_id' => 'SUPPLIER00001',
                'barang_id' => 'BARANG00002',
                'harga' => 55000.00,
            ],
            [
                'supplier_barang_id' => 'SUPPLIERBARANG00003',
                'supplier_id' => 'SUPPLIER00002',
                'barang_id' => 'BARANG00003',
                'harga' => 16000.00,
            ],
            [
                'supplier_barang_id' => 'SUPPLIERBARANG00004',
                'supplier_id' => 'SUPPLIER00002',
                'barang_id' => 'BARANG00004',
                'harga' => 21000.00,
            ],
            [
                'supplier_barang_id' => 'SUPPLIERBARANG00005',
                'supplier_id' => 'SUPPLIER00003',
                'barang_id' => 'BARANG00005',
                'harga' => 52000.00,
            ],
        ];

        DB::table('supplier_barangs')->insert($supplierBarangs);
    }
}
