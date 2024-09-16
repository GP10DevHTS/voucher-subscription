<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherPackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages  = [
            [
                'name' => 'Daily',
                'description' => "24 Hours unlimited",
                'price' => 1000,
                'currency' => 'UGX',
            ],

            [
                'name' => 'Weekly',
                'description' => "7 Days unlimited",
                'price' => 5000,
                'currency' => 'UGX',
            ],

            [
                'name' => 'Monthly',
                'description' => "30 Days unlimited",
                'price' => 25000,
                'currency' => 'UGX',
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
            $this->command->info("Created Package: " . $package['name']);
        }
    }
}
