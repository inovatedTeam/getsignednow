<?php 
// ** Data User logged ** //
     $user = Auth::user();
	 $settings = App\Models\AdminSettings::first(); 	 	 
	  ?>
@extends('app')

@section('title') {{ trans('misc.campaigns') }} - @endsection

@section('content') 
<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
      <div class="container wrap-jumbotron position-relative">
        <h2 class="title-site">{{ trans('misc.campaigns') }}</h2>
      </div>
    </div>

<div class="container margin-bottom-40">
	
		<!-- Col MD -->
		<div class="col-md-8 margin-bottom-20">
			
			@if (session('notification'))
			<div class="alert alert-warning btn-sm alert-fonts" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		{{ session('notification') }} <a href="{{url('account/withdrawals/configure')}}">{{ trans('misc.configure') }} <i class="fa fa-long-arrow-right"></i></a>
            		</div>
            	@endif

@if(  $settings->payment_gateway == 'Paypal' && $data->total() !=  0 && $data->count() != 0 )
<h6>* {{trans('misc.fund_detail_alert')}} {{$settings->fee_donation}}% + (PayPal {{config('commissions.paypal_fee')}}% + {{config('commissions.paypal_cents')}}) = {{$settings->fee_donation + config('commissions.paypal_fee')}}% + {{config('commissions.paypal_cents')}}</h6>
@endif

@if(  $settings->payment_gateway == 'Stripe' && $data->total() !=  0 && $data->count() != 0 )
<h6>* {{trans('misc.fund_detail_alert')}} {{$settings->fee_donation}}% + (Stripe {{config('commissions.stripe_fee')}}% + {{config('commissions.stripe_cents')}}) = {{$settings->fee_donation + config('commissions.stripe_fee') }}% + {{config('commissions.stripe_cents')}}</h6>
@endif

<div class="table-responsive">
   <table class="table table-striped"> 
   	
   	@if( $data->total() !=  0 && $data->count() != 0 )
   	<thead> 
   		<tr>
   		 <th class="active">ID</th>
   		  <th class="active">{{ trans('misc.title') }}</th>
          <th class="active">{{ trans('misc.goal') }}</th>
          <th class="active">{{ trans('misc.funds_raised') }}</th>
          <th class="active">{{ trans('admin.status') }}</th>
          <th class="active">{{ trans('admin.date') }}</th>
          <th class="active">{{ trans('misc.date_deadline') }}</th>
          <th class="active">{{ trans('admin.actions') }}</th> 
          </tr>
   		  </thead> 
   		  
   		  <tbody> 

   		      @foreach( $data as $campaign )
   		      
   <?php
   		      
      $amount = $campaign->donations()->sum('donation');
	
	if(  $settings->payment_gateway == 'Paypal' ) {
		$fee       = config('commissions.paypal_fee');
		$cents   =  config('commissions.paypal_cents');
	}  elseif(  $settings->payment_gateway == 'Stripe' ) {
		$fee      = config('commissions.stripe_fee');
		$cents   =  config('commissions.stripe_cents');
	}
	  
	  
	  $_funds  = $amount - (  $amount * $fee/100 ) - $cents; // Fees Payment processor
	  $funds    = $_funds - (  $_funds * $settings->fee_donation/100  ); // Fee Site
	  
	  if( $amount == 0 ) {
	  	$funds = 0;
	  } else {
	  	$funds = number_format( $funds,2 );
	  }
	 
	    		      
   	?>
   		      
                    <tr>
                      <td>{{ $campaign->id }}</td>
                      <td>
                     @if( $campaign->status == 'active' ) 	
                      	<a title="{{$campaign->title}}" href="{{ url('campaign',$campaign->id) }}" target="_blank">
                      		{{ str_limit($campaign->title,15,'...') }} <i class="fa fa-external-link-square"></i>
                      		</a>
                      		@else
                      		<span title="{{$campaign->title}}" class="text-muted">
                      		{{ str_limit($campaign->title,15,'...') }}
                      		</span>
                      		@endif
                      		
                      	</td>
                      <td>{{ $settings->currency_symbol.number_format($campaign->goal) }}</td>
                      <td>{{ $settings->currency_symbol.$funds }}</td>
                      <td>
                      	
                      	@if( $campaign->status == 'active' && $campaign->finalized == 0 )
                      	<span class="label label-success">{{trans('misc.active')}}</span>
                      	
                      	@elseif( $campaign->status == 'pending' && $campaign->finalized == 0 )
                      	<span class="label label-warning">{{trans('admin.pending')}}</span>
                      	
                      	@else
                      	<span class="label label-default">{{trans('misc.finalized')}}</span>
                      	@endif
                      	
                      </td>
                      <td>{{ date('d M, y', strtotime($campaign->date)) }}</td>
                      <td>
                      	@if( $campaign->deadline != '' )
                      	{{ date('d M, y', strtotime($campaign->deadline)) }}
                      	@else
                      	 -
                      	@endif
                      	</td>
                      <td> 
                     
                     @if( $campaign->finalized == 0 )
                      	<a href="{{ url('edit/campaign',$campaign->id) }}" class="btn btn-success btn-xs">
                      		{{ trans('admin.edit') }}
                      	</a> 
                      	@else
                      	
                      	@if( isset( $campaign->withdrawals()->id ) && $campaign->withdrawals()->status == 'pending'  )
                      		<span class="label label-warning">{{trans('misc.pending_to_pay')}}</span>
                      		
                      		@elseif( isset( $campaign->withdrawals()->id ) && $campaign->withdrawals()->status == 'paid'  )
                      		
                      		<span class="label label-success">{{trans('misc.paid')}}</span>
                      		
                      		@else
                     
                     @if( $funds != 0 )
                     
                     {!! Form::open([
			            'method' => 'POST',
			            'url' => "campaign/withdrawal/$campaign->id",
			            'class' => 'displayInline'
				        ]) !!}
				        				        
	            	{!! Form::submit(trans('misc.make_withdrawal'), ['class' => 'btn btn-success btn-xs padding-btn']) !!}
	        	{!! Form::close() !!}

                      	@else
                      	{{ trans('misc.finalized') }}
                      	@endif
                      		
                      	@endif
                      	                      	
                      	@endif
                      	
                      	</td>
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

