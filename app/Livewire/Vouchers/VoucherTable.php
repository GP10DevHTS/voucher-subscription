<?php

namespace App\Livewire\Vouchers;

use App\Models\Voucher;
use App\Models\Package;
use Livewire\Component;

class VoucherTable extends Component
{
    public $search = '';
    public $filterPackage = '';
    public $perPage;

    public function render()
    {
        // Query vouchers with search and package filter
        $vouchers = Voucher::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterPackage, function ($query) {
                $query->where('package_id', $this->filterPackage);
            })
            ->with('package') // Eager load package relationship
            ->paginate($this->perPage ??= 50);

        // Get all packages for filtering dropdown
        $packages = Package::all();

        return view('livewire.vouchers.voucher-table', [
            'vouchers' => $vouchers,
            'packages' => $packages
        ]);
    }
}

