<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Aset extends Model
{
    public function user(){
		return $this->belongsTo('App\User','last_updated_by');
    }
    public function lokasi(){
		return $this->belongsTo('App\Lokasi','lokasi_id');
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
