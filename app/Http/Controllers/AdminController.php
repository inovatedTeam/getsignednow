<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Campaigns;
use App\Models\CampaignsReported;
use App\Models\Donations;
use App\Models\Categories;
use App\Models\Withdrawals;
use App\Helper;
use Carbon\Carbon;
use Fahim\PaypalIPN\PaypalIPNListener;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;
use Mail;

class AdminController extends Controller {
	
	public function __construct( AdminSettings $settings) {
		$this->settings = $settings::first();
	}
	
		 public function index(Request $request) {
	 	
		$query = $request->input('q');
		
		if( $query != '' && strlen( $query ) > 2 ) {
		 	$data = User::where('name', 'LIKE', '%'.$query.'%')
		 	->orderBy('id','desc')->paginate(20);
		 } else {
		 	$data = User::orderBy('id','desc')->paginate(20);
		 }
		
    	return view('admin.members', ['data' => $data,'query' => $query]);
	 }
	 
	 public function uploadAvatar(Request $request){
	     
	    $settings  = AdminSettings::first();
        $id = $request->input('user_id');
   	    $user = User::findOrFail($id);
		$validator = Validator::make($request->all(), [
		'photo'       => 'required|mimes:jpg,gif,png,jpe,jpeg|image_size:>=125,>=125|max:'.$settings->file_size_allowed.'',
	    	]);
				   
		   if ($validator->fails()) {
		        return response()->json([
				        'success' => false,
				        'errors' => $validator->getMessageBag()->toArray(),
				    ]);
		    }
		
		// PATHS
		$temp    = 'public/temp/';
	    $path    = 'public/avatar/'; 
		$imgOld      = $path.$user->avatar;
		
		 //<--- HASFILE PHOTO
	    if( $request->hasFile('photo') )	{
	    	
			$extension  = $request->file('photo')->getClientOriginalExtension();
			$avatar       = strtolower($user->id.time().str_random(15).'.'.$extension );
			
			if( $request->file('photo')->move($temp, $avatar) ) {
				
				set_time_limit(0);
				
				Helper::resizeImageFixed( $temp.$avatar, 125, 125, $temp.$avatar );
				
				// Copy folder
				if ( \File::exists($temp.$avatar) ) {
					/* Avatar */	
					\File::copy($temp.$avatar, $path.$avatar);
					\File::delete($temp.$avatar);
				}//<--- IF FILE EXISTS
				
				//<<<-- Delete old image -->>>/
				if ( \File::exists($imgOld) && $imgOld != $path.'default.jpg' ) {
					\File::delete($temp.$avatar);	
					\File::delete($imgOld);
				}//<--- IF FILE EXISTS #1
				
				// Update Database
				User::where( 'id', $user->id )->update( array( 'avatar' => $avatar ) );
				
				return response()->json([
				        'success' => true,
				        'avatar' => url($path.$avatar),
				    ]);
					
			}// Move
	    }//<--- HASFILE PHOTO 
	     
	 }

	public function edit($id) {
	    
		$data = User::findOrFail($id);
		
		if( $data->id == 1 || $data->id == Auth::user()->id ) {
			\Session::flash('info_message', trans('admin.user_no_edit'));
			return redirect('panel/admin/members');
		}
    	return view('admin.edit-member')->withData($data);
	
	}//<--- End Method
	
	public function update($id, Request $request) {
    	
    $user = User::findOrFail($id);
		
	$input = $request->all();
	
	if( !empty( $request->password ) )	 {
		$rules = array(			
                        'first_name'        => 'required|min:3|max:25',
                        'last_name'         => 'required|min:3|max:25',
                        'mobile_number'     => 'required|numeric',
                        'state'             => 'required|max:255',
                        'email'     => 'required|email|unique:users,email,'.$id,                           
                        'countries_id'      => 'required',
                        'network_type_id'   => 'required',
                        'password' => 'min:6',
			);
			
			$password = \Hash::make($request->password);
			
	} else {
		$rules = array(
			'first_name'        => 'required|min:3|max:25',
                        'last_name'         => 'required|min:3|max:25',
                        'mobile_number'     => 'required|numeric',
                        'state'             => 'required|max:255',
                        'email'     => 'required|email|unique:users,email,'.$id,                           
                        'countries_id'      => 'required',
                        'network_type_id'   => 'required',
			);
			
			$password = $user->password;
	}
		
	   $this->validate($request,$rules);
	  
	  $user->name = $request->first_name;
	  $user->last_name = $request->last_name;
	  $user->mobile_number = $request->mobile_number;
	  $user->state = $request->state;
	  $user->email = $request->email;
	  $user->countries_id = $request->countries_id;
	  $user->network_type_id = $request->network_type_id;
	  $user->role = $request->role;
	  $user->password = $password;
      $user->save();

    \Session::flash('success_message', trans('admin.success_update'));

    return redirect('panel/admin/members');
	
	}//<--- End Method
	
	public function destroy($id)
    {
    	$user = User::findOrFail($id);
		
    	if( $user->id == 1 || $user->id == Auth::user()->id ) {
    		return redirect('panel/admin/members');
			exit;
    	}
				
		// Find User
		
		// Stop Campaigns
		$allCampaigns = Campaigns::where('user_id',$id)->update(array('finalized' => '1'));
		
		//<<<-- Delete Avatar -->>>/
		$fileAvatar    = 'public/avatar/'.$user->avatar;
			
		if ( \File::exists($fileAvatar) && $user->avatar != 'default.jpg' ) {
			 \File::delete($fileAvatar);	
		}//<--- IF FILE EXISTS
		
			
        $user->delete();
		return redirect('panel/admin/members');
		
    }//<--- End Method
    
    public function add_member() {
    	return view('admin.add-member');
    }
	
	public function storeMember(Request $request){
		
		$settings  = AdminSettings::first();
		$this->validate($request, [
                    'first_name'        => 'required|max:255',
                    'last_name'         => 'required|max:255',
                    'mobile_number'     => 'required|numeric',
                    'state'             => 'required|max:255',
                    'email'             => 'required|email|max:255|unique:users',
                    'password'          => 'required|confirmed|min:6',
                    'countries_id'      => 'required',
                    'network_type_id'   => 'required',
                    'photo'             => 'mimes:jpg,gif,png,jpe,jpeg|image_size:>=125,>=125|max:'.$settings->file_size_allowed.'',
                ]);
		
		$user = new User;
		$user->name = $request->first_name;
		$user->last_name = $request->last_name;
		$user->mobile_number = $request->mobile_number;
		$user->state = $request->state;
		$user->company_name = $request->company_name;
		$user->countries_id = $request->countries_id;
		$user->network_type_id = $request->network_type_id;
		$user->email = $request->email;
		$user->role = $request->role;
		$user->avatar = 'default.jpg';
		$user->token = str_random(80);
		$user->password = \Hash::make($request->password);
		$user->save();

		 //<--- HASFILE PHOTO
	    if( $request->hasFile('photo') ) {
	        
	        $id = $user->id;

    		// PATHS
    		$temp    = 'public/temp/';
    	    $path    = 'public/avatar/'; 
    		$imgOld      = $path.$user->avatar;
    	    	
    		$extension  = $request->file('photo')->getClientOriginalExtension();
    		$avatar       = strtolower($user->id.time().str_random(15).'.'.$extension );
    		
    		if( $request->file('photo')->move($temp, $avatar) ) {
    			
    			set_time_limit(0);
    			
    			Helper::resizeImageFixed( $temp.$avatar, 125, 125, $temp.$avatar );
    			
    			// Copy folder
    			if ( \File::exists($temp.$avatar) ) {
    				/* Avatar */	
    				\File::copy($temp.$avatar, $path.$avatar);
    				\File::delete($temp.$avatar);
    			}
    			// Update Database
    			User::where( 'id', $user->id )->update( array( 'avatar' => $avatar ) );
    		}
	    }
		
		 \Session::flash('success_message', trans('admin.success_add'));
		return redirect('panel/admin/members');
		
	}
    
	// START
	public function admin() {
		
		return view('admin.dashboard');
				
	}//<--- END METHOD
	
	public function settings() {
		
		return view('admin.settings')->withSettings($this->settings);
		
	}//<--- END METHOD
	
	public function saveSettings(Request $request) {
						
		$rules = array(
            'title'            => 'required',
	        'welcome_text' 	   => 'required',
	        'welcome_subtitle' => 'required',
	        'keywords'         => 'required',
	        'description'      => 'required',
	        'email_no_reply'   => 'required',
	        'email_admin'      => 'required',
        );
		
		$this->validate($request, $rules);
		
		$sql                      = AdminSettings::first();
		$sql->title               = $request->title;
		$sql->welcome_text        = $request->welcome_text;
		$sql->welcome_subtitle    = $request->welcome_subtitle;
		$sql->keywords            = $request->keywords;
		$sql->description         = $request->description;
		$sql->email_no_reply      = $request->email_no_reply;
		$sql->email_admin         = $request->email_admin;
		$sql->auto_approve_campaigns = $request->auto_approve_campaigns;
		$sql->captcha                = $request->captcha;
		$sql->email_verification = $request->email_verification;
		$sql->save();

		\Session::flash('success_message', trans('admin.success_update'));

    	return redirect('panel/admin/settings');
						
	}//<--- END METHOD
	
	public function settingsLimits() {
		
		return view('admin.limits')->withSettings($this->settings);
		
	}//<--- END METHOD
	
	public function saveSettingsLimits(Request $request) {
		
				
		$rules = array(
	        'min_campaign_amount'             => 'required|integer|min:1',
	        'max_campaign_amount'             => 'required|integer|min:1',
	        'min_donation_amount'             => 'required|integer|min:1',
	        'max_donation_amount'             => 'required|integer|min:1',
        );
		
		$this->validate($request, $rules);
				
		$sql                      = AdminSettings::first();
		$sql->result_request      = $request->result_request;
		$sql->file_size_allowed   = $request->file_size_allowed;
		$sql->min_campaign_amount   = $request->min_campaign_amount;
		$sql->max_campaign_amount   = $request->max_campaign_amount;
		$sql->min_donation_amount   = $request->min_donation_amount;
		$sql->max_donation_amount   = $request->max_donation_amount;
		
		$sql->save();

		\Session::flash('success_message', trans('admin.success_update'));

    	return redirect('panel/admin/settings/limits');
						
	}//<--- END METHOD
	
	public function profiles_social(){
		return view('admin.profiles-social')->withSettings($this->settings);
	}//<--- End Method
	
	public function update_profiles_social(Request $request) {
			
		$sql = AdminSettings::find(1);
		
		$rules = array(
            'twitter'    => 'url',
            'facebook'   => 'url',
            'googleplus' => 'url',
            'linkedin'   => 'url',
        );
		
		$this->validate($request, $rules);
		
	    $sql->twitter       = $request->twitter;
		$sql->facebook      = $request->facebook;
		$sql->googleplus    = $request->googleplus;
		$sql->instagram     = $request->instagram;
		
		$sql->save();
	
	    \Session::flash('success_message', trans('admin.success_update'));
	
	    return redirect('panel/admin/profiles-social');
	}//<--- End Method
	
	public function donations(){
		
		$data = Donations::orderBy('id','DESC')->paginate(100);
		return view('admin.donations', ['data' => $data, 'settings' => $this->settings]);
	}//<--- End Method
	
	public function donationView($id){
		
		$data = Donations::findOrFail($id);
		return view('admin.donation-view', ['data' => $data, 'settings' => $this->settings]);
	}//<--- End Method
	
	public function payments(){
		return view('admin.payments-settings')->withSettings($this->settings);
	}//<--- End Method
	
	public function savePayments(Request $request) {
			
		$sql = AdminSettings::find(1);
		
		$rules = array(
            'paypal_account'    => 'required|email',
        );
		
		$this->validate($request, $rules);
		
		switch( $request->currency_code ) {
			case 'USD':
				$currency_symbol  = '$';
				break;
			case 'EUR':
				$currency_symbol  = '€';
				break;
			case 'GBP':
				$currency_symbol  = '£';
				break;
			case 'AUD':
				$currency_symbol  = '$';
				break;
			case 'JPY':
				$currency_symbol  = '¥';
				break;
				
			case 'BRL':
				$currency_symbol  = 'R$';
				break;
			case 'MXN':
				$currency_symbol  = '$';
				break;
			case 'SEK':
				$currency_symbol  = 'Kr';
				break;
			case 'CHF':
				$currency_symbol  = 'CHF';
				break;
			case 'SGD':
				$currency_symbol  = '$';
				break;
			case 'DKK':
				$currency_symbol  = 'Kr';
				break;
			case 'RUB':
				$currency_symbol  = 'руб';
				break;
			case 'CAD':
				$currency_symbol  = '$';
				break;
			case 'CZK':
				$currency_symbol  = 'Kč';
				break;
			case 'HKD':
				$currency_symbol  = 'HK$';
				break;
			case 'PLN':
				$currency_symbol  = 'zł';
				break;
			case 'NOK':
				$currency_symbol  = 'Kr';
				break;
		}
		
	    $sql->paypal_account     = $request->paypal_account;
		$sql->currency_symbol  = $currency_symbol;
		$sql->currency_code    = $request->currency_code;
		$sql->paypal_sandbox   = $request->paypal_sandbox;
		$sql->payment_gateway    = $request->payment_gateway;
		$sql->fee_donation = $request->fee_donation;
		$sql->stripe_secret_key    = $request->stripe_secret_key;
		$sql->stripe_public_key    = $request->stripe_public_key;
		
		$sql->save();
	
	    \Session::flash('success_message', trans('admin.success_update'));
	
	    return redirect('panel/admin/payments');
	}//<--- End Method
	
	public function campaigns(){
		
	$_data = Campaigns::orderBy('id','DESC')->paginate(50);
	
	// Deadline
	$timeNow = strtotime(Carbon::now());
	
		foreach ($_data as $key) {
			if( $key->deadline != '' ) {
		    $_deadline = strtotime($key->deadline);
			  
			  if( $_deadline < $timeNow && $key->finalized == '0' ) {
			  	$sql = Campaigns::find($key->id);
			  	$sql->finalized = '1';
				$sql->save();
			  }
		  }
		}
		
		$data = Campaigns::orderBy('id','DESC')->paginate(50);
		return view('admin.campaigns', ['data' => $data, 'settings' => $this->settings]);
	}//<--- End Method
	
	public function withdrawals(){
		
		$data = Withdrawals::orderBy('id','DESC')->paginate(50);
		return view('admin.withdrawals', ['data' => $data, 'settings' => $this->settings]);
	}//<--- End Method
	
	public function withdrawalsView($id){
		$data = Withdrawals::findOrFail($id);
		return view('admin.withdrawal-view', ['data' => $data, 'settings' => $this->settings]);
	}//<--- End Method
	
	public function withdrawalsPaid(Request $request){
		
		$data = Withdrawals::findOrFail($request->id);
		$user = User::find($data->campaigns()->user_id);
		
		$data->status       = 'paid';
		$data->date_paid = Carbon::now();
		$data->save();
		
		//<------ Send Email to User ---------->>>
		$amount    = $this->settings->currency_symbol.$data->amount.' '.$this->settings->currency_code;
		$sender     = $this->settings->email_no_reply;
	    $titleSite     = $this->settings->title;
		$campaign = $data->campaigns()->title;
		$fullNameUser = $user->name;
		$_emailUser = $user->email;
		
		Mail::send('emails.withdrawal-processed', array( 
					'campaign' => $campaign, 
					'amount' => $amount, 
					'title_site' => $titleSite, 
					'fullname' => $fullNameUser
		), 
					function($message) use ( $sender, $fullNameUser, $titleSite, $_emailUser)
						{
						    $message->from($sender, $titleSite)
						    	->to($_emailUser, $fullNameUser)
								->subject( trans('misc.withdrawal_processed').' - '.$titleSite );
						});
			//<------ Send Email to User ---------->>>
						
		return redirect('panel/admin/withdrawals');
		
	}//<--- End Method
	
	public function withdrawlsIpn(){
		
		$ipn = new PaypalIPNListener();
		
		$ipn->use_curl = false;
		
		if ( $this->settings->paypal_sandbox == 'true') {
			// SandBox
			$ipn->use_sandbox = true;
			} else {
			// Real environment
			$ipn->use_sandbox = false;
			}
			
	    $verified = $ipn->processIpn();
		
		$withdrawalsID    = $_POST['custom'];
		$payment_status = $_POST['payment_status'];
		$txn_id               = $_POST['txn_id'];
		
		if ($verified) {
	        if($payment_status == 'Completed'){
	        	
				$verifiedTxnId = Withdrawals::where('txn_id',$txn_id)->first();
				$data             = Withdrawals::find($withdrawalsID);
				$user            = User::find($data->campaigns()->user_id);
				
				if( !isset( $verifiedTxnId ) && isset($data) ) {
					$data->status       = 'paid';
					$data->date_paid = Carbon::now();
					$data->save();
					
					//<------ Send Email to User ---------->>>
		$amount    = $this->settings->currency_symbol.$data->amount.' '.$this->settings->currency_code;
		$sender     = $this->settings->email_no_reply;
	    $titleSite     = $this->settings->title;
		$campaign = $data->campaigns()->title;
		$fullNameUser = $user->name;
		$_emailUser = $user->email;
		
		Mail::send('emails.withdrawal-processed', array( 
					'campaign' => $campaign, 
					'amount' => $amount, 
					'title_site' => $titleSite, 
					'fullname' => $fullNameUser
		), 
					function($message) use ( $sender, $fullNameUser, $titleSite, $_emailUser)
						{
						    $message->from($sender, $titleSite)
						    	->to($_emailUser, $fullNameUser)
								->subject( trans('misc.withdrawal_processed').' - '.$titleSite );
						});
			//<------ Send Email to User ---------->>>
					
				}// Isset $verifiedTxnId
     	
	        }// $payment_status
		}// $verified
		
	}//<--- End Method
	
	
	public function editCampaigns($id){
		
		$data = Campaigns::findOrFail($id);
		return view('admin.edit-campaign', ['data' => $data, 'settings' => $this->settings]);
	}
	
	public function postEditCampaigns(Request $request){
		
		$sql = Campaigns::findOrFail($request->id);
		
		Validator::extend('text_required', function($attribute, $value, $parameters)
			{
				$value = preg_replace("/\s|&nbsp;/",'',$value);   
			    return strip_tags($value);
			});
			
		$messages = array (
		'description.required' => trans('misc.description_required'),
		'description.text_required' => trans('misc.text_required'),
		'categories_id.required' => trans('misc.please_select_category'),
		'goal.min' => trans('misc.amount_minimum', ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
		'goal.max' => trans('misc.amount_maximum', ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
	);
	
		$rules = array(
            'title'             => 'required|min:3|max:45',
            'categories_id'  => 'required',
	    	'goal'             => 'required|integer|max:'.$this->settings->max_campaign_amount.'|min:'.$this->settings->min_campaign_amount,
	    	 'location'        => 'required|max:50',
	        'description'  => 'text_required|required|min:20',
        );
		
		$this->validate($request, $rules, $messages);
		
		$description = html_entity_decode($request->description);
		
		//REMOVE SCRIPT TAG
		$description = Helper::removeTagScript($description);
					
		//REMOVE SCRIPT Iframe
		//$description = Helper::removeTagIframe($description);
		
		$description = trim(Helper::spaces($description));
		
		// Status
		if( $request->finalized == 'pending' ) {
			$statusGlobal = 'pending';
			$request->finalized = '0';
		} elseif($request->finalized == '0' || $request->finalized == 1 ) {
			$statusGlobal = 'active';
			$request->finalized = $request->finalized;
		}
		
		$sql->title = $request->title;
		$sql->goal = $request->goal;
		$sql->location = $request->location;
		$sql->description = Helper::removeBR($description);
		$sql->finalized = $request->finalized;
		$sql->status = $statusGlobal;
		$sql->categories_id = $request->categories_id;
		$sql->featured = $request->featured;
		$sql->save();

		\Session::flash('success_message', trans('admin.success_update'));
	    return redirect('panel/admin/campaigns');
	}
	
	public function deleteCampaign(Request $request){
		
		$data = Campaigns::findOrFail($request->id);
		
		$path_small     = 'public/campaigns/small/'; 
		$path_large     = 'public/campaigns/large/';
		$path_updates = 'public/campaigns/updates/';
		
		$updates = $data->updates()->get();
		
		//Delete Updates
		foreach ($updates as $key) {
			
			if ( \File::exists($path_updates.$key->image) ) {
					\File::delete($path_updates.$key->image);
				}//<--- if file exists
				
				$key->delete();
		}//<-- 
		
		// Delete Campaign
		if ( \File::exists($path_small.$data->small_image) ) {
					\File::delete($path_small.$data->small_image);
				}//<--- if file exists
				
				if ( \File::exists($path_large.$data->large_image) ) {
					\File::delete($path_large.$data->large_image);
				}//<--- if file exists
		
		 $data->delete();
		 
		 \Session::flash('success_message', trans('misc.success_delete'));
	    return redirect('panel/admin/campaigns');
	}

	// START
	public function categories() {
		
		$categories      = Categories::orderBy('name')->get();
		$totalCategories = count( $categories );
		
		return view('admin.categories', compact( 'categories', 'totalCategories' ));
				
	}//<--- END METHOD
	
	public function addCategories() {
		
		return view('admin.add-categories');
				
	}//<--- END METHOD
	
	public function storeCategories(Request $request) {
		
		$temp            = 'public/temp/'; // Temp
	    $path            = 'public/img-category/'; // Path General
	    					
		Validator::extend('ascii_only', function($attribute, $value, $parameters){
    		return !preg_match('/[^x00-x7F\-]/i', $value);
		});
		
		$rules = array(
            'name'        => 'required',
	        'slug'        => 'required|ascii_only|unique:categories',
	        'thumbnail'   => 'mimes:jpg,gif,png,jpe,jpeg|image_size:>=457,>=359',
        );
		
		$this->validate($request, $rules);
		
		if( $request->hasFile('thumbnail') )	{
		
		$extension              = $request->file('thumbnail')->getClientOriginalExtension();
		$type_mime_shot   = $request->file('thumbnail')->getMimeType();
		$sizeFile                 = $request->file('thumbnail')->getSize();
		$thumbnail              = $request->slug.'-'.str_random(32).'.'.$extension;
		
		if( $request->file('thumbnail')->move($temp, $thumbnail) ) {
			
			$image = Image::make($temp.$thumbnail);
			
			if(  $image->width() == 457 && $image->height() == 359 ) {
				
					\File::copy($temp.$thumbnail, $path.$thumbnail);
					\File::delete($temp.$thumbnail);
			
			} else {
				$image->fit(457, 359)->save($temp.$thumbnail);
				
				\File::copy($temp.$thumbnail, $path.$thumbnail);
				\File::delete($temp.$thumbnail);
			}
			
			}// End File
		} // HasFile
		
else {
	$thumbnail = '';
}
		
		$sql                      = New Categories;
		$sql->name         = $request->name;
		$sql->slug            = $request->slug;
		$sql->mode         = $request->mode;
		$sql->image       = $thumbnail;
		$sql->save();

		\Session::flash('success_message', trans('admin.success_add_category'));

    	return redirect('panel/admin/categories');
						
	}//<--- END METHOD
	
	public function editCategories($id) {
		
		$categories        = Categories::find( $id );
		
		return view('admin.edit-categories')->with('categories',$categories);
				
	}//<--- END METHOD
	
	public function updateCategories( Request $request ) {
		
		
		$categories        = Categories::find( $request->id );
		$temp            = 'public/temp/'; // Temp
	    $path            = 'public/img-category/'; // Path General
	    
	    if( !isset($categories) ) {
			return redirect('panel/admin/categories');
		}
			
		Validator::extend('ascii_only', function($attribute, $value, $parameters){
    		return !preg_match('/[^x00-x7F\-]/i', $value);
		});
		
		$rules = array(
            'name'        => 'required',
	        'slug'        => 'required|ascii_only|unique:categories,slug,'.$request->id,
	        'thumbnail'   => 'mimes:jpg,gif,png,jpe,jpeg|image_size:>=457,>=359',
	     );
		
		$this->validate($request, $rules);	
		
		if( $request->hasFile('thumbnail') )	{
		
		$extension              = $request->file('thumbnail')->getClientOriginalExtension();
		$type_mime_shot   = $request->file('thumbnail')->getMimeType();
		$sizeFile                 = $request->file('thumbnail')->getSize();
		$thumbnail              = $request->slug.'-'.str_random(32).'.'.$extension;
		
		if( $request->file('thumbnail')->move($temp, $thumbnail) ) {
			
			$image = Image::make($temp.$thumbnail);
			
			if(  $image->width() == 457 && $image->height() == 359 ) {
				
					\File::copy($temp.$thumbnail, $path.$thumbnail);
					\File::delete($temp.$thumbnail);
			
			} else {
				$image->fit(457, 359)->save($temp.$thumbnail);
				
				\File::copy($temp.$thumbnail, $path.$thumbnail);
				\File::delete($temp.$thumbnail);
			}
			
			// Delete Old Image
			\File::delete($path.$categories->thumbnail);
			
			}// End File
		} // HasFile
		else {
			$thumbnail = $categories->image;
		}	
		
		// UPDATE CATEGORY
		$categories->name         = $request->name;
		$categories->slug            = $request->slug;
		$categories->mode         = $request->mode;
		$categories->image         = $thumbnail;
		$categories->save();

		\Session::flash('success_message', trans('misc.success_update'));

    	return redirect('panel/admin/categories');
				
	}//<--- END METHOD
	
	public function deleteCategories($id){
		
		$categories        = Categories::find( $id );
		$thumbnail          = 'public/img-category/'.$categories->image; // Path General
		
		if( !isset($categories) ) {
			return redirect('panel/admin/categories');
		} else {
					
			// Delete Category	
			$categories->delete();
			
			// Delete Thumbnail
			if ( \File::exists($thumbnail) ) {
				\File::delete($thumbnail);	
			}//<--- IF FILE EXISTS
			
			return redirect('panel/admin/categories');
		}	
	}//<--- END METHOD
	
	public function reportedCampaigns(){
		$data = CampaignsReported::orderBy('id','DESC')->paginate(50);
		return view('admin.reported-campaigns')->withData($data);
	}//<--- END METHOD
	
	public function reportedDeleteCampaigns( Request $request ){
		
		$report = CampaignsReported::find($request->id);
		
		if( isset( $report ) ) {
			$report->delete();
			return redirect('panel/admin/campaigns/reported');
		} else {
			return redirect('panel/admin/campaigns/reported');
		}
       	
	}//<--- END METHOD
	
	
			
}// End Class