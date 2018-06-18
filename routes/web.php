<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
/* 
 |-----------------------------------
 | Index
 |-----------------------------------
 */
Route::get('/', 'HomeController@index');

Route::get('home', function(){
	return redirect('/');
});


/* 
 |
 |-----------------------------------
 | Search
 |--------- -------------------------
 */
Route::get('search', 'HomeController@search');

/* 
 |
 |-----------------------------------
 | Categories List
 |--------- -------------------------
 */
Route::get('category/{slug}','HomeController@category');

// Categories
Route::get('categories','HomeController@categories') ;
Route::post('categories','HomeController@categories') ;


/* 
 |
 |-----------------------------------
 | Verify Account
 |--------- -------------------------
 */
Route::get('verify/account/{confirmation_code}', 'HomeController@getVerifyAccount')->where('confirmation_code','[A-Za-z0-9]+');

/* 
/* 
 |-----------------------------------
 | Authentication
 |-----------------------------------
 */	
Auth::routes();

// Logout
Route::get('/logout', 'Auth\LoginController@logout');

/*
 |
 |------------------------------------------------
 | Chat page
 |------------------------------------------------
 */
Route::get('/chat/{id?}', 'ChatController@index')->name('my-chat');
Route::post('/chat-api', 'ChatController@api')->name('my-chat-api');
Route::post('/chat-mess', 'ChatController@getMess')->name('my-chat-mess');



/* 
 |
 |-----------------------------------------------
 | Ajax Request
 |--------- -------------------------------------
 */
Route::get('ajax/donations', 'AjaxController@donations');
Route::get('ajax/campaign/updates', 'AjaxController@updatesCampaign');
Route::get('ajax/campaigns', 'AjaxController@campaigns');
Route::get('ajax/category', 'AjaxController@category');
Route::get('ajax/search', 'AjaxController@search');

/* 
 |
 |-----------------------------------
 | Contact Organizer
 |-----------------------------------
 */

Route::post('contact/organizer','CampaignsController@contactOrganizer');

/* 
 |
 |-----------------------------------
 | Details Campaign
 |--------- -------------------------
 */
Route::get('campaign/{id}/{slug?}','CampaignsController@view');

/* 
 |
 |-----------------------------------
 | User Views LOGGED
 |--------- -------------------------
 */
Route::group(['middleware' => 'auth'], function() {
	
	//<-------------- Create Campaign
	Route::get('create/campaign', function(){
	return view('campaigns.create');
	});
	//  Post
	Route::post('create/campaign','CampaignsController@create');
	
	//<-------------- Edit Campaign
	Route::get('edit/campaign/{id}','CampaignsController@edit');
	Route::post('edit/campaign/{id}','CampaignsController@post_edit');
	
	//<-------------- Post a Update
	Route::get('update/campaign/{id}','CampaignsController@update');
	Route::post('update/campaign/{id}','CampaignsController@post_update');
	
	//<-------------- Edit post a Update
	Route::get('edit/update/{id}','CampaignsController@edit_update');
	Route::post('edit/update/{id}','CampaignsController@post_edit_update');
	
	Route::post('delete/image/updates','CampaignsController@delete_image_update');
	
	// Delete Campaign
	Route::get('delete/campaign/{id}','CampaignsController@delete');
	
	// Withdrawal
	Route::get('account/withdrawals','CampaignsController@show_withdrawal');
	Route::post('campaign/withdrawal/{id}','CampaignsController@withdrawal');
	
	Route::get('account/withdrawals/configure', function(){
	return view('users.withdrawals-configure');
	});
	
	Route::post('withdrawals/configure/{type}','CampaignsController@withdrawalConfigure');
	
	Route::post('delete/withdrawal/{id}','CampaignsController@withdrawalDelete');
	
	// Account Settings
	Route::get('account','UserController@account');
	Route::post('account','UserController@update_account');
	
	// Password
	Route::get('account/password','UserController@password');
	Route::post('account/password','UserController@update_password');
	
	// Upload Avatar
	Route::post('upload/avatar','UserController@upload_avatar');
	
	// Campaigns
	Route::get('account/campaigns', function(){
		
		$_data = App\Models\Campaigns::where('user_id',Auth::user()->id)
	 ->orderBy('id','DESC')
	 ->paginate(20);
	 
	 // Deadline
	$timeNow = strtotime(Carbon\Carbon::now());
	
	foreach ($_data as $key) {
		if( $key->deadline != '' ) {
	    $_deadline = strtotime($key->deadline);
		  
		  if( $_deadline < $timeNow && $key->finalized == '0' ) {
		  	$sql = App\Models\Campaigns::find($key->id);
		  	$sql->finalized = '1';
			$sql->save();
		  }
	  }
	}

	$data = App\Models\Campaigns::where('user_id',Auth::user()->id)
	 ->orderBy('id','DESC')
	 ->paginate(20);

	return view('users.campaigns')->withData($data);
	});
	
	// Donations
	Route::get('account/donations', function(){
	return view('users.donations');
	});
	
	// Report Campaign
	Route::get('report/campaign/{id}/{user}','CampaignsController@report');
	
	// Ajax Like
	Route::post('ajax/like', 'AjaxController@like');

    // Ajax Friend request
    Route::post('ajax/friend', 'AjaxController@friend');
    // Ajax Cancel Friend request
    Route::post('ajax/cancel_friend', 'AjaxController@friend_cancel');

    // Ajax Check Noti request
    Route::post('ajax/get_noti', 'AjaxController@get_noti');

    // Ajax Check Noti request
    Route::post('ajax/check_noti', 'AjaxController@check_noti');

	// User Likes
	Route::get('user/likes', 'UserController@likes');
});
/* 
 |
 |-----------------------------------
 | Admin Panel
 |--------- -------------------------
 */
Route::group(['middleware' => 'role'], function() {
	
    // Upgrades
	Route::get('update/{version}','UpgradeController@update');
	
	// Dashboard
	Route::get('panel/admin','AdminController@admin');
	
	// Settings
	Route::get('panel/admin/settings','AdminController@settings');
	Route::post('panel/admin/settings','AdminController@saveSettings');
	
	// Limits
	Route::get('panel/admin/settings/limits','AdminController@settingsLimits');
	Route::post('panel/admin/settings/limits','AdminController@saveSettingsLimits');
	
	// Campaigns
	Route::get('panel/admin/campaigns','AdminController@campaigns');
	Route::post('panel/admin/campaigns','AdminController@saveCampaigns');
	
	// Edit Campaign
	Route::get('panel/admin/campaigns/edit/{id}','AdminController@editCampaigns');
	Route::post('panel/admin/campaigns/edit/{id}','AdminController@postEditCampaigns');
	
	//Withdrawals
	Route::get('panel/admin/withdrawals','AdminController@withdrawals');
	Route::get('panel/admin/withdrawal/{id}','AdminController@withdrawalsView');
	Route::post('panel/admin/withdrawals/paid/{id}','AdminController@withdrawalsPaid');
	
	Route::post('paypal/withdrawal/ipn','AdminController@withdrawlsIpn');
	
	
	// Delete Campaign
	Route::post('panel/admin/campaign/delete','AdminController@deleteCampaign');
	
	// Donations
	Route::get('panel/admin/donations','AdminController@donations');
	Route::get('panel/admin/donations/{id}','AdminController@donationView');
	
	// Members
	Route::resource('panel/admin/members', 'AdminController', 
		['names' => [
		    'edit'    => 'user.edit',
		    'destroy' => 'user.destroy'
		 ]]
	);
	
	// Add Member
	Route::get('panel/admin/member/add','AdminController@add_member');
	Route::post('panel/admin/member/add','AdminController@storeMember');
	
	// Add/Change member avatar
	Route::post('panel/admin/member/uploadAvatar','AdminController@uploadAvatar');
	
	// Pages
	Route::resource('panel/admin/pages', 'PagesController', 
		['names' => [
		    'edit'    => 'pages.edit',
		    'destroy' => 'pages.destroy'
		 ]]
	);
	
	// Payments Settings
	Route::get('panel/admin/payments','AdminController@payments');
	Route::post('panel/admin/payments','AdminController@savePayments');
	
	// Profiles Social
	Route::get('panel/admin/profiles-social','AdminController@profiles_social');
	Route::post('panel/admin/profiles-social','AdminController@update_profiles_social');
	
	// Admin categories
	Route::get('panel/admin/categories','AdminController@categories');
	Route::get('panel/admin/categories/add','AdminController@addCategories');
	Route::post('panel/admin/categories/add','AdminController@storeCategories');
	Route::get('panel/admin/categories/edit/{id}','AdminController@editCategories')->where(array( 'id' => '[0-9]+'));
	Route::post('panel/admin/categories/update','AdminController@updateCategories');
	Route::get('panel/admin/categories/delete/{id}','AdminController@deleteCategories')->where(array( 'id' => '[0-9]+'));
	
	// Campaigns Reported
	Route::get('panel/admin/campaigns/reported','AdminController@reportedCampaigns');
	Route::post('panel/admin/campaigns/reported/delete','AdminController@reportedDeleteCampaigns');
	
});

/* 
 |
 |-----------------------------------
 | Donations
 |--------- -------------------------
 */
Route::get('donate/{id}/{slug?}','DonationsController@show');
Route::post('donate/{id}','DonationsController@send');

// Paypal IPN
Route::post('paypal/ipn','DonationsController@paypalIpn');


	Route::get('paypal/donation/success/{id}', function($id){
			
			session()->put('donation_success', trans('misc.donation_success'));
			return redirect("campaign/".$id);
	});
	

	Route::get('paypal/donation/cancel/{id}', function($id){
			
			session()->put('donation_cancel', trans('misc.donation_cancel'));
	       return redirect("campaign/".$id);
	});
	
/* 
 |
 |------------------------
 | Pages Static Custom
 |--------- --------------
 */
Route::get('page/{page}', function( $page ){
	
	$response = App\Models\Pages::where( 'slug','=', $page )->first();
	
	$total = count( $response );
	
	if( $total == 0 ) {
		abort(404);
	} else {
		
		$title = $response->title.' - ';
		return view('pages.home', compact('response', 'title'));
	}

})->where('page','[^/]*' );


/* 
 |
 |------------------------
 | Embed Widget
 |--------- --------------
 */
Route::get('c/{id}/widget.js', function($id){
	
	$iframeUrl = url('c',$id).'/widget/show';
	return 'var html = \'<iframe align="middle" scrolling="no" width="100%" height="550" frameBorder="0" src="'.$iframeUrl.'"></iframe>\'; document.write( html );';
	
});

// Embed Widget iFrame
Route::get('c/{id}/widget/show', function($id){
	
	$response = App\Models\Campaigns::where('id',$id)->where('status','active')->firstOrFail();
	return view('includes.embed')->withResponse($response);	
});

