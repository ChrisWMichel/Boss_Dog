<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['status', 'total', 'created_by', 'updated_by'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
