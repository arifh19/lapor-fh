<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laratrust\Traits\LaratrustUserTrait;
use Carbon\Carbon;

class User extends Authenticatable 
{
    use LaratrustUserTrait;
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'unit_id', 'password', 'is_verified'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role(){
      return $this->belongsTo('App\RoleUser','id');
    }

    public function unit(){
      return $this->belongsTo('App\Unit','unit_id');
    }

    public function roleUser(){
      return $this->belongsTo('App\RoleUser','id');
    }
  
    public function getReadableCreatedAt(){
        setlocale(LC_TIME, 'id_ID.UTF-8');
  //        return Carbon::parse($this->attributes['created_at'])->formatLocalized('%H:%S %A, %d %B %Y');
        return Carbon::parse($this->attributes['created_at'])->formatLocalized('%A, %d %B %Y');
    }   
  
    public function getReadableUpdatedAt(){
        setlocale(LC_TIME, 'id_ID.UTF-8');
          return Carbon::parse($this->attributes['updated_at'])->formatLocalized('%A, %d %B %Y');
    }  
  


}
