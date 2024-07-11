<?php

namespace Database\Seeders;

use App\Models\GoodReceiptNote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GoodReceiptNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        GoodReceiptNote::create([
            'grn_id' => 'GRN-' . date('Y') . date('m') . '00001',
            'penerimaan_id' => 'PENERIMAAN00001',
            'url_file' => 'LINK FILE DISINI',
        ]);
        GoodReceiptNote::create([
            'grn_id' => 'GRN-' . date('Y') . date('m') . '00002',
            'penerimaan_id' => 'PENERIMAAN00002',
            'url_file' => 'LINK FILE DISINI',
        ]);
        GoodReceiptNote::create([
            'grn_id' => 'GRN-' . date('Y') . date('m') . '00003',
            'penerimaan_id' => 'PENERIMAAN00003',
            'url_file' => 'LINK FILE DISINI',
        ]);
        GoodReceiptNote::create([
            'grn_id' => 'GRN-' . date('Y') . date('m') . '00004',
            'penerimaan_id' => 'PENERIMAAN00004',
            'url_file' => 'LINK FILE DISINI',
        ]);
        GoodReceiptNote::create([
            'grn_id' => 'GRN-' . date('Y') . date('m') . '00005',
            'penerimaan_id' => 'PENERIMAAN00005',
            'url_file' => 'LINK FILE DISINI',
        ]);
    }
}
