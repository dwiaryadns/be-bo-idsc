<?php

namespace Database\Seeders;

use App\Models\MasterPoa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterPOASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        MasterPoa::truncate();
        $csvFile = fopen(base_path("database/data/poa.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                MasterPoa::create([
                    "id_idsc" => $data['0'],
                    "poa_code" => $data['1'],
                    "pov" => $data['2'],
                    "poa" => $data['3'],
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}
