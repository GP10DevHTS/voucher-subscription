<div>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div>
            Welcome to {{ config('app.name') }} !!!
        </div>

        <div class="mt-4">
            @if (session()->has('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 text-red-700 p-4 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <!-- Check if redirect_url is set -->
        @if (isset($red_url))
            <!-- Show the iframe for payment -->
            <div class="mt-4">
                <iframe src="{{ $red_url }}" frameborder="0" width="100%" height="600px"></iframe>
            </div>
        @else
            <!-- Show the package selection and form if redirect_url is not set -->
            @if ($pagesect == 1)
                <div>
                    Please select a package
                </div>
                <div class="grid grid-cols-3 ms:grid-cols-1 gap-4">
                    @foreach ($packages as $package)
                        <div class="p-4 bg-white rounded-lg shadow-md cursor-pointer"
                            wire:click="selectPackage('{{ $package->slug }}')">
                            {{ $package->name }} <br>
                            {{ $package->description }} <br>
                            {{ $package->currency }} {{ number_format($package->price, 0) }}
                        </div>
                    @endforeach
                </div>
            @elseif ($pagesect == 2)
                <x-form-section submit="">
                    <x-slot name="title">
                        {{ __('Enter Your Details') }}
                    </x-slot>

                    <x-slot name="description">
                        {{ __('Please enter your details to proceed with payment') }}
                    </x-slot>

                    <x-slot name="form">
                        <div class="col-span-6">
                            <x-label for="name" value="{{ __('Name') }}" />
                            <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                            <x-input-error for="name" />
                        </div>
                        <div class="col-span-6">
                            <x-label for="email" value="{{ __('Email') }}" />
                            <x-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="email" />
                            <x-input-error for="email" />
                        </div>
                        <div class="col-span-6">
                            <x-label for="phone" value="{{ __('Phone') }}" />
                            <x-input id="phone" type="text" class="mt-1 block w-full" wire:model.defer="phone" />
                            <x-input-error for="phone" />
                        </div>
                    </x-slot>

                    <x-slot name="actions">
                        <x-button type="button" wire:click="createClient" wire:loading.attr="disabled">
                            {{ __('Proceed to Payment') }}
                        </x-button>
                    </x-slot>
                </x-form-section>
            @endif
        @endif
    </x-authentication-card>
</div>
