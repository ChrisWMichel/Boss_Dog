<?php

namespace App\Enums;

enum OrderStatus: string
{
    
    case Paid = 'paid';
    case Unpaid = 'unpaid';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function getStatuses()
    {
        return [
           
            self::Paid,
            self::Unpaid,
            self::Shipped,
            self::Completed,
            self::Cancelled,
            
        ];
    }
}

