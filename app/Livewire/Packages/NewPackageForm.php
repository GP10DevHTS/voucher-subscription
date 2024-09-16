<?php

namespace App\Livewire\Packages;

use App\Models\Package;
use Livewire\Component;

class NewPackageForm extends Component
{
    public $newpackage_open = false;

    public $name;
    public $description;
    public $price;
    public $currency = 'UGX';

    public function openCreatePackageModal()
    {
        $this->newpackage_open = true;
    }

    public function createPackage()
    {
        $this->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:UGX,USD',
        ]);

        Package::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
        ]);
        $this->closeCreatePackageModal();
        session()->flash('flash.banner', 'Yay it works!');
        session()->flash('flash.bannerStyle', 'success');
    }

    public function closeCreatePackageModal()
    {
        $this->reset();
        $this->newpackage_open = false;
    }

    public function render()
    {
        return view('livewire.packages.new-package-form');
    }
}
