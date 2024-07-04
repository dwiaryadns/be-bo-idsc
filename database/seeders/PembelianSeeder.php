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
                'supplier_id' => 'SUPPLIER00001', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'fasyankes_warehouse_id' => 'WFID00001', // Sesuaikan dengan warehouse_id yang ada di tabel warehouse_fasyankes
                'tanggal_po' => '2020-01-01',
                'status' => 'Pending',
                'total_harga' => 120000.00,
                'catatan' => 'Pembelian pertama'
            ],
            [
                'po_id' => 'PO00002',
                'supplier_id' => 'SUPPLIER00001', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'fasyankes_warehouse_id' => 'WFID00001', // Sesuaikan dengan warehouse_id yang ada di tabel warehouse_fasyankes
                'tanggal_po' => '2020-02-01',
                'status' => 'Confirmed',
                'total_harga' => 55000.00,
                'catatan' => 'Pembelian kedua'
            ],
            [
                'po_id' => 'PO00003',
                'supplier_id' => 'SUPPLIER00002', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'fasyankes_warehouse_id' => 'WFID00002', // Sesuaikan dengan warehouse_id yang ada di tabel warehouse_fasyankes
                'tanggal_po' => '2020-03-01',
                'status' => 'Shipped',
                'total_harga' => 160000.00,
                'catatan' => 'Pembelian ketiga'
            ],
            [
                'po_id' => 'PO00004',
                'supplier_id' => 'SUPPLIER00002', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'fasyankes_warehouse_id' => 'WFID00002', // Sesuaikan dengan warehouse_id yang ada di tabel warehouse_fasyankes
                'tanggal_po' => '2020-04-01',
                'status' => 'Completed',
                'total_harga' => 210000.00,
                'catatan' => 'Pembelian keempat'
            ],
            [
                'po_id' => 'PO00005',
                'supplier_id' => 'SUPPLIER00003', // Sesuaikan dengan supplier_id yang ada di tabel suppliers
                'fasyankes_warehouse_id' => 'WFID00002', // Sesuaikan dengan warehouse_id yang ada di tabel warehouse_fasyankes
                'tanggal_po' => '2020-05-01',
                'status' => 'Cancelled',
                'total_harga' => 52000.00,
                'catatan' => 'Pembelian kelima'
            ],
        ];

        DB::table('pembelians')->insert($pembelians);
    }
}
