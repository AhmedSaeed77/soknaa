<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilySituation extends Model
{
    use HasFactory;

    protected $table = 'family_situations';
    protected $guarded = [];
}
