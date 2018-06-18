<?php 

	$settings = App\Models\AdminSettings::first();
	
	$percentage = round($response->donations()->sum('donation') / $response->goal * 100);
	
	if( $percentage > 100 ) {
		$percentage = 100;
	} else {
		$percentage = $percentage;
	}
	
	// All Donations
	$donations = $response->donations()->orderBy('id','desc')->paginate(2);
	
	// Updates
	$updates = $response->updates()->orderBy('id','desc')->paginate(1);
	
	if( str_slug( $response->title ) == '' ) {
		$slug_url  = '';
	} else {
		$slug_url  = '/'.str_slug( $response->title );
	}

 ?>
 @extends('app')

@section('title'){{ trans('misc.donate').' - '.$response->title.' - ' }}@endsection

@section('css')
<link href="{{ asset('public/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="jumbotron md header-donation jumbotron_set">
      <div class="container wrap-jumbotron position-relative">
      	<h2 class="title-site">{{ trans('misc.donate') }}</h2>
      	<p class="subtitle-site"><strong>{{$response->title}}</strong></p>
      </div>
    </div>
    
<div class="container margin-bottom-40 padding-top-40">
	
<!-- Col MD -->
<div class="col-md-8 margin-bottom-20"> 
	
	   <!-- form start -->
    <form method="POST" action="{{ url('donate',$response->id) }}" enctype="multipart/form-data" id="formDonation">
    	
    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
    	<input type="hidden" name="_id" value="{{ $response->id }}">
			
			<div class="form-group">
				    <label>{{ trans('misc.enter_your_donation') }}</label>
				    <div class="input-group has-success">
				      <div class="input-group-addon addon-dollar">{{$settings->currency_symbol}}</div>
				      <input type="number" min="{{$settings->min_donation_amount}}"  autocomplete="off" id="onlyNumber" class="form-control input-lg" name="amount" value="{{ old('donation') }}" placeholder="{{trans('misc.minimum_amount')}} {{$settings->currency_symbol.$settings->min_donation_amount}} {{$settings->currency_code}}">
				    </div>
				  </div>
				  
				  
                 <!-- Start -->
                    <div class="form-group">
                      <label>{{ trans('auth.full_name') }}</label>
                        <input type="text"  value="@if( Auth::check() ){{Auth::user()->name}}@endif" name="full_name" class="form-control input-lg" placeholder="{{ trans('misc.first_name_and_last_name') }}">
                    </div><!-- /. End-->
                    
                    <!-- Start -->
                    <div class="form-group">
                      <label>{{ trans('auth.email') }}</label>
                        <input type="text"  value="@if( Auth::check() ){{Auth::user()->email}}@endif" name="email" class="form-control input-lg" placeholder="{{ trans('auth.email') }}">
                    </div><!-- /. End-->
              
              <div class="row form-group">    
                  <!-- Start -->
                    <div class="col-xs-6">
                      <label>{{ trans('misc.country') }}</label>
                      	<select name="country" class="form-control input-lg" >
                      		<option value="">{{trans('misc.select_one')}}</option>
                      	@foreach(  App\Models\Countries::orderBy('country_name')->get() as $country ) 	
                            <option @if( Auth::check() && Auth::user()->countries_id == $country->id ) selected="selected" @endif value="{{$country->country_name}}">{{ $country->country_name }}</option>
						@endforeach
                          </select>
                  </div><!-- /. End-->
                  
                  <!-- Start -->
                    <div class="col-xs-6">
                      <label>{{ trans('misc.postal_code') }}</label>
                        <input type="text"  value="{{ old('postal_code') }}" name="postal_code" class="form-control input-lg" placeholder="{{ trans('misc.postal_code') }}">
                    </div><!-- /. End-->
                    
              </div><!-- row form-control -->
                  
                  <!-- Start -->
                    <div class="form-group">
                        <input type="text" value="{{ old('comment') }}" name="comment" class="form-control input-lg" placeholder="{{ trans('misc.leave_comment') }}">
                    </div><!-- /. End-->
                    
        <div class="form-group checkbox icheck">
				<label class="margin-zero">
					<input class="no-show" name="anonymous" type="checkbox" value="1">
					<span class="margin-lft5 keep-login-title">{{ trans('misc.anonymous_donation') }}</span>
			</label>
		</div>                    
                    <!-- Alert -->
                    <div class="alert alert-danger display-none" id="errorDonation">
							<ul class="list-unstyled" id="showErrorsDonation"></ul>
						</div><!-- Alert -->
                
                  <div class="box-footer text-center">
                  	<hr />
                    <button type="submit" id="buttonDonation" class="btn-padding-custom btn btn-lg btn-main custom-rounded">{{ trans('misc.donate') }}</button>
                    <div class="btn-block text-center margin-top-20">
			           		<a href="{{url('campaign',$response->id)}}" class="text-muted">
			           		<i class="fa fa-long-arrow-left"></i>	{{trans('auth.back')}}</a>
			           </div>
                  </div><!-- /.box-footer -->
						
                </form>
		       
 </div><!-- /COL MD -->
 
 <div class="col-md-4">
	
	<!-- Start Panel -->
	<div class="panel panel-default">
		<div class="panel-body">
			<h3 class="btn-block margin-zero" style="line-height: inherit;">
				<strong class="font-default">{{$settings->currency_symbol.number_format($response->donations()->sum('donation'))}}</strong> 
				<small>{{trans('misc.of')}} {{$settings->currency_symbol.number_format($response->goal)}} {{strtolower(trans('misc.goal'))}}</small>
				</h3>
				
				<span class="progress margin-top-10 margin-bottom-10">
					<span class="percentage" style="width: {{$percentage }}%" aria-valuemin="0" aria-valuemax="100" role="progressbar"></span>
				</span>
				
				<small class="btn-block margin-bottom-10 text-muted">
					{{$percentage }}% {{trans('misc.raised')}} {{trans('misc.by')}} {{number_format($response->donations()->count())}} {{trans_choice('misc.donation_plural',$response->donations()->count())}}
				</small>						
		</div>
	</div><!-- End Panel -->
		
	<div class="panel panel-default">
		<div class="panel-body">
			<img class="img-responsive img-rounded" style="display: inline-block;" src="{{url('public/campaigns/small',$response->small_image)}}" />
			</div>
		</div>	
	
	@if( $settings->payment_gateway == 'Paypal' )	
		<div class="panel panel-default">
		<div class="panel-body">
			<img class="img-responsive img-rounded" style="display: inline-block;" src="{{url('public/img/payment-1.png')}}" />
			</div>
		</div>
		@endif
	
<!-- Start Panel -->
 	<div class="panel panel-default">
	  <div class="panel-body">
	    <div class="media none-overflow">
	    	
	    	<span class="btn-block text-center margin-bottom-10 text-muted"><strong>{{trans('misc.organizer')}}</strong></span>
	    	
			  <div class="media-center margin-bottom-5">
			      <img class="img-circle center-block" src="{{url('public/avatar/',$response->user()->avatar)}}" width="60" height="60" >
			  </div>
			  
			  <div class="media-body text-center">
			  	
			    	<h4 class="media-heading">
			    		{{$response->user()->name}}
			    	
			    	@if( Auth::guest()  || Auth::check() && Auth::user()->id != $response->user()->id )				    		
			    		<a href="#" title="{{trans('misc.contact_organizer')}}" data-toggle="modal" data-target="#sendEmail">
			    				<i class="fa fa-envelope myicon-right"></i>
			    		</a>
			    		@endif
			    		</h4>
			    		
			    <small class="media-heading text-muted btn-block margin-zero">{{trans('misc.created')}} {{ date('M d, Y', strtotime($response->date) ) }}</small>
			    @if( $response->location != '' )
			    <small class="media-heading text-muted btn-block"><i class="fa fa-map-marker myicon-right"></i> {{$response->location}}</small>
			    @endif
			  </div>
			</div>
	  </div>
	</div><!-- End Panel -->
			
<div class="modal fade" id="sendEmail" tabindex="-1" role="dialog" aria-hidden="true">
     		<div class="modal-dialog">
     			<div class="modal-content"> 
     				<div class="modal-header headerModal">
				        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				        
				        <h4 class="modal-title text-center" id="myModalLabel">
				        	{{ trans('misc.contact_organizer') }}
				        	</h4>
				     </div><!-- Modal header -->
				     
				      <div class="modal-body listWrap text-center center-block modalForm">
				    
				    <!-- form start -->
			    <form method="POST" class="margin-bottom-15" action="{{ url('contact/organizer') }}" enctype="multipart/form-data" id="formContactOrganizer">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
			    	<input type="hidden" name="id" value="{{ $response->user()->id }}">  	
				    
				    <!-- Start -->
                    <div class="form-group">
                    	<input type="text"  name="name" class="form-control" placeholder="{{ trans('users.name') }}">
                    </div><!-- /. End-->
                    
                    <!-- Start -->
                    <div class="form-group">
                    	<input type="text"  name="email" class="form-control" placeholder="{{ trans('auth.email') }}">
                    </div><!-- /. End-->
                    
                    <!-- Start -->
                    <div class="form-group">
                    	<textarea name="message" rows="4" class="form-control" placeholder="{{ trans('misc.message') }}"></textarea>
                    </div><!-- /. End-->
                   						
                    <!-- Alert -->
                    <div class="alert alert-danger display-none" id="dangerAlert">
							<ul class="list-unstyled text-left" id="showErrors"></ul>
						</div><!-- Alert -->

                  
                   <button type="submit" class="btn btn-lg btn-main custom-rounded" id="buttonFormSubmit">{{ trans('misc.send_message') }}</button>
                   
                    </form>
                    
                                        <!-- Alert -->
                    <div class="alert alert-success display-none" id="successAlert">
							<ul class="list-unstyled" id="showSuccess"></ul>
						</div><!-- Alert -->


				      </div><!-- Modal body -->
     				</div><!-- Modal content -->
     			</div><!-- Modal dialog -->
     		</div><!-- Modal -->
     			
 </div><!-- /COL MD -->
 
 </div><!-- container wrap-ui -->
 
 <?php /*
 <form id="form_pp" name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post"  style="display:none">
    <input type="hidden" name="cmd" value="_donations">
    <input type="hidden" name="return" value="'.$urlSuccess.'">
    <input type="hidden" name="cancel_return"   value="'.$urlCancel.'">
    <input type="hidden" name="notify_url" value="'.$urlPaypalIPN.'">
    <input type="hidden" name="currency_code" value="'.$this->settings->currency_code.'">
    <input type="hidden" name="amount" id="amount" value="'.$this->request->amount.'">
    <input type="hidden" name="custom" value="id='.$this->request->_id.'&fn='.$this->request->full_name.'&cc='.$this->request->country.'&pc='.$this->request->postal_code.'&cm='.$this->request->comment.'">
    <input type="hidden" name="item_name" value="'.trans('misc.donation_for').' '.$response->title.'">
    <input type="hidden" name="business" value="'.$this->settings->paypal_email.'">
    <input type="submit">
</form>
  */
  ?> 

@endsection

@section('javascript')
<script src="https://checkout.stripe.com/checkout.js"></script>
<script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}"></script>

<script type="text/javascript">
/*function onlyNumber(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode < 48 || charCode > 57))
        return false;
    return true;
}*/

$('#onlyNumber').focus();

$(document).ready(function() {
	
	$("#onlyNumber").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    
    $('input').iCheck({
	  	checkboxClass: 'icheckbox_square-red',
    	radioClass: 'iradio_square-red',
	    increaseArea: '20%' // optional
	  });
	  
});
</script>
@endsection
