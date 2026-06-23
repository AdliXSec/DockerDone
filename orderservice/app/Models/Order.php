<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Order extends Model
{
    /**
     * Field yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_email',
        'product_id',
        'quantity',
        'total_price',
        'status',
    ];

    /**
     * Cast attributes to native types.
     */
    protected $casts = [
        'total_price' => 'double',
        'quantity' => 'integer',
        'user_id' => 'integer',
        'product_id' => 'integer',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
