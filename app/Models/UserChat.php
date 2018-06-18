<?php

namespace App\Models;


trait UserChat{



	public function getAvatar(){


		return '/public/avatar/'.$this->avatar;
	}

	public function test(){

		echo "hello";
	}

	public function participants(){

	    return $this->hasMany('App\Models\Participant','user_id','id');

    }

    public function recipients()
    {
        return $this->participants()->where('user_id', '!=', $this->user_id);
    }


    public function Unread(){


        $threads = Participant::where('user_id',$this->id)->pluck('thread_id')->toArray();

        return \DB::table('chat_threads_messages')
                        ->where('user_id','!=',$this->id)
                        ->where('read_mess',0)
                        ->whereIn('thread_id',$threads)->count();    
    }
}