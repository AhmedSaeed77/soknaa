<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order)
        {
            if (is_null($order->order_num))
            {
                $order->order_num = static::generateOrderNumber();
            }
        });
    }

    protected static function generateOrderNumber()
    {
        $lastOrderNum = static::max('order_num') ?? 999;
        return $lastOrderNum + 1;
    }

    public function getStatusAttribute()
    {
        if($this->attributes['status'] == 0)
            return "جارى العمل";
        elseif($this->attributes['status'] == 1)
            return 'مكتمل';
        elseif($this->attributes['status'] == 2)
            return 'مرفوض';
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class,'from');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class,'to');
    }
}
