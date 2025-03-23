<?php

namespace App\Http\Controllers\Api;

use DB;
use Carbon\Carbon;
use App\Models\Api\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function activeCustomers(Request $request)
    {
        $query = \App\Models\Customer::where('status', 'active');
        
        if ($request->has('period') && $request->period !== 'all') {
            $dateRange = $this->getDateRange($request->period);
            if ($dateRange['start']) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }
        
        $activeCustomers = $query->count();
        return response()->json(['active_customers' => $activeCustomers]);
    }

    public function activeProducts(Request $request)
    {
        $query = Product::query();
        
        if ($request->has('period') && $request->period !== 'all') {
            $dateRange = $this->getDateRange($request->period);
            if ($dateRange['start']) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }
        
        return $query->count();
    }

    public function paidOrders(Request $request)
    {
        $query = \App\Models\Order::where('status', 'paid');
        
        if ($request->has('period') && $request->period !== 'all') {
            $dateRange = $this->getDateRange($request->period);
            if ($dateRange['start']) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }
        
        $paidOrders = $query->count();
        return response()->json(['paid_orders' => $paidOrders]);
    }

    public function totalSales(Request $request)
    {
        $query = \App\Models\Order::query();
        
        if ($request->has('period') && $request->period !== 'all') {
            $dateRange = $this->getDateRange($request->period);
            if ($dateRange['start']) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }
        
        $totalSales = $query->sum('total');
        return response()->json(['total_sales' => $totalSales]);
    }

    public function ordersByState(Request $request)
    {
        $query = DB::table('orders')
            ->join('customers', 'orders.created_by', '=', 'customers.user_id')
            ->join('customer_addresses', 'customers.user_id', '=', 'customer_addresses.customer_id')
            ->where('customer_addresses.type', '=', 'shipping');
            
        if ($request->has('period') && $request->period !== 'all') {
            $dateRange = $this->getDateRange($request->period);
            if ($dateRange['start']) {
                $query->whereBetween('orders.created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }
        
        $ordersByState = $query->select('customer_addresses.state', DB::raw('count(orders.id) as count'))
            ->groupBy('customer_addresses.state')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json($ordersByState);
    }

    public function latestCustomers(Request $request)
    {
        $query = \App\Models\Customer::orderBy('created_at', 'desc')
            ->select('user_id', 'first_name', 'last_name', 'phone', 'created_at')
            ->with(['user' => function($query) {
                $query->select('id', 'email');
            }]);
            
        if ($request->has('period') && $request->period !== 'all') {
            $dateRange = $this->getDateRange($request->period);
            if ($dateRange['start']) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }
        
        $latestCustomers = $query->limit(5)->get();
        return response()->json($latestCustomers);
    }

    public function latestOrders(Request $request)
    {
        $query = \App\Models\Order::query()
            ->select([
                'o.id', 
                'o.total', 
                'o.created_at', 
                'o.created_by',
                'c.first_name',
                'c.last_name',
                DB::raw('count(oi.id) as number_of_items')
            ])
            ->from('orders as o')
            ->join('order_items as oi', 'o.id', '=', 'oi.order_id')
            ->join('customers as c', 'o.created_by', '=', 'c.user_id')
            ->where('o.status', 'paid');
            
        if ($request->has('period') && $request->period !== 'all') {
            $dateRange = $this->getDateRange($request->period);
            if ($dateRange['start']) {
                $query->whereBetween('o.created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }
        
        $latestOrders = $query->groupBy('o.id', 'o.total', 'o.created_at', 'o.created_by', 'c.first_name', 'c.last_name')
            ->orderBy('o.created_at', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json($latestOrders);
    }

    private function getDateRange($period)
    {
        $end = Carbon::now();
        $start = null;
        
        switch ($period) {
            case 'last-day':
                $start = Carbon::now()->subDay();
                break;
            case 'last-week':
                $start = Carbon::now()->subWeek();
                break;
            case 'last-2-week':
                $start = Carbon::now()->subWeeks(2);
                break;
            case 'last-month':
                $start = Carbon::now()->subMonth();
                break;
            case 'last-3-month':
                $start = Carbon::now()->subMonths(3);
                break;
            case 'last-6-month':
                $start = Carbon::now()->subMonths(6);
                break;
            default:
                // No filter, return all-time data
                $start = null;
                break;
        }
        
        return ['start' => $start, 'end' => $end];
    }
}

