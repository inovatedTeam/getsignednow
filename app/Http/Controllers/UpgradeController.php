<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Categories;
use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Campaigns;

class UpgradeController extends Controller {
	
	public function update($version) {
		
		$upgradeDone = '<h2 style="text-align:center; margin-top: 30px; font-family: Arial, san-serif;color: #4BBA0B;">'.trans('admin.upgrade_done').' <a style="text-decoration: none; color: #F50;" href="'.url('/').'">'.trans('error.go_home').'</a></h2>';
		
		//<---------------------------- Version 1.2
		if( $version == '1.2' ) {
			 
			 $category = Categories::first();
			
			if( isset($category->image) ) {
				return redirect('/');
			} else {
				
				Schema::table('categories', function($table){
					$table->string('image', 200)->after('mode');
				 });
				 
				return $upgradeDone;
			}
		}//<------------------------ Version 1.2
		
		//<-------------------------- Version 1.6
		if( $version == '1.6' ) {
			
			$admin = AdminSettings::first();
			
			if( isset($admin->auto_approve_campaigns) ) {
				return redirect('/');
			} else {
				
				Schema::table('admin_settings', function($table){
					$table->enum('auto_approve_campaigns', ['0', '1'])->default('1')->after('fee_donation');
				 });
				 
				return $upgradeDone;
			}
		}//<------------------------- Version 1.6
		
		//<------------------------ Version 1.7
		if( $version == '1.7' ) {
			
			$admin = AdminSettings::first();
			$campaigns = Campaigns::first();
			
			if( isset($admin->max_donation_amount) && isset( $campaigns->featured ) ) {
				return redirect('/');
			} else {
				
				Schema::table('admin_settings', function($table){
					$table->unsignedInteger('max_donation_amount')->after('stripe_public_key');
				 });
				 
				 Schema::table('campaigns', function($table){
				 	$table->enum('featured', ['0', '1'])->default('0')->after('categories_id');
				 });
				 
				return $upgradeDone;
			}
		}//<---------------------- Version 1.7
		
		//<------------------------ Version 1.8
		if( $version == '1.8' ) {
			
			
			if( Schema::hasTable('campaigns_reported') ) {
				return redirect('/');
			} else {
				
				 Schema::create('campaigns_reported', function ($table) {
				 	
				    $table->engine = 'InnoDB';
				
				    $table->increments('id');
					$table->unsignedInteger('user_id');
					$table->unsignedInteger('campaigns_id');
					$table->timestamp('created_at');
				});
				
				return $upgradeDone;
			}
		}//<---------------------- Version 1.8
		
		
		//<------------------------ Version 1.9.1
		if( $version == '1.9.1' ) {
			
			
			if( Schema::hasTable('likes') ) {
				return redirect('/');
			} else {
				
				 Schema::create('likes', function ($table) {
				 	
				    $table->engine = 'InnoDB';
				
				    $table->increments('id');
					$table->unsignedInteger('user_id');
					$table->unsignedInteger('campaigns_id');
					$table->enum('status', ['0', '1'])->default('1');
					$table->timestamp('date');
				});
				
				return $upgradeDone;
			}
		}//<---------------------- Version 2.0
		
		if( $version == '2.0' ) {
			
			$query = Campaigns::first();
			
			if( isset($query->deadline) ) {
				return redirect('/');
			} else {
				
				Schema::table('campaigns', function($table){
					$table->string('deadline', 200)->after('featured');
				 });
				 
				return $upgradeDone;
			}
		}//<------------------------- Version 2.0

	}//<--- End Method

}
