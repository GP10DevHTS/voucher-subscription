<div class="p-6">
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div class="grid grid-cols-1 md:grid-cols-2">
        <div>
            <x-section-title>
                <x-slot name="title">{{ __('Voucher Manager') }}</x-slot>
                <x-slot name="description">{{ __('Manage your Vouchers.') }}</x-slot>
            </x-section-title>
        </div>
        <div class="flex justify-end gap-4">
            @livewire('vouchers.new-voucher-form')

            @livewire('vouchers.new-voucher-upload-form')
        </div>
    </div>

    <div>
        @livewire('vouchers.voucher-table')
    </div>
</div>
