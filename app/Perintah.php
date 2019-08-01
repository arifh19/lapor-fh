<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Perintah extends Model
{
    public function user(){
      return $this->belongsTo('App\User','last_updated_by');
    }
    public function pengirim(){
		  return $this->belongsTo('App\User','user_id');
    }
    public function unit(){
		  return $this->belongsTo('App\Unit','unit_id');
    }

    public function getReadableCreatedAt(){
		setlocale(LC_TIME, 'id_ID.UTF-8');
        return Carbon::parse($this->attributes['created_at'])->formatLocalized('%H:%S %A, %d %B %Y');
	}   

	public function getReadableUpdatedAt(){
		setlocale(LC_TIME, 'id_ID.UTF-8');
        return Carbon::parse($this->attributes['updated_at'])->formatLocalized('%H:%S %A, %d %B %Y');
	}  
}
