<?php

namespace App\Http\Controllers;


use App\Models\CartItem;
use App\Http\Helpers\Cart;
use App\Models\Api\Product;
//use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    public function index(){
        list($products, $cartItems) = Cart::getProductsAndCartItems();
        $total = 0;
        foreach($products as $product){
            $total += $product->price * $product->quantity;
        }
        return view('Cart.index', compact('products', 'cartItems', 'total'));
    }

    public function add(Request $request, Product $product){
        $quantity = $request->post('quantity', 1);
        $user = $request->user();
        $cart = session()->get('cart');
        if($user){
            $cartItem = CartItem::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();
            if($cartItem){
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity
                ]);
            }
            return response(['count' => Cart::getCartItemsCount()]);
        } else {
            $cartItems = json_decode($request->cookie('cart_items'), true) ?? [];
            $productFound = false;
            foreach($cartItems as &$item){
                if($item['product_id'] == $product->id){
                    $item['quantity'] += $quantity;
                    $productFound = true;
                    break;
                }
            }
            if(!$productFound){
                $cartItems[] = [
                    'user_id' => null,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price
                ];
            }
            return response(['count' => Cart::getCountFromItems($cartItems)])
                ->cookie('cart_items', json_encode($cartItems), 60*24*30);
        }
    }

    public function remove(Request $request, Product $product){
        $user = $request->user();
        if ($user) {
            $cartItem = CartItem::query()->where(['user_id' => $user->id, 'product_id' => $product->id])->first();
            if ($cartItem) {
                $cartItem->delete();
            }

            $cartItems = Cart::getCartItems();
            $total = $this->calculateTotal($cartItems);
            return response([
                'count' => Cart::getCartItemsCount(),
                'cartItems' => $cartItems,
                'total' => $total,
            ]);
        } else {
            $cartItems = json_decode($request->cookie('cart_items', '[]'), true);
            foreach ($cartItems as $i => &$item) {
                if ($item['product_id'] === $product->id) {
                    array_splice($cartItems, $i, 1);
                    break;
                }
            }
            Cookie::queue('cart_items', json_encode($cartItems), 60 * 24 * 30);

            $total = $this->calculateTotal($cartItems);
            return response([
                'count' => Cart::getCountFromItems($cartItems),
                'cartItems' => $cartItems,
                'total' => $total,
            ]);
        }
    }

    private function calculateTotal($cartItems) {
        //Log::info('Calculating total for cart items: ' . json_encode($cartItems, JSON_PRETTY_PRINT));
        $total = 0;
        foreach($cartItems as $item) {
            if (!isset($item['price'])) {
                throw new \Exception('Price is not set for product ID: ' . $item['product_id']);
            }
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function updateQuantity(Request $request, Product $product){
        $quantity = $request->post('quantity');
        $user = $request->user();
        try {
            if($user){
                $cartItem = CartItem::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->first();
                if($cartItem){
                    $cartItem->quantity = $quantity;
                    $cartItem->save();
                }
                $cartItems = Cart::getCartItems();
                //Log::info('Calculating total for cart items: ' . json_encode($cartItems, JSON_PRETTY_PRINT));
                 
                $total = $this->calculateTotal($cartItems);
                return response()->json(['count' => Cart::getCartItemsCount(), 'cartItems' => $cartItems, 'total' => $total]);
            } else {
                $cartItems = json_decode($request->cookie('cart_items'), true) ?? [];
                foreach($cartItems as &$item){
                    if($item['product_id'] == $product->id){
                        $item['quantity'] = $quantity;
                        $item['price'] = $product->price;
                        break;
                    }
                }
                $total = $this->calculateTotal($cartItems);
                return response()->json(['count' => Cart::getCountFromItems($cartItems), 'cartItems' => $cartItems, 'total' => $total])
                    ->cookie('cart_items', json_encode($cartItems), 60*24*30);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
