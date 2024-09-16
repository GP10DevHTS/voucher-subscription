<div class="container mx-auto p-4" wire:poll>


    <div class="overflow-x-auto">
        <table
            class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-md">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left">Name</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left">Description</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left">Price</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left">Vouchers Available
                    </th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left">Status</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packages as $package)
                    <tr
                        class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-900 transition duration-200">
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">{{ $package->name }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">{{ $package->description }}
                        </td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">{{ $package->price }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">
                            {{ $package->vouchers->count() }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $package->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100' }}">
                                {{ !$package->trashed() ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">
                            @if (!$package->trashed())
                                <x-danger-button wire:click="deactivate('{{ $package->slug }}')"
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 dark:bg-red-700 dark:hover:bg-red-800 transition duration-200">
                                    Deactivate
                                </x-danger-button>
                            @else
                                <button wire:click="reactivate('{{ $package->slug }}')"
                                    class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 dark:bg-green-700 dark:hover:bg-green-800 transition duration-200">
                                    Reactivate
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
