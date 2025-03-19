@php
    use Carbon\Carbon;
@endphp
<img src="{{ asset('images/projectLogo-large.png') }}" alt="Logo"
    style="width: 125px; margin: 0 auto; display: block;" />
<h1>
    New order has been placed
</h1>

<table>
    <tr>
        <th>Order ID</th>
        <td>
            @if ($forAdmin)
                <a href="{{ config('app.url') }}/app/orders/{{ $order->id }}">
                    View Order #{{ $order->id }} (Admin)
                </a>
            @else
                <a href="{{ config('app.url') }}/orders/view/{{ $order->id }}">
                    View Order #{{ $order->id }}
                </a>
            @endif
        </td>
    </tr>
    <tr>
        <th>Customer</th>
        <td>{{ $order->user->firstname }} {{ $order->user->lastname }}</td>
    </tr>
    <tr>
        <th>Order Date</th>
        <td>{{ $order->created_at }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>{{ $order->status }}</td>
    </tr>
    <tr>
        <th>SubTotal</th>
        <td>${{ $order->total }}</td>
    </tr>
</table>
<table style="width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 20px;">
    <tr style="background-color: #f3f4f6;">
        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Image</th>
        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Product</th>
        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">Quantity</th>
        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Total Price</th>
        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;">Date</th>
    </tr>
    @foreach ($order->items as $item)
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; vertical-align: middle;">
                <img src="{{ asset($item->product->image) }}" alt="Product Image" width="64" height="64"
                    style="width: 64px; height: 64px; object-fit: cover;" />
            </td>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; vertical-align: middle;">
                {{ $item->product->title }}</td>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: center; vertical-align: middle;">
                {{ $item->quantity }}</td>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: right; vertical-align: middle;">
                ${{ $item->unit_price * $item->quantity }}</td>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: right; vertical-align: middle;">
                {{ Carbon::parse($item->created_at)->diffForHumans() }}</td>
        </tr>
    @endforeach
</table>
