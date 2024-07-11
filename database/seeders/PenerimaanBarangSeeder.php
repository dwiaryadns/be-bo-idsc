<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenerimaanBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penerimaanBarangs = [
            [
                'penerimaan_id' => 'PENERIMAAN00001',
                'po_id' => 'PO00001', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'fasyankes_warehouse_id' => 'WFID00001', // Sesuaikan dengan fasyankes_warehouse_WFIDda di tabel warehouse_fasyankes
                'tanggal_penerimaan' => '2020-01-02',
                'status' => 'Pending',
                'catatan' => 'Penerimaan barang pertama',
                'penerima' => 'Dimas',
                'pengecek' => 'Arya',
                'pengirim' => 'Febbe'
            ],
            [
                'penerimaan_id' => 'PENERIMAAN00002',
                'po_id' => 'PO00002', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'fasyankes_warehouse_id' => 'WFID00001', // Sesuaikan dengan fasyankes_warehouse_WFIDda di tabel warehouse_fasyankes
                'tanggal_penerimaan' => '2020-02-02',
                'status' => 'Completed',
                'catatan' => 'Penerimaan barang kedua',
                'penerima' => 'Dimas',
                'pengecek' => 'Arya',
                'pengirim' => 'Febbe'
            ],
            [
                'penerimaan_id' => 'PENERIMAAN00003',
                'po_id' => 'PO00003', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'fasyankes_warehouse_id' => 'WFID00002', // Sesuaikan dengan fasyankes_warehouse_WFIDda di tabel warehouse_fasyankes
                'tanggal_penerimaan' => '2020-03-02',
                'status' => 'Pending',
                'catatan' => 'Penerimaan barang ketiga',
                'penerima' => 'Dimas',
                'pengecek' => 'Arya',
                'pengirim' => 'Febbe'
            ],
            [
                'penerimaan_id' => 'PENERIMAAN00004',
                'po_id' => 'PO00004', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'fasyankes_warehouse_id' => 'WFID00002', // Sesuaikan dengan fasyankes_warehouse_WFIDda di tabel warehouse_fasyankes
                'tanggal_penerimaan' => '2020-04-02',
                'status' => 'Pending',
                'catatan' => 'Penerimaan barang keempat',
                'penerima' => 'Dimas',
                'pengecek' => 'Arya',
                'pengirim' => 'Febbe'
            ],
            [
                'penerimaan_id' => 'PENERIMAAN00005',
                'po_id' => 'PO00005', // Sesuaikan dengan po_id yang ada di tabel pembelians
                'fasyankes_warehouse_id' => 'WFID00002', // Sesuaikan dengan fasyankes_warehouse_WFIDda di tabel warehouse_fasyankes
                'tanggal_penerimaan' => '2020-05-02',
                'status' => 'Completed',
                'catatan' => 'Penerimaan barang kelima',
                'penerima' => 'Dimas',
                'pengecek' => 'Arya',
                'pengirim' => 'Febbe'
            ],
        ];

        DB::table('penerimaan_barangs')->insert($penerimaanBarangs);
    }
}
