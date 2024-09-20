<?php

namespace App\Livewire\Vouchers;

use App\Imports\VoucherUpload;
use App\Models\Package;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

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

        Excel::import(new VoucherUpload($this->package), $this->voucherFile);

        $this->dispatch('uploaded-vouchers');
        $this->reset(['voucherFile', 'package']);
    }

    public function render()
    {
        return view('livewire.vouchers.new-voucher-upload-form',[
            'packages' => Package::all(),
        ]);
    }
}
