<?php

namespace App\Models;

use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, Billable,UserChat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'last_name', 'company_name', 'mobile_number', 'password', 'email', 'avatar', 'status','role','token','confirmation_code','countries_id','state', 'network_type_id', 'registration_ip_address', 'last_login_ip_address'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	public function campaigns() {
        return $this->hasMany('App\Models\Campaigns');
    }
	
	public function country() {
        return $this->belongsTo('App\Models\Countries', 'countries_id')->first();
    }
    
    public function network() {
        return $this->belongsTo('App\Models\Networks', 'network_type_id')->first();
    }
	
}