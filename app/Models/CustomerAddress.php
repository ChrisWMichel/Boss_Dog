<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerAddress extends Model
{
    protected $fillable = [
        'type',
        'address1',
        'address2',
        'city',
        'state',
        'country_code',
        'zip_code',
        'customer_id'
    ];

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'user_id', 'customer_id');
    }

    
}
