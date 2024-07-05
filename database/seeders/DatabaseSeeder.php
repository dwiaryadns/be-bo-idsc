<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AccessFasyankes;
use App\Models\BisnisOwner;
use App\Models\BoInfo;
use App\Models\Fasyankes;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            'contact' => '08293492710239',
        ]);
        $warehouse2 = Warehouse::create([
            'bisnis_owner_id' => 2,
            'name' => 'Warehouse Arya Test',
            'address' => 'Jalan',
            'pic' => 'Arya',
            'contact' => '08293492710239',
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
        $fasyankes2 = Fasyankes::create([
            'fasyankesId' => '123456789',
            'bisnis_owner_id' => 2,
            'type' => 'Klinik',
            'warehouse_id' => 1,
            'name' => 'Test Fasyankes 2',
            'address' => 'Jalan',
            'pic' => 'Teguh',
            'pic_number' => '0893849238293',
            'email' => 'arya@gmail.com',
            'is_active' => 1,
        ]);
        $fasyankesWarehouses = [
            [
                'wfid' => 'WFID00001',
                'fasyankes_id' => $fasyankes->fasyankesId,
                'warehouse_id' => $warehouse->id,
            ],
            [
                'wfid' => 'WFID00002',
                'fasyankes_id' => $fasyankes2->fasyankesId,
                'warehouse_id' => $warehouse2->id,
            ],
        ];

        DB::table('fasyankes_warehouse')->insert($fasyankesWarehouses);

        $accessFasyankes = AccessFasyankes::create([
            'fasyankes_id' => '12345678',
            'username' => 'USNTEST0001',
            'password' => Hash::make('password123'),
            'is_active' => 1,
            'created_by' => 'Teguh',
            'role' => 'admin',
            'id_profile' => null
        ]);

        $this->call(KfaSeeder::class);
        $this->call(KategoriBarangApotekSeeder::class);
        $this->call(SuppliersSeeder::class);
        $this->call(BarangsSeeder::class);
        $this->call(SupplierBarangsSeeder::class);
        $this->call(StockBarangsSeeder::class);
        $this->call(PembelianSeeder::class);
        $this->call(DetailPembelianSeeder::class);
        $this->call(PenerimaanBarangSeeder::class);
        $this->call(DetailPenerimaanBarangSeeder::class);
    }
}
