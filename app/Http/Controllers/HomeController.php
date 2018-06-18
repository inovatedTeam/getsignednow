<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\Campaigns;
use App\Models\Categories;
use App\Models\User;
use App\Models\Networks;
use App\Models\Countries;

class HomeController extends Controller
{

    /**
     *  
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
	   $settings = AdminSettings::first();
	   $categories = Categories::where('mode','on')->orderBy('name')->get();
       $data      = Campaigns::where('status', 'active')->orderBy('id','DESC')->paginate($settings->result_request);
		
		return view('index.home', ['data' => $data, 'categories' => $categories]);
    }
	
	public function search(Request $request) {

		$q = trim($request->input('q'));
		$settings = AdminSettings::first();
		
		$page = $request->input('page');
		
		$data = Campaigns::where( 'title','LIKE', '%'.$q.'%' )
		->where('status', 'active' )
		->orWhere('location','LIKE', '%'.$q.'%')
		->where('status', 'active' )
		->groupBy('id')
		->orderBy('id', 'desc' )
		->paginate( $settings->result_request );

		
		$title = trans('misc.result_of').' '. $q .' - ';
		
		$total = $data->total();
		
		//<--- * If $page not exists * ---->
		if( $page > $data->lastPage() ) {
			abort('404');
		}
		
		//<--- * If $q is empty or is minus to 1 * ---->
		if( $q == '' || strlen( $q ) <= 1 ){
			return redirect('/');
		}
		
		return view('default.search', compact( 'data', 'title', 'total', 'q' ));
		
	}// End Method
	
		public function getVerifyAccount( $confirmation_code ) {
		
        // Session Inactive
		if( !Auth::check() ) {
		$user = User::where( 'confirmation_code', $confirmation_code )->where('status','pending')->first();
		
		if( $user ) {
			
			$update = User::where( 'confirmation_code', $confirmation_code )
			->where('status','pending')
			->update( array( 'status' => 'active', 'confirmation_code' => '' ) );
			
			Auth::loginUsingId($user->id);
			
			 return redirect('/')
					->with([
						'success_verify' => true,
					]);
			} else {
			return redirect('/')
					->with([
						'error_verify' => true,
					]);
			}
		} else {
			
			// Session Active
			$user = User::where( 'confirmation_code', $confirmation_code )->where('status','pending')->first();
			 if( $user ) {
			
			$update = User::where( 'confirmation_code', $confirmation_code )
			->where('status','pending')
			->update( array( 'status' => 'active', 'confirmation_code' => '' ) );
			
			 return redirect('/')
					->with([
						'success_verify' => true,
					]);
			} else {
			return redirect('/')
					->with([
						'error_verify' => true,
					]);
			}
		}
	}// End Method
	
	public function categories(Request $request){
	    
	    if($request->input()){
	        
	        $whereStatement = [];
	        $whereStatement = ['categories.mode' => 'on'];
	        $whereStatement = ['campaigns.status'=> 'active'];
	        $settings = AdminSettings::first();
	        $query = Campaigns::query();
	        
	        $query = $query->select('users.id as user_id', 'users.name', 'users.last_name', 'users.avatar', 'users.company_name', 'campaigns.id as campaign_id', 'campaigns.small_image','campaigns.large_image', 'campaigns.title', 'campaigns.description', 'campaigns.goal', 'campaigns.featured', 'campaigns.deadline');
	        if($request->input('networkId') != ''){
	            $query = $query->whereIn('users.network_type_id', $request->input('networkId'));
	           // $whereStatement['users.network_type_id'] = $request->input('networkId');
	        }
	        if($request->input('categoryId') != ''){
	            $query = $query->whereIn('campaigns.categories_id', $request->input('categoryId'));
	           // $whereStatement['campaigns.categories_id'] = $request->input('categoryId');
	        }
	        
	        if($request->input('countryId') != ''){
	            $query = $query->whereIn('users.countries_id', $request->input('countryId'));
	           // $whereStatement['users.countries_id'] = $request->input('countryId');
	        }
	        if($request->input('profileId') != ''){
	            $id = (int)$request->input('profileId')-89066;
	            $query = $query->where('users.id', $id);
	           // $whereStatement['users.id'] = $id;
	        }
	        
	        $query = $query->join('users', 'users.id', '=', 'campaigns.user_id')
                ->join('categories', 'categories.id', '=', 'campaigns.categories_id')
                ->join('networks', 'networks.id', '=', 'users.network_type_id')->get();
            if(count($query) > 0){
                foreach($query as $res){
                    //dd($r);
                    // dd($res->donations()->sum('donation'));
                    if( str_slug( $res->title ) == '' ) {
                		$slugUrl  = '';
                	} else {
                		$slugUrl  = '/'.str_slug( $res->title );
                	}
                	$res->url = url('campaign',$res->campaign_id).$slugUrl;
                	$percentage = round($res->donations()->sum('donation') / $res->goal * 100);
        
                	if( $percentage > 100 ) {
                		$res->percentage = 100;
                	} else {
                		$res->percentage = $percentage;
                	}
                	$res->description = str_limit(strip_tags($res->description),80,'...');
                	$res->donations = $settings->currency_symbol.number_format($res->donations()->sum('donation'));
                }
                echo json_encode($query);
                exit;
            }
            else {
                $error = ['error' => trans('misc.no_results_found')];
                echo json_encode($error);
                exit;
            }
	    }
	    
	    $data['categories'] = Categories::where('mode','on')->orderBy('name')->get();
    	$data['networks'] = Networks::orderBy('network_type')->get();
    	$data['countries'] = Countries::orderBy('country_name')->get();
    	return view('default.categories')->withData($data);
	}
	
	public function category($slug) {
		
		$settings = AdminSettings::first();
			
		 $category = Categories::where('slug','=',$slug)->where('mode','on')->firstOrFail();
	  	 $data       = Campaigns::where('status', 'active')->where('categories_id',$category->id)->orderBy('id','DESC')->paginate($settings->result_request);
				
		return view('default.category', ['data' => $data, 'category' => $category]);
		
	}// End Method
}
