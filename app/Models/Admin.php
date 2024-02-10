<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'admins';
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function generateToken()
    {
        return \JWTAuth::fromUser($this);
    }
    
    public function getStatusAttribute()
    {
        if( $this->attributes['status'] == 1)
        {
            return __('dashboard.active');
        }
        elseif($this->attributes['status'] == 0)
        {
            return __('dashboard.unactive');
        } 
    }
}
