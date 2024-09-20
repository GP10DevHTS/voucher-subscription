<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <x-button wire:click="openUploadModal">Voucher Upload</x-button>

    <x-dialog-modal wire:model="uploadModal_isOpen">
        <x-slot name="title">
            {{ __('New Voucher Upload') }}
        </x-slot>

        <x-slot name="content">
            <x-form-section submit="">
                <x-slot name="title">
                    {{ __('Upload New Voucher(s)') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Upload new Voucher(s) in bulk') }}
                </x-slot>

                <x-slot name="form">
                    <div class="col-span-6">
                        <x-validation-errors class="mb-4" />
                    </div>
                    

                    <div class="col-span-6">
                        <x-label for="package" value="{{ __('Package') }}" />
                        <select id="package" type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            wire:model.defer="package">
                            <option value="">Select Package</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->slug }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="package" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <x-label for="voucherFile" value="{{ __('File') }}" />
                        <x-input id="voucherFile" type="file" accept="text/csv,application/xlsx,application/xls" class="mt-1 block w-full" wire:model.defer="voucherFile" />
                        <x-input-error for="voucherFile" class="mt-2" />
                    </div>

                </x-slot>

            </x-form-section>
        </x-slot>

        <x-slot name="footer">
            @if ($voucherFile)
                <span class="text-sm text-gray-600 mr-3 dark:text-gray-400">File ready to upload</span>
            @endif

            <x-action-message on="uploaded-vouchers" class="mr-3">
                {{ __('Saved.') }}
            </x-action-message>

            <x-secondary-button class="mr-3" wire:click="closeUploadModal" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="mx-3" wire:click="uploadVouchers" wire:loading.attr="disabled">Upload</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
