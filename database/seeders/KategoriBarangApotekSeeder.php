<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriBarangApotekSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Obat Resep (Rx)',
            'Obat Bebas Terbatas',
            'Obat Bebas',
            'Vitamin',
            'Mineral',
            'Suplemen Herbal dan Alami',
            'Produk Perawatan Kulit',
            'Produk Perawatan Rambut',
            'Produk Perawatan Mulut',
            'Susu Formula dan Makanan Bayi',
            'Produk Perawatan Bayi',
            'Suplemen untuk Ibu Hamil dan Menyusui',
            'Alat Pengukur Tekanan Darah',
            'Termometer',
            'Alat Tes Gula Darah',
            'Masker dan Sarung Tangan Medis',
            'Perban dan Plester',
            'Alat Bantu Jalan',
            'Produk Rehabilitasi Medis',
            'Kontrasepsi',
            'Produk Kebersihan Menstruasi',
            'Obat-Obatan untuk Hewan Peliharaan',
            'Suplemen dan Vitamin Hewan',
            'Sabun Cuci Tangan',
            'Sanitizer',
            'Produk Kebersihan Rumah Tangga',
            'Makanan Organik',
            'Minuman Berenergi dan Isotonik',
            'Camilan Sehat',
        ];

        foreach ($categories as $index => $category) {
            DB::table('kategori_barang_apoteks')->insert([
                'kategori_id' => 'KATEGORI0000' . $index + 1,
                'nama' => $category
            ]);
        }
    }
}
