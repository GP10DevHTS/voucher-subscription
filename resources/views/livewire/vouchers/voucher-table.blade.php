<div wire:poll class="container mx-auto p-4">
    <!-- Search and Filter Section -->
    <div class="flex justify-between items-center mb-4 gap-4">
        <!-- Search Input -->
        <input type="text" wire:model="search" placeholder="Search Vouchers"
            class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-1/3 dark:bg-gray-800 dark:text-white" />

            {{-- pagination filter --}}
        <select wire:model.live.debounce="perPage" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-1/3 dark:bg-gray-800 dark:text-white">
            <option value="25">25 Per Page</option>
            <option value="50">50 Per Page</option>
            <option value="100">100 Per Page</option>
            <option value="200">200 Per Page</option>
            <option value="500">500 Per Page</option>
        </select>

        <!-- Filter by Package -->
        <select wire:model="filterPackage"
            class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-1/3 dark:bg-gray-800 dark:text-white">
            <option value="">All Packages</option>
            @foreach ($packages as $package)
                <option value="{{ $package->id }}">{{ $package->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Vouchers Cards -->
    <div class="grid grid-cols-3 md:grid-cols-3 lg:grid-cols-6 gap-6">
        @foreach ($vouchers as $voucher)
            <div
                class="bg-white text-center dark:bg-gray-800 p-4 rounded-lg shadow-md border border-gray-300 dark:border-gray-700">
                <h3 class="text-lg font-bold dark:text-white">{{ $voucher->name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Package: <br>
                    {{ $voucher->package->name ?? 'N/A' }} <br>
                    {{ $voucher->package->currency ?? 'XXX' }} {{ number_format($voucher->package->price, 0) ?? 'N/A' }}
                </p>
            </div>
        @endforeach
    </div>
</div>
