<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailPembelianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $detilPembelians = [
            [
                'detil_po_id' => 'DETILPO00001',
                'po_id' => 'PO00001', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'barang_id' => 'BARANG00001', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 10,
                'harga_satuan' => 12000.00,
                'total_harga' => 120000.00
            ],
            [
                'detil_po_id' => 'DETILPO00002',
                'po_id' => 'PO00002', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'barang_id' => 'BARANG00002', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 1,
                'harga_satuan' => 55000.00,
                'total_harga' => 55000.00
            ],
            [
                'detil_po_id' => 'DETILPO00003',
                'po_id' => 'PO00003', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'barang_id' => 'BARANG00003', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 15,
                'harga_satuan' => 16000.00,
                'total_harga' => 240000.00
            ],
            [
                'detil_po_id' => 'DETILPO00004',
                'po_id' => 'PO00004', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'barang_id' => 'BARANG00004', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 10,
                'harga_satuan' => 21000.00,
                'total_harga' => 210000.00
            ],
            [
                'detil_po_id' => 'DETILPO00005',
                'po_id' => 'PO00005', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'barang_id' => 'BARANG00005', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 5,
                'harga_satuan' => 52000.00,
                'total_harga' => 260000.00
            ],
        ];

        DB::table('detail_pembelians')->insert($detilPembelians);
    }
}
