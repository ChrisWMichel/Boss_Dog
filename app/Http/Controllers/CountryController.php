<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CountryService;

class CountryController extends Controller
{
    protected $countryService;
    
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }
    
    public function index()
    {
        $countries = $this->countryService->getCountries();
        return response()->json($countries);
    }

    public function states($countryCode)
    {
        $states = $this->countryService->getStates($countryCode);
        return response()->json($states);
    }

   
}
