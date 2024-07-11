<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailPenerimaanBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $detilPenerimaanBarangs = [
            [
                'detil_penerimaan_id' => 'DETILPENERIMAAN00001',
                'penerimaan_id' => 'PENERIMAAN00001', // Sesuaikan dengan penerimaan_id yang ada di tabel penerimaan_barangs
                'barang_id' => 'BARANG00001', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 10,
                'jml_datang' => 10,
                'jml_kurang' => 5,
                'kondisi' => 'Baik',
            ],
            [
                'detil_penerimaan_id' => 'DETILPENERIMAAN00002',
                'penerimaan_id' => 'PENERIMAAN00002', // Sesuaikan dengan penerimaan_id yang ada di tabel penerimaan_barangs
                'barang_id' => 'BARANG00002', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 1,
                'jml_datang' => 10,
                'jml_kurang' => 5,
                'kondisi' => 'Baik',
            ],
            [
                'detil_penerimaan_id' => 'DETILPENERIMAAN00003',
                'penerimaan_id' => 'PENERIMAAN00003', // Sesuaikan dengan penerimaan_id yang ada di tabel penerimaan_barangs
                'barang_id' => 'BARANG00003', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 15,
                'jml_datang' => 10,
                'jml_kurang' => 5,
                'kondisi' => 'Baik',
            ],
            [
                'detil_penerimaan_id' => 'DETILPENERIMAAN00004',
                'penerimaan_id' => 'PENERIMAAN00004', // Sesuaikan dengan penerimaan_id yang ada di tabel penerimaan_barangs
                'barang_id' => 'BARANG00004', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 10,
                'jml_datang' => 10,
                'jml_kurang' => 5,
                'kondisi' => 'Baik',
            ],
            [
                'detil_penerimaan_id' => 'DETILPENERIMAAN00005',
                'penerimaan_id' => 'PENERIMAAN00005', // Sesuaikan dengan penerimaan_id yang ada di tabel penerimaan_barangs
                'barang_id' => 'BARANG00005', // Sesuaikan dengan barang_id yang ada di tabel barangs
                'jumlah' => 5,
                'jml_datang' => 10,
                'jml_kurang' => 5,
                'kondisi' => 'Baik',
            ],
        ];

        DB::table('detail_penerimaan_barangs')->insert($detilPenerimaanBarangs);
    }
}
