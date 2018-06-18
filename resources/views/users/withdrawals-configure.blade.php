<?php 
// ** Data User logged ** //
     $user = Auth::user();
	 $settings = App\Models\AdminSettings::first();	 	 	 
	  ?>
@extends('app')

@section('title') {{ trans('misc.withdrawals') }} {{ trans('misc.configure') }} - @endsection

@section('content') 
<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
      <div class="container wrap-jumbotron position-relative">
        <h2 class="title-site">{{ trans('misc.withdrawals') }} {{ trans('misc.configure') }} </h2>
      </div>
    </div>

<div class="container margin-bottom-40">
	
		<!-- Col MD -->
<div class="col-md-8 margin-bottom-20">
	
	@if (session('error'))
			<div class="alert alert-danger btn-sm alert-fonts" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		{{ session('error') }}
            		</div>
            	@endif
            	
            	@if (session('success'))
			<div class="alert alert-success btn-sm alert-fonts" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		{{ session('success') }}
            		</div>
            	@endif
            	
           @include('errors.errors-forms')

<h5>{{ trans('misc.select_method_payment') }} - <strong>{{ trans('misc.default_withdrawal') }}</strong>: @if( Auth::user()->payment_gateway == '' ) {{trans('misc.unconfigured')}} @else {{Auth::user()->payment_gateway}} @endif</h5>

	<a class="btn btn-primary pp btn-block" role="button" data-toggle="collapse" href="#paypal" aria-expanded="false" aria-controls="paypal">
	  <i class="fa fa-paypal myicon-right"></i> Paypal
	</a>
	
	<!-- collapse -->
	<div class="collapse margin-top-15" id="paypal">
	<form method="post" action="{{url('withdrawals/configure/paypal')}}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
	  <div class="form-group">
	    <input type="text" class="form-control" value="{{Auth::user()->paypal_account}}" id="email_paypal" name="email_paypal" placeholder="Email Paypal">
	  </div>
	  
	  <div class="form-group">
	    <input type="text" class="form-control" name="email_paypal_confirmation" placeholder="{{trans('misc.confirm_email')}}">
	  </div>
	  
	  <button type="submit" class="btn btn-success">{{ trans('misc.submit') }}</button>
	</form>
	</div><!-- collapse -->

	<button class="btn btn-default bank margin-top-10 btn-block" type="button" data-toggle="collapse" data-target="#bank" aria-expanded="false" aria-controls="bank">
	  <i class="fa fa-university myicon-right"></i> {{ trans('misc.bank_transfer') }} 
	</button>

   

	<!-- collapse -->
	<div class="collapse margin-top-15" id="bank">
	<form method="post" action="{{url('withdrawals/configure/bank')}}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
	  <div class="form-group">
	    <textarea class="form-control" rows="5" id="bank_form" name="bank" placeholder="{{ trans('misc.bank_details') }}">{{Auth::user()->bank}}</textarea>
	  </div>
	  <button type="submit" class="btn btn-success">{{ trans('misc.submit') }}</button>
	</form>
	</div><!-- collapse -->
	  		 			
</div><!-- /COL MD -->
		
		<div class="col-md-4">
			@include('users.navbar-edit')
		</div>
		
 </div><!-- container -->
 
 <!-- container wrap-ui -->
@endsection

@section('javascript')

<script type="text/javascript">

	$(document).on('click','.pp',function(s){
		$('#bank').collapse('hide');
		$('#email_paypal').focus();
	});
	
	$(document).on('click','.bank',function(s){
		$('#paypal').collapse('hide');
		$('#bank_form').focus();
	});
	
</script>
@endsection