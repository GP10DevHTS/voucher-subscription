<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <x-button wire:click="openCreatePackageModal">New Package</x-button>

    <x-dialog-modal wire:model="newpackage_open">
        <x-slot name="title">
            {{ __('New Package') }}
        </x-slot>

        <x-slot name="content">
            <x-form-section submit="">
                <x-slot name="title">
                    {{ __('Create New Package') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Create a new package.') }}
                </x-slot>

                <x-slot name="form">
                    <div class="col-span-6">
                        <x-label for="name" value="{{ __('Name') }}" />
                        <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    <div class="col-span-3">
                        <x-label for="currency" value="{{ __('Currency') }}" />
                        <select id="currency" type="text" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model.defer="currency">
                            <option value="USD">USD</option>
                            <option value="UGX">UGX</option>
                        </select>
                        <x-input-error for="currency" class="mt-2" />
                    </div>

                    <div class="col-span-3">
                        <x-label for="price" value="{{ __('Price') }}" />
                        <x-input id="price" type="number" class="mt-1 block w-full" wire:model.defer="price" />
                        <x-input-error for="price" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <x-label for="description" value="{{ __('Description') }}" />
                        <textarea id="description" type="text" class="mt-1 block w-full resize-none rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model.defer="description"></textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>
                </x-slot>
            </x-form-section>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeCreatePackageModal" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="mx-3" wire:click="createPackage" wire:loading.attr="disabled">Save</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
