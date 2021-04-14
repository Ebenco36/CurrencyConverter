<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCurrency extends Model
{
    //
	protected $fillable = ['baseCurrency', 'currentRate', 'compared_to', 'threshold', 'user_id'];
	public function user(){
		return $this->belongsTo('\App\User', 'user_id', 'id');
	}
	
	public function scopeActive($query){
		
		return $query->where('baseCurrency', '!=', '')->orWhere('baseCurrency', '!=', NULL);
	}
}
