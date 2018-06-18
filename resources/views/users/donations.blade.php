<?php 
// ** Data User logged ** //
     $user = Auth::user();
	 $settings = App\Models\AdminSettings::first();
	 
	 $data = App\Models\Donations::leftJoin('campaigns', function($join) {
      $join->on('donations.campaigns_id', '=', 'campaigns.id');
    })
    ->where('campaigns.user_id',Auth::user()->id)
	->select('donations.*')
	->addSelect('campaigns.title')
	->orderBy('donations.id','DESC')
    ->paginate(20);
	 	 
	  ?>
@extends('app')

@section('title') {{ trans('misc.donations') }} - @endsection

@section('content') 
<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
      <div class="container wrap-jumbotron position-relative">
        <h2 class="title-site">{{ trans('misc.donations') }}</h2>
      </div>
    </div>

<div class="container margin-bottom-40">
	
		<!-- Col MD -->
		<div class="col-md-8 margin-bottom-20">

<div class="table-responsive">
   <table class="table table-striped"> 
   	
   	@if( $data->total() !=  0 && $data->count() != 0 )
   	<thead> 
   		<tr>
   		 <th class="active">ID</th>
          <th class="active">{{ trans('auth.full_name') }}</th>
          <th class="active">{{ trans_choice('misc.campaigns_plural', 1) }}</th>
          <th class="active">{{ trans('auth.email') }}</th>
          <th class="active">{{ trans('misc.donation') }}</th>
          <th class="active">{{ trans('admin.date') }}</th>
          </tr>
   		  </thead> 
   		  
   		  <tbody> 
   		      @foreach( $data as $donation )
                    <tr>
                      <td>{{ $donation->id }}</td>
                      <td>{{ $donation->fullname }}</td>
                      <td><a href="{{url('campaign',$donation->campaigns_id)}}" target="_blank">{{ str_limit($donation->title, 10, '...') }} <i class="fa fa-external-link-square"></i></a></td>
                      <td>{{ $donation->email }}</td>
                      <td>{{ $settings->currency_symbol.number_format($donation->donation) }}</td>
                      <td>{{ date('d M, y', strtotime($donation->date)) }}</td>
                    </tr><!-- /.TR -->
                    @endforeach
                    
                    @else
                    <hr />
                    	<h3 class="text-center no-found">{{ trans('misc.no_results_found') }}</h3>

                    @endif   		  		 		
   		  		 		</tbody> 
   		  		 		</table>
   		  		 		</div>
   		  		 	
   		  		 	@if( $data->lastPage() > 1 )	
   		  		 		{{ $data->links() }}
   		  		 	@endif
   		  		 	
		</div><!-- /COL MD -->
		
		<div class="col-md-4">
			@include('users.navbar-edit')
		</div>
		
 </div><!-- container -->
 
 <!-- container wrap-ui -->
@endsection

