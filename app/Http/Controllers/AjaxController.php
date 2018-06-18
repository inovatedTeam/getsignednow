<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\Campaigns;
use App\Models\Categories;
use App\Models\Donations;
use App\Models\Updates;
use App\Helper;
use App\Models\Like;
use App\Models\User;
use App\Models\Friend;
use App\Models\Notification;

use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
	
	public function __construct( Request $request) {
		$this->request = $request;
	}
	
    /**
     *  
     * @return \Illuminate\Http\Response
     */
     
  public function campaigns()
    {
       
	   $settings = AdminSettings::first();
       $data      = Campaigns::where('status', 'active' )->orderBy('id','DESC')->paginate($settings->result_request);
		
		return view('ajax.campaigns',['data' => $data, 'settings' => $settings])->render();
    }
	
	public function category() {
		
		 $settings = AdminSettings::first();
		 
		 $slug = $this->request->slug;
			
		 $category = Categories::where('slug','=',$slug)->first();
	  	 $data       = Campaigns::where('status', 'active')->where('categories_id',$category->id)->orderBy('id','DESC')->paginate($settings->result_request);
		
		return view('ajax.campaigns',['data' => $data, 'settings' => $settings, 'slug' => $category->slug])->render();		
		
	}// End Method
	
    public function donations()
    {
       
	   $settings = AdminSettings::first();
		$page   = $this->request->input('page');
		$id        = $this->request->input('id');
		$data    = Donations::where('campaigns_id',$id)->orderBy('id','desc')->paginate(10);

 		return view('ajax.donations',['data' => $data, 'settings' => $settings])->render();

    }//<--- End Method
    
    public function updatesCampaign()
    {
       
	    $settings = AdminSettings::first();
		$page     = $this->request->input('page');
		$id         = $this->request->input('id');
		$data     = Updates::where('campaigns_id',$id)->orderBy('id','desc')->paginate(1);

 		return view('ajax.updates-campaign',['data' => $data, 'settings' => $settings])->render();

    }//<--- End Method
    
    public function search() {
		
		 $settings = AdminSettings::first();
		 
		 $q = $this->request->slug;
		
		$data = Campaigns::where( 'title','LIKE', '%'.$q.'%' )
		->where('status', 'active' )
		->orWhere('location','LIKE', '%'.$q.'%')
		->where('status', 'active' )
		->groupBy('id')
		->orderBy('id', 'desc' )
		->paginate( $settings->result_request );
		
		return view('ajax.campaigns',['data' => $data, 'settings' => $settings, 'slug' => $q])->render();		
		
	}// End Method
	
	public function like(){
		
		$like = Like::firstOrNew(['user_id' => Auth::user()->id, 'campaigns_id' => $this->request->id]);
		
		$campaign = Campaigns::find($this->request->id);
	
		if( $like->exists ) {
			    
				// IF ACTIVE DELETE Like
				if( $like->status == '1' ) {
					$like->status = '0';
					$like->update();
				
				// ELSE ACTIVE AGAIN	
				} else {
					$like->status = '1';
					$like->update();
				}
			
		} else {
			
			// INSERT
			$like->save();
			
		}
				$totalLike = Helper::formatNumber( $campaign->likes()->count() );
				
				return $totalLike;
				
	}//<---- End Method

    public function friend(){

        $friend = Friend::firstOrNew(['user_id' => Auth::user()->id, 'target_id' => $this->request->id]);
        $state = 0;
        if( $friend->exists ) {
            // IF friend, remove friend
            if( $friend->state == '2' ) {
                $friend->state = '0';
                $friend->update();
                $state = 0;
                // ELSE ACTIVE AGAIN
            }else if( $friend->state == '0' ) {
                $friend->state = '1';
                $state = 1;
                $friend->update();
            }

        } else {
            // INSERT
            $state = 1;
            $friend->save();
        }

        // send email
        if($state == 1){
            $settings    = AdminSettings::first();
            $user_sender = Auth::user();
            $user_receiver = User::find($this->request->id);
            $profile_id = sprintf ("%05d",$user_sender->id);
            $message= ucfirst($user_sender->name)." ".ucfirst($user_sender->last_name)." Profile ID: ".$profile_id." would like to add you as a Friend on GetSigned Now. Login to your account to Accept or Request your Friend Request!";

            //send verification mail to user
            $_username      = $user_receiver->name;
            $_email_user    = $user_receiver->email;
            $_title_site    = $settings->title;
            $_email_noreply = $settings->email_no_reply;

//            Mail::send('emails.send', array('message' => $message, 'title' => $_title_site ),
//                function($message) use (
//                    $_username,
//                    $_email_user,
//                    $_title_site,
//                    $_email_noreply
//                ) {
//                    $message->from($_email_noreply, $_title_site);
//                    $message->subject(trans('users.title_email_verify'));
//                    $message->to($_email_user,$_username);
//                });
        }

        // send notification
//        $notification = Notification::firstOrNew(
//            [   'noti_type'=>'friend_request',
//                'ref_id' => Auth::user()->id,
//                'user_id' => $this->request->id
//                ]);
//        if( !$notification->exists ) {
//            $notification->save();
//        }
        Notification::create([
            'noti_type' => 'friend_request',
            'ref_id' => Auth::user()->id,
            'user_id' => $this->request->id
        ]);

        return $state;

    }//<---- End Method

    public function friend_cancel(){

        $friend = Friend::firstOrNew(['user_id' => Auth::user()->id, 'target_id' => $this->request->id]);
        $state = 0;
        if( $friend->exists ) {
            // IF friend, remove friend
            if( $friend->state == '1' ) {
                $friend->state = '0';
                $state = 0;
                $friend->update();
            }
        }

        // remove friend_request notification
        $update_noti = Notification::firstOrNew(['ref_id'=> Auth::user()->id, 'user_id' => $this->request->id, "noti_type"=>"friend_request", "solved"=>'0']);
        if( $update_noti->exists ){
            // if target user didn't check notification yet
            $update_noti->solved = '1';
            $update_noti->update();
        }

        // send notification
        $add_cancel_noti = Notification::firstOrNew([
            'noti_type' => 'cancel_friend_request',
            'ref_id' => Auth::user()->id,
            'user_id' => $this->request->id
        ]);
        if(!$add_cancel_noti->exists){
            $add_cancel_noti->save();
        }

        return $state;

    }//<---- End Method

    public function get_noti(){

        if( !Auth::check() ) {
            return array("success"=>"failed");
        }

        $data = Notification::leftJoin('users as b', function($join) {
            $join->on('notifications.ref_id', '=', 'b.id');
        })
        ->where('notifications.user_id', '=', Auth::user()->id)
        ->where('notifications.noti_type', '=', 'friend_request')
        ->where('notifications.solved', '=', '0')
        ->orderBy('created','DESC')->limit(5)
        ->select('notifications.*', 'b.id as s_id','b.name as s_name', 'b.last_name as s_last', 'b.avatar')
        ->get();
        return array("success"=>"OK", "data"=>$data);

    }//<---- End Method

    public function check_noti(){

        if( !Auth::check() ) {
            return array("success"=>"failed");
        }
        $noti_type = $this->request->noti_type;
        $friend = Friend::firstOrNew(['user_id' => $this->request->ref_id, 'target_id' => Auth::user()->id]);

        if( $friend->exists ) {
            switch ($noti_type){
                case "accept":
                    $state = '2';
                    break;
                case "reject":
                    $state = '0';
                    break;
            }
            // IF friend, remove friend
            if( $friend->state == '1' ) {
                $friend->state = $state;
                $friend->update();
            }

        } else {
            return array("success"=>"failed");
        }

        $notification = Notification::firstOrNew(['id' => $this->request->noti_id]);

        if( $notification->exists ) {
            if( $notification->solved == '0' ) {
                $notification->solved = "1";
                $notification->update();
            }

        } else {
            return array("success"=>"failed");
        }

        return array("success"=>"OK");

    }//<---- End Method
}
