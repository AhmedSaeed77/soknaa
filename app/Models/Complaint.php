<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($complaint)
        {
            if (is_null($complaint->complaint_num))
            {
                $complaint->complaint_num = static::generateMembershipNumber();
            }
        });
    }

    protected static function generateMembershipNumber()
    {
        $lastMembershipNumber = static::max('complaint_num') ?? 999;
        return $lastMembershipNumber + 1;
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
