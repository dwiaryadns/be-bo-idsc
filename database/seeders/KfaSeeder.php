<?php

namespace Database\Seeders;

use App\Models\MasterKfa;
use App\Models\MasterKfaPov;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KfaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kfa = MasterKfa::create([
            'kfa_code_idsc' => 'KFA0000001',
            'kfa_code' => '91000101',
            'bza_desc' => 'Paracetamol'
        ]);

        $kfaPov = DB::table('master_kfa_povs')->insert([
            [
                'kfa_pov_code' => 92000855,
                'kfa_code' => 91000101,
                'kfa_pov_idsc' => 'POV0000001',
                'pov_desc' => 'Paracetamol 10 mg/mL Infus',
                'product_state' => 'Obat Keras',
            ],
            [
                'kfa_pov_code' => 92001230,
                'kfa_code' => 91000101,
                'kfa_pov_idsc' => 'POV0000002',
                'pov_desc' => 'Paracetamol 600 mg / Phenypropanolamine Hydrochloride 15 mg / Dextromethorphan Hydrobromide 15 mg / Guaifenesin 100 mg / Chlorphenamine Maleate 2 mg Tablet',
                'product_state' => 'Obat Bebas Terbatas',
            ],
            [
                'kfa_pov_code' => 92001267,
                'kfa_code' => 91000101,
                'kfa_pov_idsc' => 'POV0000003',
                'pov_desc' => 'Paracetamol 500 mg Tablet',
                'product_state' => 'Obat Bebas',
            ],
            [
                'kfa_pov_code' => 92001268,
                'kfa_code' => 91000101,
                'kfa_pov_idsc' => 'POV0000003',
                'pov_desc' => 'Paracetamol 500 mg Tablet',
                'product_state' => 'Obat Bebas',
            ],
            [
                'kfa_pov_code' => 92001269,
                'kfa_code' => 91000101,
                'kfa_pov_idsc' => 'POV0000003',
                'pov_desc' => 'Paracetamol 500 mg Tablet',
                'product_state' => 'Obat Bebas',
            ],
            [
                'kfa_pov_code' => 92001270,
                'kfa_code' => 91000101,
                'kfa_pov_idsc' => 'POV0000003',
                'pov_desc' => 'Paracetamol 500 mg Tablet',
                'product_state' => 'Obat Bebas',
            ],
            [
                'kfa_pov_code' => 92001271,
                'kfa_code' => 91000101,
                'kfa_pov_idsc' => 'POV0000003',
                'pov_desc' => 'Paracetamol 500 mg Tablet',
                'product_state' => 'Obat Bebas',
            ],
            [
                'kfa_pov_code' => 92001272,
                'kfa_code' => 91000101,
                'kfa_pov_idsc' => 'POV0000003',
                'pov_desc' => 'Paracetamol 500 mg Tablet',
                'product_state' => 'Obat Bebas',
            ],
            [
                'kfa_pov_code' => 92001273,
                'kfa_code' => 91000101,
                'kfa_pov_idsc' => 'POV0000003',
                'pov_desc' => 'Paracetamol 500 mg Tablet',
                'product_state' => 'Obat Bebas',
            ],
        ]);

        DB::table('master_kfa_poas')->insert([
            [
                'kfa_poa_code' => 93012420,
                'kfa_pov_code' => 92000855,
                'kfa_poa_idsc' => 'POA0000001',
                'poa_desc' => 'Paracetamol 10 mg/mL Infus (Umum)',
                'manufacture' => null,
                'generic_flag' => true,
                'made_in' => 'LOKAL',
                'kfa_code_poak' => null,
                'pack_type' => null,
                'estimate_pack_price' => null,
            ],
            [
                'kfa_poa_code' => 93014390,
                'kfa_pov_code' => 92000855,
                'kfa_poa_idsc' => 'POA0000002',
                'poa_desc' => 'Paracetamol 10 mg/mL Infus (Mesfarma Timika; Merucana, 100 mL)',
                'manufacture' => 'Mesfarma Timika; Merucana',
                'generic_flag' => true,
                'made_in' => 'LOKAL',
                'kfa_code_poak' => null,
                'pack_type' => null,
                'estimate_pack_price' => null,
            ],
            [
                'kfa_poa_code' => 93040956,
                'kfa_pov_code' => 92000855,
                'kfa_poa_idsc' => 'POA0000003',
                'poa_desc' => 'Paracetamol 10 mg/mL Infus (Paradol, 100 mL)',
                'manufacture' => 'Global Multi Pharmalab',
                'generic_flag' => false,
                'made_in' => 'LOKAL',
                'kfa_code_poak' => null,
                'pack_type' => null,
                'estimate_pack_price' => null,
            ],
            [
                'kfa_poa_code' => 93041380,
                'kfa_pov_code' => 92001230,
                'kfa_poa_idsc' => 'POA0000004',
                'poa_desc' => 'Paracetamol 600 mg / Phenypropanolamine Hydrochloride 15 mg / Dextromethorphan Hydrobromide 15 mg / Guaifenesin 100 mg / Chlorphenamine Maleate 2 mg Tablet (ULTRAFIL)',
                'manufacture' => 'Hanson Farma',
                'generic_flag' => false,
                'made_in' => 'LOKAL',
                'kfa_code_poak' => '94002694',
                'pack_type' => 'Dus isi 100',
                'estimate_pack_price' => 86250,
            ],
            [
                'kfa_poa_code' => 93060192,
                'kfa_pov_code' => 92001267,
                'kfa_poa_idsc' => 'POA0000005',
                'poa_desc' => 'Paracetamol 500 mg Tablet (INTUNYA META RATNA PHARMINDO)',
                'manufacture' => 'INTUNYA META RATNA PHARMINDO',
                'generic_flag' => false,
                'made_in' => 'LOKAL',
                'kfa_code_poak' => '94008928',
                'pack_type' => 'Dus isi 100',
                'estimate_pack_price' => 27500,
            ],
            [
                'kfa_poa_code' => 93060199,
                'kfa_pov_code' => 92001267,
                'kfa_poa_idsc' => 'POA0000005',
                'poa_desc' => 'Paracetamol 500 mg Tablet (INTUNYA META RATNA PHARMINDO)',
                'manufacture' => 'INTUNYA META RATNA PHARMINDO',
                'generic_flag' => false,
                'made_in' => 'LOKAL',
                'kfa_code_poak' => '94008928',
                'pack_type' => 'Dus isi 100',
                'estimate_pack_price' => 27500,
            ],
            [
                'kfa_poa_code' => 93060196,
                'kfa_pov_code' => 92001267,
                'kfa_poa_idsc' => 'POA0000005',
                'poa_desc' => 'Paracetamol 500 mg Tablet (INTUNYA META RATNA PHARMINDO)',
                'manufacture' => 'INTUNYA META RATNA PHARMINDO',
                'generic_flag' => false,
                'made_in' => 'LOKAL',
                'kfa_code_poak' => '94008928',
                'pack_type' => 'Dus isi 100',
                'estimate_pack_price' => 27500,
            ],
            [
                'kfa_poa_code' => 93060193,
                'kfa_pov_code' => 92001267,
                'kfa_poa_idsc' => 'POA0000005',
                'poa_desc' => 'Paracetamol 500 mg Tablet (INTUNYA META RATNA PHARMINDO)',
                'manufacture' => 'INTUNYA META RATNA PHARMINDO',
                'generic_flag' => false,
                'made_in' => 'LOKAL',
                'kfa_code_poak' => '94008928',
                'pack_type' => 'Dus isi 100',
                'estimate_pack_price' => 27500,
            ],
        ]);
    }
}
