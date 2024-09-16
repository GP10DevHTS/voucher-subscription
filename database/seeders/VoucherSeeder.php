<?php 

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = Package::all();

        for ($i = 0; $i <= 10000; $i++) {

            // Randomly select a package
            $package = $packages->random();

            do {
                $code = mt_rand(100000, 99999999); // 6 to 8 digit random number
            } while (Voucher::where('name', $code)->exists());

            Voucher::create([
                'package_id' => $package->id,
                'name' => $code,
            ]);
        }
    }
}
