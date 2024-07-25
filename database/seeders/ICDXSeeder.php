<?php

namespace Database\Seeders;

use App\Models\Icdx;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ICDXSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Icdx::truncate();
        $csvFile = fopen(base_path("database/data/icdx.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                Icdx::create([
                    "category" => $data['0'],
                    "sub_category" => $data['1'],
                    "en_name" => $data['2'],
                    "id_name" => $data['3'],
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}
