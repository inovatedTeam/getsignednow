<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use Pusher;
class ChatController extends Controller
{
    //


    public function index($id = null){


    	$user = \Auth::user();
        $thread = new \App\Models\Thread();
        if($id == null){  
            $pa =  $user->participants->sortBy('created_at');
            if($pa->first()){
            	$thread = $pa->first()->thread;
            }
        }
        else{
            $thread = \App\Models\Thread::find($id);
        }
        return view('chat.index',compact(
            'user',
            'thread'
        ));
    }

    public function api(Request $request){

        if($request->input('type') == 'add'){
            $user = \Auth::user();
            $thread_id = $request->input('thread');
            $content = $request->input('content');

            $mess = new \App\Models\Message();

            $mess->user_id = $user->id;
            $mess->thread_id = $thread_id;
            $mess->body = $content;
            $mess->type = "message";
            $mess->save();

            $thread =  Models\Thread::find($thread_id);
            $to_user = $thread->Sender($user->id);

            $options = array(
                'cluster' => 'us2',
                'encrypted' => true
              );
              $pusher = new Pusher(
                  env('PUSHER_KEY', 'a67405a003f557ce006c'),
                  env('PUSHER_SECRET', '7616e99448fb67c2f7c1'),
                  env('PUSHER_APP_ID', '528961'),
                $options
              );

            
            $pusher->trigger('user-'.$to_user->id, 'MessageSent',$mess);

            //$myEvent = new \App\Events\MessageSent($mess,$to_user->id);
           // $result = broadcast($myEvent)->toOthers();

           // echo (string)view('chatbox',['t'=>$mess,'user_id'=>$user->id]);
        //$to_user = \App\User::find($test);
        }

        if($request->input('type') == 'create_mess_box'){

            $user = \Auth::user();
            $id = $request->input('mess');
            $mess = Models\Message::find($id);
            echo (string)view('chat.chatbox',['t'=>$mess,'user_id'=>$user->id]);
        }

    }

    public function getMess(Request $request){

        $user = \Auth::user();

        // last record
        if($request->input('type') == 'last-record'){

            $thread_id = $request->input('thread_id');
            $numberOfRecords = $request->input('number');

         
            $mycount = \App\Models\Message::where('thread_id',$thread_id)->count();

            if($mycount >= $numberOfRecords){
                
                $mess = \App\Models\Message::where('thread_id',$thread_id)->skip($mycount - $numberOfRecords)->take($numberOfRecords)->get();

            }else{

                $mess = \App\Models\Message::where('thread_id',$thread_id)->get();
            }
            
            $view = $request->input('view');

            $result = "";

            foreach($mess as $t){

                $result .= (string)view($view,['t'=>$t,'user_id'=>$user->id]);


            }
            

            return [
                'raw'=>$result,
                'count'=>0,
                'full'=>0
            ]; 
            
        }
        // end last record

        if($request->input('type') == 'create_mess_box_small'){

            $user = \Auth::user();
            $id = $request->input('mess');
            $mess = \App\Models\Message::find($id);
            

            return [
                $mess->thread_id,
                 (string)view('chat.chatbox-small',['t'=>$mess,'user_id'=>$user->id])
            ];
        }
    }
}
