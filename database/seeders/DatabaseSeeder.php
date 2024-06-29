<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AccessFasyankes;
use App\Models\BisnisOwner;
use App\Models\BoInfo;
use App\Models\Fasyankes;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $bo = BisnisOwner::create([
            'name' => 'Dwi Arya Putra',
            'email' => 'test@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password')
        ]);
        $bo->markEmailAsVerified();


        $bo2 = BisnisOwner::create([
            'name' => 'teguh',
            'email' => 'teguh@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password')
        ]);
        $bo2->markEmailAsVerified();

        $warehouse = Warehouse::create([
            'bisnis_owner_id' => 2,
            'name' => 'Warehouse Teguh Test',
            'address' => 'Jalan',
            'pic' => 'Teguh',
            'contact' => '08293492739427',
        ]);
        $fasyankes = Fasyankes::create([
            'fasyankesId' => '12345678',
            'bisnis_owner_id' => 2,
            'type' => 'Klinik',
            'warehouse_id' => 1,
            'name' => 'Test Fasyankes',
            'address' => 'Jalan',
            'pic' => 'Teguh',
            'pic_number' => '0893849238293',
            'email' => 'teguh@gmail.com',
            'is_active' => 1,
        ]);

        $accessFasyankes = AccessFasyankes::create([
            'fasyankes_id' => '12345678',
            'username' => 'USNTEST0001',
            'password' => Hash::make('password123'),
            'is_active' => 1,
            'created_by' => 'Teguh',
            'role' => 'admin',
            'id_profile' => null
        ]);
    }
}
