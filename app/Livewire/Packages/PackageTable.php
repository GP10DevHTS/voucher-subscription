<?php

namespace App\Livewire\Packages;

use App\Models\Package;
use App\Models\Voucher;
use Livewire\Component;

class PackageTable extends Component
{
    public function render()
    {
        // Get packages including soft-deleted ones
        return view('livewire.packages.package-table', [
            'packages' => Package::withTrashed()->get(),
        ]);
    }

    // Method to deactivate a package and its unsold vouchers
    public function deactivate($slug)
    {
        // Find the package by its slug
        $package = Package::where('slug', $slug)->firstOrFail();

        // Soft delete the package
        $package->delete();

        // Soft delete associated unsold vouchers
        Voucher::where('package_id', $package->id)
            ->where('is_sold', false)
            ->delete();
        
        // Emit success message (optional)
        session()->flash('message', 'Package and associated unsold vouchers deactivated successfully.');
    }

    // Method to reactivate a package and its unsold vouchers
    public function reactivate($slug)
    {
        // Find the soft-deleted package by its slug
        $package = Package::onlyTrashed()->where('slug', $slug)->firstOrFail();

        // Restore the package
        $package->restore();

        // Restore the associated unsold vouchers
        Voucher::onlyTrashed()
            ->where('package_id', $package->id)
            ->where('is_sold', false)
            ->restore();
            // ->each(function ($voucher) {
            //     $voucher->restore();
            // });

        // Emit success message (optional)
        session()->flash('message', 'Package and associated unsold vouchers reactivated successfully.');
    }
}

