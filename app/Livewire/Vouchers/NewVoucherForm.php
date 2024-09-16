<?php

namespace App\Livewire\Vouchers;

use App\Models\Package;
use App\Models\Voucher;
use Livewire\Component;
use Illuminate\Support\Str;

class NewVoucherForm extends Component
{
    public $code;
    public $package;

    public $newVoucherForm_isOpen = false;

    public function openNewVoucherForm(){
        $this->newVoucherForm_isOpen = true;
    }

    public function closeNewVoucherForm(){
        $this->newVoucherForm_isOpen = false;
        $this->reset();
    }

    public function createVoucher(){

        // dd($this->package, $this->code);
        $this->validate([
            'code' => 'required',
            'package' => 'required|exists:packages,slug',
        ]);

        $package = Package::where('slug', $this->package)->firstOrFail();
         
        if($package){
            Voucher::create([
                'name' => Str::slug($this->code) ,
                'package_id' => $package->id
            ]);
    
            $this->closeNewVoucherForm();
        }
    }

    public function render()
    {
        return view('livewire.vouchers.new-voucher-form',[
            'packages' => Package::all(),
        ]);
    }
}
