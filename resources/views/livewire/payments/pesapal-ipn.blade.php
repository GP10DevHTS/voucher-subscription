<div>
    {{-- Be like water. --}}
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>
        <div class="mt-4">
            @if (session()->has('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 text-red-700 p-4 rounded-lg">
                    {!! session('error') !!}
                </div>
            @endif
            <div class="mt-4 text-center">
                @if (isset($transaction) && $transaction->status === 'completed')
                    <div>
                        Your voucher code is:
                    </div>
                    <div>
                        {{ $transaction->voucher ? $transaction->voucher->name  : 'N/A' }}
                    </div>
                @else
                    <div>
                        Payment failed or was cancelled
                    </div>
                @endif
            </div>
        </div>

    </x-authentication-card>
</div>
