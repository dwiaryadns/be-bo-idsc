<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\BisnisOwner;
use App\Models\BoInfo;
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

        // BoInfo::create([
        //     'bisnis_owner_id' => 1,
        //     'businessId' => 'BO000' . rand(10000, 99999),
        //     'businessType' => 'individual',
        //     'businessName' => 'John Doe',
        //     'businessEmail' => 'john.doe@example.com',
        //     'phone' => '123-456-7890',
        //     'mobile' => '098-765-4321',
        //     'address' => '123 Main St, Anytown',
        //     'province' => 'Province1',
        //     'city' => 'City1',
        //     'village' => 'Village',
        //     'subdistrict' => 'Subdistrict1',
        //     'postal_code' => '12345',
        // ],);
    }
}
