<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    //

    protected $table = 'chat_threads';




    public function messages(){

    	return $this->hasMany('App\Models\Message','thread_id','id');
    }

    public function participant(){

        return $this->hasMany('App\Models\Participant','thread_id','id');
    }

    public function Sender($id){


        $pa = $this->participant->where('user_id','!=',$id)->first();


    	return ($pa != null ? \App\Models\User::find($pa->user_id): new \App\Models\User());
    }


    public function UnreadMessage($id){
        return \DB::table('chat_threads_messages')
                        ->where('user_id','!=',$id)
                        ->where('read_mess',0)
                        ->where('thread_id',$this->id)->count();
    }

    public function LastMessage($id){
        return \DB::table('chat_threads_messages')
            ->where('user_id','!=',$id)
            ->where('type','=','message')
            ->where('thread_id',$this->id)->first();
    }

}
