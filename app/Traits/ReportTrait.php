<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ReportTrait
{
    public function getDateRange()
    {
        $request = request();
        $period = $request->get('period');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $end = Carbon::now();
        $start = null;

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            return ['start' => $start, 'end' => $end];
        }
        
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

    