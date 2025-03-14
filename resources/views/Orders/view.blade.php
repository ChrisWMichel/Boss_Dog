<x-app-layout>
    <div class="container mx-auto mt-20 lg:w-2/3 xl:w-2/3">
        <h1 class="mb-6 text-3xl font-bold">Order #{{ $order->id }} Details</h1>

        <div class="p-3 bg-white rounded-md shadow-md">
            <div>
                <table class="table-sm">
                    <tbody>
                        <tr>
                            <td class="font-bold">Order #</td>
                            <td>{{ $order->id }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Order Date</td>
                            <td>{{ $order->created_at }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Status</td>
                            <td>
                                <span class="p-1 text-white rounded bg-emerald-500">{{ $order->status }}</span>
                            </td>
                        </tr>
                        @php
                            $formattedPrice = number_format($order->total, 2, '.', ',');
                        @endphp
                        <tr>
                            <td class="font-bold">SubTotal</td>
                            <td>${{ $formattedPrice }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr class="my-5" />

            <!-- Order Items -->
            <div>
                <!-- Product Item -->
                @foreach ($order->items()->with('product')->get() as $item)
                    @php
                        $unitPrice = number_format($item->unit_price, 2, '.', ',');
                    @endphp
                    <div class="flex flex-col items-center w-full gap-4 sm:flex-row">
                        <a href="{{ route('product.view', $item->product) }}"
                            class="flex items-center justify-center h-32 overflow-hidden w-36">
                            <img src="{{ asset($item->product->image) }}" class="object-cover" alt="" />
                        </a>
                        <div class="flex flex-col justify-between w-full">
                            <div class="mb-3 ">
                                <h3 class="text-lg font-semibold">
                                    {{ $item->product->title }}
                                </h3>
                            </div>

                            <div class="flex justify-between">
                                <div>Qty: {{ $item->quantity }}</div>

                                <div class="text-lg font-semibold"> ${{ $unitPrice }} </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Order Item -->
                    <hr class="my-3" />
                @endforeach

            </div>
            <!--/ Order Items -->
            @if ($order->status === 'unpaid')
                <form action="{{ route('cart.checkout-order', $order) }}" method="post">
                    <div class="pt-4 border-t border-gray-300">
                        <button type="submit" class="flex items-center justify-center w-full py-3 text-lg btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Proceed to Pay
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</x-app-layout>
