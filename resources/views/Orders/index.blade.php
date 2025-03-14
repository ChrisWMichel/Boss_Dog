<x-app-layout>
    <div class="container mx-auto mt-20 lg:w-2/3 xl:w-2/3">
        <h1 class="mb-6 text-3xl font-bold">My Orders</h1>

        <div class="p-3 bg-white rounded-md shadow-md">
            <table class="table w-full table-auto">
                <thead class="border-b-2">
                    <tr class="text-left">
                        <th>Order</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>SubTotal</th>
                        <th>Items</th>
                        <th class="w-64">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-b">
                            <td>
                                <a href="{{ route('orders.view', $order) }}"
                                    class="text-purple-600 hover:text-purple-500">
                                    #{{ $order->id }}
                                </a>
                            </td>
                            <td>{{ $order->created_at }}</td>
                            <td>
                                <span
                                    class="p-1 text-white rounded {{ $order->status === 'paid' ? 'bg-emerald-500' : 'bg-gray-400' }}">

                                    {{ $order->status }}
                                </span>
                            </td>
                            @php
                                $formattedPrice = number_format($order->total, 2, '.', ',');
                            @endphp
                            <td>${{ $formattedPrice }}</td>
                            <td class="whitespace-nowrap">
                                {{ $order->items->count() }}
                                {{ $order->items->count() === 1 ? 'item' : 'items' }}
                            </td>
                            <td class="flex gap-3">
                                {{-- @if ($order->status === 'paid')
                                    <div x-data="{ open: false }">
                                        <button @click="open = true"
                                            class="flex items-center px-2 py-1 btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Invoice
                                        </button>
                                        <template x-teleport="body">
                                            <!-- Backdrop -->
                                            <div x-show="open"
                                                class="fixed top-0 bottom-0 left-0 right-0 z-10 flex items-center justify-center bg-black/80">
                                                <!-- Dialog -->
                                                <div x-show="open" x-transition @click.outside="open = false"
                                                    class="w-[90%] md:w-1/2 bg-white rounded-lg">
                                                    <!-- Modal Title -->

                                                    <div
                                                        class="flex items-center justify-between px-4 py-2 text-lg font-semibold bg-gray-100 rounded-t-lg">
                                                        <h2>Modal Title</h2>
                                                        <button @click="open = false">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <!-- Modal Body -->
                                                    <div class="p-4">
                                                        Invoice Content
                                                    </div>
                                                    <!-- Modal Footer -->
                                                    <div
                                                        class="flex justify-end px-4 py-2 text-lg font-semibold bg-gray-100 rounded-b-lg">
                                                        <button @click="open = false"
                                                            class="inline-flex items-center px-3 py-1 text-gray-800 bg-gray-300 rounded-md shadow hover:bg-opacity-95">
                                                            Close
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                        </template>
                                @endif --}}
        </div>
        @if ($order->status != 'paid')
            <form action="{{ route('cart.checkout-order', $order) }}" method="post">
                @csrf
                <button class="flex items-center px-2 py-1 btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Pay
                </button>
            </form>
        @endif
        </td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
    </div>
</x-app-layout>
