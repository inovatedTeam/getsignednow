<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //


    protected $table = 'chat_threads_messages';


    public function user(){

        return $this->hasOne('App\Models\User','id','user_id');

    }
}
