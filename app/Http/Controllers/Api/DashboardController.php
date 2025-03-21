<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Models\Api\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function activeCustomers()
    {
        $activeCustomers = \App\Models\Customer::where('status', 'active')->count();
        return response()->json(['active_customers' => $activeCustomers]);
    }

    public function activeProducts()
    {
        // $activeProducts = Product::where('active', '1')->count();
        // return response()->json(['active_products' => $activeProducts]);
        return Product::count();
    }

    public function paidOrders()
    {
        $paidOrders = \App\Models\Order::where('status', 'paid')->count();
        return response()->json(['paid_orders' => $paidOrders]);
    }

    public function totalSales()
    {
        $totalSales = \App\Models\Order::sum('total');
        return response()->json(['total_sales' => $totalSales]);
    }

    public function ordersByState()
    {
        $ordersByState = DB::table('orders')
        ->join('customers', 'orders.created_by', '=', 'customers.user_id')
        ->join('customer_addresses', 'customers.user_id', '=', 'customer_addresses.customer_id')
        ->where('customer_addresses.type', '=', 'shipping')
        ->select('customer_addresses.state', DB::raw('count(orders.id) as count'))
        ->groupBy('customer_addresses.state')
        ->orderBy('count', 'desc')
        ->limit(5)
        ->get();
    
    return response()->json($ordersByState);
    }
}
