<?php

namespace App\Http\Controllers;

use App\Traits\ReportTrait;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ReportTrait;
    
    public function orders()
    {
        return view('reports.orders');
    }

    public function customers()
    {
        return view('reports.customers');
    }

    
        
}
