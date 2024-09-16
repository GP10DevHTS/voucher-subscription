<?php

namespace App\Livewire\Vouchers;

use App\Models\Package;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class NewVoucherUploadForm extends Component
{
    use WithFileUploads;

    public $voucherFile;
    public $package;

    public $uploadModal_isOpen = false;

    public function openUploadModal()
    {
        $this->uploadModal_isOpen = true;
    }

    public function closeUploadModal()
    {
        $this->uploadModal_isOpen = false;
    }


    public function uploadVouchers()
    {
        $this->validate([
            'voucherFile' => 'required|mimes:csv,xls,xlsx',
            'package'  => 'required|exists:packages,slug',
        ]);
    }

    public function render()
    {
        return view('livewire.vouchers.new-voucher-upload-form',[
            'packages' => Package::all(),
        ]);
    }
}
