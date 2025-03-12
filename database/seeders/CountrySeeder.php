<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PHPUnit\Framework\Constraint\Count;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usaStates = [
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'FL' => 'Florida',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
        ];

        $countries = [
            [
                'name' => 'United States',
                'code' => 'US',
                'states' => json_encode($usaStates),
            ],
            [
                'name' => 'Canada',
                'code' => 'CA',
                'states' => null,
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'UK',
                'states' => null,
            ],
            [
                'name' => 'Australia',
                'code' => 'AU',
                'states' => null,
            ],
           
           
        ];

        Country::insert($countries);
    }
}
