<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivatChat extends Model
{
    use HasFactory;
    protected $table = 'privat_chats';
    protected $guarded = [];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user');
    }

    public function fromAdmin()
    {
        return $this->belongsTo(Admin::class, 'from_admin');
    }
}
