<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['status', 'total', 'created_by', 'updated_by'];

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function items ()
    {
        return $this->hasMany(OrderItem::class);
    }
}
