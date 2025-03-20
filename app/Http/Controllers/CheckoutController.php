<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use App\Http\Helpers\Cart;
use App\Mail\NewOrderEmail;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function success(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        
        $stripe = new \Stripe\StripeClient(config('app.STRIPE_SECRET_KEY'));
       
        try{
            $sessionId = $request->query('session_id');
            
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            $payment = Payment::where('session_id', $sessionId)->first();
            
        if(!$payment || $payment->status !== PaymentStatus::Pending->value){
            return redirect()->route('checkout.failure');
        }
        $payment->status = PaymentStatus::Paid;
        $payment->update();

        $order = $payment->order;
        $order->status = OrderStatus::Paid;
        $order->update();
        }catch(\Exception $e){
            Log::error('FAILURE-FAILURE-FAILURE', ['message' => json_encode($e->getMessage())]);
            return view('checkout.failure', ['message' => $e->getMessage()]);
        }

        $adminUsers = User::where('is_admin', true)->get();
        // For admin users
        foreach($adminUsers as $admin){
            Mail::to($admin->email)->send(new NewOrderEmail($order, true));
        }
        // For customer
    Mail::to($user->email)->send(new NewOrderEmail($order, false));
        
        $customer = $user->firstname;
        
        return view('checkout.success', compact('customer'));
    }

    public function failure(Request $request)
    {
        return view('checkout.failure');
    }

    public function checkout(Request $request){
    /** @var \App\Models\User $user */
        $user = $request->user();

        // Check if user has both shipping and billing addresses
    // if (!$user->customer || !$user->customer->shippingAddress || !$user->customer->billingAddress) {
    //     return redirect()->route('profile.view')
    //         ->with('error', 'Please complete your shipping and billing information before checkout');
    // }

        $stripeSecretKey = config('app.STRIPE_SECRET_KEY');
        if (!$stripeSecretKey) {
            throw new \Exception('Stripe secret key is not set.');
        }
        $stripe = new \Stripe\StripeClient(config('app.STRIPE_SECRET_KEY'));
        

        list($products, $cartItems) = Cart::getProductsAndCartItems();
        //dd($products);
        $lineItems = [];
        $totalPrice = 0;
        $orderItems = [];

        foreach($products as $product){
            $quantity = $cartItems[$product->id]['quantity'];
            $totalPrice += $product->price * $quantity;
            $unitAmount = intval($product->price * 100);

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->title,
                        'images' => [$product->image],
                    ],
                    'unit_amount' => $unitAmount,
                ],
                'quantity' => $quantity,
            ];

            $orderItems[] = [
                'product_id' => $product->id,
                'unit_price' => $product->price,
                'quantity' => $quantity,
            ];
        }
        
        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true). '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.failure', [], true),
          ]);

          $orderData = [
                'total' => $totalPrice,
                'status' => OrderStatus::Unpaid,
                'created_by' => $user->id,
                'updated_by' => $user->id,
          ];
          $order = Order::create($orderData);

          // create order items
            foreach ($orderItems as $orderItem) {
                    $orderItem['order_id'] = $order->id;
                    OrderItem::create($orderItem);
            }

          $paymentData = [
                'order_id' => $order->id,
                'amount' => $totalPrice,
                'status' => PaymentStatus::Pending,
                'order_id' => $order->id,
                'amount' => $totalPrice,
                'type' => 'stripe',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'session_id' => $checkout_session->id,
          ];
          Payment::create($paymentData);

          CartItem::where(['user_id' => $user->id])->delete();

          return redirect($checkout_session->url);
    }
    

    public function checkoutOrder(Order $order, Request $request)
    {

        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->title,
                        'images' => [$item->product->image]
                    ],
                    'unit_amount' => $item->unit_price * 100,
                ],
                'quantity' => $item->quantity,
            ];
        }

        \Stripe\Stripe::setApiKey(config('app.STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.failure', [], true),
        ]);

        $order->payment->session_id = $session->id;
        $order->payment->save();


        return redirect($session->url);
    }
}
