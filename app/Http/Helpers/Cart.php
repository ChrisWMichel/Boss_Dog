<?php

namespace App\Http\Helpers;

use App\Models\CartItem;
use App\Models\Product;

class Cart
{
    public static function getCartItemsCount()
    {
       $request = \request();
       $user = $request->user();
       if($user){
           return CartItem::where('user_id', $user->id)->sum('quantity');
       } else {
           $cart = self::getCookieCartItems();
           return array_reduce($cart, function($carry, $item){
               return $carry + $item['quantity'];
           }, 0);
       }
    }

    public static function getCartItems()
    {
        $request = \request();
        $user = $request->user();
        if($user){
            return CartItem::where('user_id', $user->id)->get()->map(
                fn($item) => [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity
                ]
            );
        } else {
            return self::getCookieCartItems();
            
        }
    }

    public static function getCookieCartItems()
    {
        $request = \request();

        $cart = json_decode($request->cookie('cart_items'), true);
        return $cart ? $cart : [];
    }

    public static function getCountFromItems($cartItems): int
    {
        return array_reduce(
            $cartItems,
            fn($carry, $item) => $carry + $item['quantity'],
            0
        );
    }

    public static function moveCartItemsIntoDb()
    {
        $request = \request();
        $cartItems = self::getCookieCartItems();
        $dbCartItems = CartItem::where(['user_id' => $request->user()->id])->get()->keyBy('product_id');
        $newCartItems = [];
        foreach ($cartItems as $cartItem) {
            if (isset($dbCartItems[$cartItem['product_id']])) {
                continue;
            }
            // Check if the product exists in the products table
            // if (!Product::find($cartItem['product_id'])) {
            //     continue;
            // }
            $newCartItems[] = [
                'user_id' => $request->user()->id,
                'product_id' => $cartItem['product_id'],
                'quantity' => $cartItem['quantity'],
            ];
        }

        if (!empty($newCartItems)) {
            CartItem::insert($newCartItems);
        }
        //$request->cookie('cart_items', json_encode([]), 60 * 24 * 30);
    }
}