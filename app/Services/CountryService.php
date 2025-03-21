<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CountryService
{
    public function getCountries()
    {
        return Cache::remember('countries', 86400, function () {
            $response = Http::get('https://restcountries.com/v3.1/all?fields=name,cca2');
            return collect($response->json())
                ->sortBy('name.common')
                ->map(function ($country) {
                    return [
                        'code' => $country['cca2'],
                        'name' => $country['name']['common']
                    ];
                });
        });
    }

    public function getStates($countryCode)
    {
        return Cache::remember("states_{$countryCode}", 86400, function () use ($countryCode) {
            // For US states, you can use another API or hardcode them
            if ($countryCode === 'US') {
                $response = Http::get('https://gist.githubusercontent.com/mshafrir/2646763/raw/states_hash.json');
                return $response->json();
            }
            
            return [];
        });
    }
}
