<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Traits\ReportTrait;
use Illuminate\Http\Request;

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
            // Add other fields as needed
        ];
    });
    
    return $formattedOrders;
}

    
        
}
