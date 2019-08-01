<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RiwayatPerintah extends Model
{
    public function perintah(){
		return $this->belongsTo('App\Perintah','perintah_id');
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
