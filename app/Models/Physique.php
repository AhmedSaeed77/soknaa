<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Physique extends Model
{
    use HasFactory;

    protected $table = 'physiques';
    protected $guarded = [];
}
