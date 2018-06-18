<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    //
    protected $table = 'chat_participants';


    public function thread(){
        return $this->belongsTo('App\Models\Thread','thread_id','id');
    }



    
}
