<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\Order;
use App\Models\Payment;
use App\Enums\OrderStatus;
use App\Http\Helpers\Cart;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;

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
        if(!$payment || $payment->status !== PaymentStatus::Pending){
            return redirect()->route('checkout.failure');
        }
        $payment->status = PaymentStatus::Paid;
        $payment->update();

        $order = $payment->order;
        $order->status = OrderStatus::Paid;
        $order->update();
        }catch(\Exception $e){
            return redirect()->route('checkout.failure');
        }
        
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

        $stripeSecretKey = config('app.STRIPE_SECRET_KEY');
        if (!$stripeSecretKey) {
            throw new \Exception('Stripe secret key is not set.');
        }
        $stripe = new \Stripe\StripeClient(config('app.STRIPE_SECRET_KEY'));
        

        list($products, $cartItems) = Cart::getProductsAndCartItems();
        //dd($products);
        $lineItems = [];
        $totalPrice = 0;
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

          return redirect($checkout_session->url);
    }
    
    
}
