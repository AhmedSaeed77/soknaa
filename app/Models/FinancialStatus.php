<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialStatus extends Model
{
    use HasFactory;

    protected $table = 'financial_statuses';
    protected $guarded = [];
}
