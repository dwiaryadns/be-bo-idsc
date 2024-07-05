<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BarangsSeeder extends Seeder
{
    public function run()
    {
        $barangs = [
            [
                'barang_id' => 'BARANG00001',
                'nama_barang' => 'Paracetamol',
                'kfa_poa_code'=> 93012420,
                'kategori_id' => 'KATEGORI00001',
                'satuan' => 'Box',
                'harga_beli' => 10000.00,
                'harga_jual' => 15000.00,
                'deskripsi' => 'Obat pereda nyeri dan penurun demam.'
            ],
            [
                'barang_id' => 'BARANG00002',
                'nama_barang' => 'Vitamin C',
                'kfa_poa_code'=> null,
                'kategori_id' => 'KATEGORI00004',
                'satuan' => 'Botol',
                'harga_beli' => 50000.00,
                'harga_jual' => 60000.00,
                'deskripsi' => 'Suplemen untuk meningkatkan daya tahan tubuh.'
            ],
            [
                'barang_id' => 'BARANG00003',
                'nama_barang' => 'Aspirin',
                'kfa_poa_code'=> null,
                'kategori_id' => 'KATEGORI00002',
                'satuan' => 'Box',
                'harga_beli' => 15000.00,
                'harga_jual' => 20000.00,
                'deskripsi' => 'Obat pereda nyeri, antiinflamasi, dan antikoagulan.'
            ],
            [
                'barang_id' => 'BARANG00004',
                'nama_barang' => 'Hand Sanitizer',
                'kfa_poa_code'=> null,
                'kategori_id' => 'KATEGORI000025',
                'satuan' => 'Botol',
                'harga_beli' => 20000.00,
                'harga_jual' => 25000.00,
                'deskripsi' => 'Cairan antiseptik untuk membersihkan tangan.'
            ],
            [
                'barang_id' => 'BARANG00005',
                'nama_barang' => 'Thermometer',
                'kfa_poa_code'=> null,
                'kategori_id' => 'KATEGORI000014',
                'satuan' => 'Unit',
                'harga_beli' => 50000.00,
                'harga_jual' => 60000.00,
                'deskripsi' => 'Alat untuk mengukur suhu tubuh.'
            ],
        ];

        DB::table('barangs')->insert($barangs);
    }
}
