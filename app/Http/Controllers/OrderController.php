<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $orders = Order::withCount('items')->where('created_by', $user->id)->orderBy('created_at', 'desc')->paginate(10);

        //Log::info('Orders:', ['orders' => $orders]);

        return view('orders.index', compact('orders'));
    }
   


    /**
     * Display the specified resource.
     */
    public function view(Order $order)
    {
        /** @var \App\Models\User $user */
        $user = \request()->user();
        if($order->created_by !== $user->id){
            abort(403);
        }
        
        return view('orders.view', compact('order'));
    }

    
}
