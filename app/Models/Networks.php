<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Networks extends Model {

	protected $guarded = array();
	public $timestamps = false;
	
	public function users() {
        return $this->hasMany('App\Models\User');
    }	

}