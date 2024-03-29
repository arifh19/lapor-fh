<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_user';
    protected $primaryKey = 'user_id';

    public function role(){
        return $this->belongsTo('App\Role','role_id');
    }
}
