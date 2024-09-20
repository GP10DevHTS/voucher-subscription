<?php

namespace App\Imports;

use App\Models\Package;
use App\Models\Voucher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VoucherUpload implements ToModel
{
    public $package;

    public function __construct($package)
    {
        $this->package = Package::where('slug', $package)->firstOrFail();
    }
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new Voucher([
           'name'     => $row[0],
           'package_id'    => $this->package->id, 
           'is_sold' => false,
        ]);
    }

   
}
