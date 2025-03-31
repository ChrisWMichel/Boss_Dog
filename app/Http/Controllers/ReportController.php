<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Customer;
use App\Traits\ReportTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    use ReportTrait;
    
    public function orders(Request $request)
    {
        $dateRange = $this->getDateRange();
        $query = Order::query();

        if ($dateRange['start']) {
            $query->where('created_at', '>=', $dateRange['start'])
                ->where('created_at', '<=', $dateRange['end']);
        }
        
        $orders = $query->get();
        
        // Ensure each order has properly formatted created_at and total
        $formattedOrders = $orders->map(function ($order) {
            // Check if created_at is already a string or a Carbon instance
            $createdAt = $order->created_at;
            if (is_object($createdAt) && method_exists($createdAt, 'format')) {
                $formattedDate = $createdAt->format('Y-m-d');
            } else {
                // If it's already a string, use it as is or parse it if needed
                $formattedDate = $createdAt;
            }
            
            return [
                'id' => $order->id,
                'total' => (float)$order->total,
                'created_at' => $formattedDate,
            ];
        });
        
        return $formattedOrders;
    }

    public function customers(){
        $query = Customer::query();

        return $this->prepareDataForLineChart($query, 'Customers By Day');
    }
        
    private function prepareDataForLineChart($query, $label)
    {
        $dateRange = $this->getDateRange();
    $fromDate = isset($dateRange['start']) ? Carbon::parse($dateRange['start']) : Carbon::now()->subDays(30);
        $query
            ->select([DB::raw('CAST(created_at as DATE) AS day'), DB::raw('COUNT(created_at) AS count')])
            ->groupBy(DB::raw('CAST(created_at as DATE)'));
        if ($fromDate) {
            $query->where('created_at', '>', $fromDate);
        }
        $records = $query->get()->keyBy('day');

        // Process for chartjs
        $days = [];
        $labels = [];
        $now = Carbon::now();
        while ($fromDate < $now) {
            $key = $fromDate->format('Y-m-d');
            $labels[] = $key;
            $fromDate = $fromDate->addDay(1);
            $days[] = isset($records[$key]) ? $records[$key]['count'] : 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => $label,
                'backgroundColor' => '#f87979',
                'data' => $days
            ]]
        ];
    }
}

