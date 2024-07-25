<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'supplier_id' => 'SUPPLIER00001',
                'bisnis_owner_id' => 2,
                'nama_supplier' => 'Supplier A',
                'alamat' => 'Jl. Merdeka 1',
                'kabupaten' => 'Jakarta',
                'provinsi' => 'DKI Jakarta',
                'kode_pos' => '12345',
                'nomor_telepon' => '0211234567',
                'email' => 'supplierA@example.com',
                'website' => 'www.supplierA.com',
                'kontak_person' => 'John Doe',
                'nomor_kontak_person' => '081234567890',
                'email_kontak_person' => 'johndoe@example.com',
                'tipe_supplier' => 'Distributor',
                'nomor_npwp' => '123456789012345',
                'start_pks_date' => '2020-01-01',
                'end_pks_date' => '2020-01-01',
                'catatan_tambahan' => 'Catatan tambahan A',
                'desa' => 'Batu Ampar',
                'kecamatan' => 'Bekasi Timur',
            ],
            [
                'supplier_id' => 'SUPPLIER00002',
                'nama_supplier' => 'Supplier B',
                'bisnis_owner_id' => 2,
                'alamat' => 'Jl. Merdeka 2',
                'kabupaten' => 'Bandung',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '54321',
                'nomor_telepon' => '0221234567',
                'email' => 'supplierB@example.com',
                'website' => 'www.supplierB.com',
                'kontak_person' => 'Jane Doe',
                'nomor_kontak_person' => '082234567890',
                'email_kontak_person' => 'janedoe@example.com',
                'tipe_supplier' => 'Manufacturer',
                'nomor_npwp' => '543210987654321',
                'start_pks_date' => '2020-02-01',
                'end_pks_date' => '2020-02-01',
                'catatan_tambahan' => 'Catatan tambahan B',
                'desa' => 'Batu Ampar',
                'kecamatan' => 'Bekasi Timur',
            ],
            [
                'supplier_id' => 'SUPPLIER00003',
                'nama_supplier' => 'Supplier C',
                'bisnis_owner_id' => 2,
                'alamat' => 'Jl. Merdeka 3',
                'kabupaten' => 'Surabaya',
                'provinsi' => 'Jawa Timur',
                'kode_pos' => '67890',
                'nomor_telepon' => '0311234567',
                'email' => 'supplierC@example.com',
                'website' => 'www.supplierC.com',
                'kontak_person' => 'Jack Smith',
                'nomor_kontak_person' => '083234567890',
                'email_kontak_person' => 'jacksmith@example.com',
                'tipe_supplier' => 'Wholesaler',
                'nomor_npwp' => '678905432109876',
                'start_pks_date' => '2020-03-01',
                'end_pks_date' => '2020-03-01',
                'catatan_tambahan' => 'Catatan tambahan C',
                'desa' => 'Batu Ampar',
                'kecamatan' => 'Bekasi Timur',
            ],
            [
                'supplier_id' => 'SUPPLIER00004',
                'nama_supplier' => 'Supplier D',
                'bisnis_owner_id' => 2,
                'alamat' => 'Jl. Merdeka 4',
                'kabupaten' => 'Medan',
                'provinsi' => 'Sumatera Utara',
                'kode_pos' => '98765',
                'nomor_telepon' => '0611234567',
                'email' => 'supplierD@example.com',
                'website' => 'www.supplierD.com',
                'kontak_person' => 'Jill Johnson',
                'nomor_kontak_person' => '084234567890',
                'email_kontak_person' => 'jilljohnson@example.com',
                'tipe_supplier' => 'Retailer',
                'nomor_npwp' => '987654321098765',
                'start_pks_date' => '2020-04-01',
                'end_pks_date' => '2020-04-01',
                'catatan_tambahan' => 'Catatan tambahan D',
                'desa' => 'Batu Ampar',
                'kecamatan' => 'Bekasi Timur',
            ],
            [
                'supplier_id' => 'SUPPLIER00005',
                'nama_supplier' => 'Supplier E',
                'bisnis_owner_id' => 2,
                'alamat' => 'Jl. Merdeka 5',
                'kabupaten' => 'Makassar',
                'provinsi' => 'Sulawesi Selatan',
                'kode_pos' => '11223',
                'nomor_telepon' => '0411234567',
                'email' => 'supplierE@example.com',
                'website' => 'www.supplierE.com',
                'kontak_person' => 'Jim Brown',
                'nomor_kontak_person' => '085234567890',
                'email_kontak_person' => 'jimbrown@example.com',
                'tipe_supplier' => 'Exporter',
                'nomor_npwp' => '112233445566778',
                'start_pks_date' => '2020-05-01',
                'end_pks_date' => '2020-05-01',
                'catatan_tambahan' => 'Catatan tambahan E',
                'desa' => 'Batu Ampar',
                'kecamatan' => 'Bekasi Timur',
            ],
        ];

        DB::table('suppliers')->insert($suppliers);
    }
}
