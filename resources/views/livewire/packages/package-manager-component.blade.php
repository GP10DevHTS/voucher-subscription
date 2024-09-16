<div class="p-6">
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="grid grid-cols-1 md:grid-cols-2">
        <div>
            <x-section-title>
                <x-slot name="title">{{ __('Package Manager') }}</x-slot>
                <x-slot name="description">{{ __('Manage your packages.') }}</x-slot>
            </x-section-title>
        </div>
        <div class="flex justify-end">
            @livewire('packages.new-package-form')
        </div>
    </div>

    <div>
        @livewire('packages.package-table')
    </div>
</div>
