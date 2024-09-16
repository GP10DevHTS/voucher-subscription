<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <x-button wire:click="openNewVoucherForm">New Voucher</x-button>

    <x-dialog-modal wire:model="newVoucherForm_isOpen">
        <x-slot name="title">
            {{ __('New Voucher') }}
        </x-slot>

        <x-slot name="content">
            <x-form-section submit="">
                <x-slot name="title">
                    {{ __('Create New Voucher') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Create a new Voucher.') }}
                </x-slot>

                <x-slot name="form">
                    <div class="col-span-6">
                        <x-validation-errors class="mb-4" />
                    </div>
                    <div class="col-span-6">
                        <x-label for="code" value="{{ __('Name / Code') }}" />
                        <x-input id="code" type="text" class="mt-1 block w-full" wire:model.defer="code" />
                        <x-input-error for="code" class="mt-2" />
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

                </x-slot>

            </x-form-section>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeNewVoucherForm" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="mx-3" wire:click="createVoucher" wire:loading.attr="disabled">Save</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
