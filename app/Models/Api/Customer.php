<?php

namespace App\Models\Api;

use Carbon\Carbon;
use App\Models\Customer as BaseCustomer;

class Customer extends BaseCustomer
{
    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }
    public function getFormattedUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->diffForHumans();
    }
}