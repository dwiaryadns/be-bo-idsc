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
                'supplier_id' => 'SUPPLIER00001', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'barang_id' => 'BARANG00001', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'harga' => 12000.00,
                'tanggal_mulai' => '2020-01-01',
                'tanggal_berakhir' => '2021-01-01'
            ],
            [
                'supplier_barang_id' => 'SUPPLIERBARANG00002',
                'supplier_id' => 'SUPPLIER00001', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'barang_id' => 'BARANG00002', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'harga' => 55000.00,
                'tanggal_mulai' => '2020-02-01',
                'tanggal_berakhir' => '2021-02-01'
            ],
            [
                'supplier_barang_id' => 'SUPPLIERBARANG00003',
                'supplier_id' => 'SUPPLIER00002', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'barang_id' => 'BARANG00003', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'harga' => 16000.00,
                'tanggal_mulai' => '2020-03-01',
                'tanggal_berakhir' => '2021-03-01'
            ],
            [
                'supplier_barang_id' => 'SUPPLIERBARANG00004',
                'supplier_id' => 'SUPPLIER00002', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'barang_id' => 'BARANG00004', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'harga' => 21000.00,
                'tanggal_mulai' => '2020-04-01',
                'tanggal_berakhir' => '2021-04-01'
            ],
            [
                'supplier_barang_id' => 'SUPPLIERBARANG00005',
                'supplier_id' => 'SUPPLIER00003', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'barang_id' => 'BARANG00005', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'harga' => 52000.00,
                'tanggal_mulai' => '2020-05-01',
                'tanggal_berakhir' => '2021-05-01'
            ],
        ];

        DB::table('supplier_barangs')->insert($supplierBarangs);
    }
}
