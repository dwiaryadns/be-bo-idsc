<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembelianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pembelians = [
            [
                'po_id' => 'PO00001',
                'po_name' => 'PO KE-1',
                'supplier_id' => 'SUPPLIER00001',
                'fasyankes_warehouse_id' => 'WFID00001',
                'tanggal_po' => '2020-01-01',
                'status' => 'Received',
                'total_harga' => 120000.00,
                'catatan' => 'Pembelian pertama'
            ],
            [
                'po_id' => 'PO00002',
                'po_name' => 'PO KE-2',
                'supplier_id' => 'SUPPLIER00001',
                'fasyankes_warehouse_id' => 'WFID00001',
                'tanggal_po' => '2020-02-01',
                'status' => 'Order',
                'total_harga' => 55000.00,
                'catatan' => 'Pembelian kedua'
            ],
            [
                'po_id' => 'PO00003',
                'po_name' => 'PO KE-3',
                'supplier_id' => 'SUPPLIER00002',
                'fasyankes_warehouse_id' => 'WFID00002',
                'tanggal_po' => '2020-03-01',
                'status' => 'Order',
                'total_harga' => 160000.00,
                'catatan' => 'Pembelian ketiga'
            ],
            [
                'po_id' => 'PO00004',
                'po_name' => 'PO KE-4',
                'supplier_id' => 'SUPPLIER00002',
                'fasyankes_warehouse_id' => 'WFID00002',
                'tanggal_po' => '2020-04-01',
                'status' => 'Order',
                'total_harga' => 210000.00,
                'catatan' => 'Pembelian keempat'
            ],
            [
                'po_id' => 'PO00005',
                'po_name' => 'PO KE-5',
                'supplier_id' => 'SUPPLIER00003',
                'fasyankes_warehouse_id' => 'WFID00002',
                'tanggal_po' => '2020-05-01',
                'status' => 'Received',
                'total_harga' => 52000.00,
                'catatan' => 'Pembelian kelima'
            ],
        ];

        DB::table('pembelians')->insert($pembelians);
    }
}
