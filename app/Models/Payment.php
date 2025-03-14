<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['amount', 'status', 'order_id', 'session_id', 'type', 'created_by', 'updated_by'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
