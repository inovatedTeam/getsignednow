<?php $settings = App\Models\AdminSettings::first(); ?>
@extends('app')

@section('title')
{{ trans('auth.sign_up') }} -
@stop

@section('content')

<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
      <div class="container wrap-jumbotron position-relative">
        <h1 class="title-site">{{ trans('auth.sign_up') }}</h1>
        <p class="subtitle-site"><strong>{{$settings->title}}</strong></p>
      </div>
    </div>

<div class="container margin-bottom-40">
	
	<div class="row">
<!-- Col MD -->
<div class="col-md-12">	
	
	<h2 class="text-center position-relative">{{ trans('auth.sign_up') }}</h2>
	
	<div class="login-form">
		
		@if (session('notification'))
		
                        <div class="alert alert-success text-center">

                                <div class="btn-block text-center margin-bottom-10">
                                        <i class="glyphicon glyphicon-ok ico_success_cicle"></i>
                                        </div>

                                {{ session('notification') }}
                        </div>
                @endif
					
		@include('errors.errors-forms')
	        @if (count($errors) > 0)
                    <?php $err = $errors->default->toArray();?> 
                @endif	
          	<form action="{{ url('register') }}" method="post" name="form" id="signup_form">
            
            <input type="hidden" name="_token" value="{{ csrf_token() }}">          
            
            <!-- FORM GROUP -->
            <div class="form-group has-feedback">
                <select name="network_type_id" class="form-control {{((isset($err['network_type_id'])) ? " form-error" : "")}}" >
                    <option value="">{{trans('misc.select_your_network')}}</option>
                    @foreach(  App\Models\Networks::get() as $network_type ) 	
                        <option value="{{$network_type->id}}">{{ $network_type->network_type }}</option>
					@endforeach
                </select>
            </div>
            
            <!-- ./FORM GROUP -->
            
            <!-- FORM GROUP -->
            <div class="form-group has-feedback">
              <input type="text" class="form-control login-field custom-rounded {{((isset($err['name'])) ? " form-error" : "")}}" value="{{ old('name') }}" name="name" placeholder="{{ trans('users.name') }}" title="{{ trans('users.name') }}" autocomplete="off">
              <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div><!-- ./FORM GROUP -->
            
            <!-- FORM GROUP -->
            <div class="form-group has-feedback">
              <input type="text" class="form-control login-field custom-rounded {{((isset($err['last_name'])) ? " form-error" : "")}}" value="{{ old('last_name') }}" name="last_name" placeholder="{{ trans('users.last_name') }}" title="{{ trans('users.last_name') }}" autocomplete="off">
              <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div><!-- ./FORM GROUP -->
            
            <!-- FORM GROUP -->
            <div class="form-group has-feedback">
              <input type="text" class="form-control login-field custom-rounded" value="{{ old('company_name') }}" name="company_name" placeholder="{{ trans('users.company_name') }}" title="{{ trans('users.company_name') }}" autocomplete="off">
              <span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
            </div><!-- ./FORM GROUP -->
            
            <!-- FORM GROUP -->
            <div class="form-group has-feedback">
              <input type="text" class="form-control login-field custom-rounded {{((isset($err['mobile_number'])) ? " form-error" : "")}}" value="{{ old('mobile_number') }}" name="mobile_number" placeholder="{{ trans('users.mobile_number') }}" title="{{ trans('users.mobile_number') }}" autocomplete="off">
              <span class="glyphicon glyphicon-phone form-control-feedback"></span>
            </div><!-- ./FORM GROUP -->
            
             <!-- FORM GROUP -->
            <div class="form-group has-feedback">
              <input type="text" class="form-control login-field custom-rounded {{((isset($err['email'])) ? " form-error" : "")}}" value="{{ old('email') }}" name="email" placeholder="{{ trans('auth.email') }}" title="{{ trans('auth.email') }}" autocomplete="off">
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div><!-- ./FORM GROUP -->
            
            <!-- FORM GROUP -->
            <div class="form-group has-feedback">
              <input type="text" class="form-control login-field custom-rounded {{((isset($err['state'])) ? " form-error" : "")}}" value="{{ old('state') }}" name="state" placeholder="{{ trans('users.state') }}" title="{{ trans('users.state') }}" autocomplete="off">
              <span class="glyphicon glyphicon-phone form-control-feedback"></span>
            </div><!-- ./FORM GROUP -->
            
            <!-- FORM GROUP -->
         <div class="form-group has-feedback">
              <select name="countries_id" class="form-control {{((isset($err['countries_id'])) ? " form-error" : "")}}" >
                      		<option value="">{{trans('misc.select_your_country')}}</option>
                      	@foreach(  App\Models\Countries::orderBy('country_name')->get() as $country ) 	
                            <option value="{{$country->id}}">{{ $country->country_name }}</option>
						@endforeach
                          </select>
         </div><!-- ./FORM GROUP -->
         
         <!-- FORM GROUP -->
         <div class="form-group has-feedback">
              <input type="password" class="form-control login-field custom-rounded {{((isset($err['password'])) ? " form-error" : "")}}" name="password" placeholder="{{ trans('auth.password') }}" title="{{ trans('auth.password') }}" autocomplete="off">
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
         </div><!-- ./FORM GROUP -->
         
         <div class="form-group has-feedback">
			<input type="password" class="form-control {{((isset($err['password'])) ? " form-error" : "")}}" name="password_confirmation" placeholder="{{ trans('auth.confirm_password') }}" title="{{ trans('auth.confirm_password') }}" autocomplete="off">
			<span class="glyphicon glyphicon-log-in form-control-feedback"></span>
		</div>
		
		
		@if( $settings->captcha == 'on' )    
            <div class="form-group has-feedback">
              <input type="text" class="form-control login-field" name="captcha" id="lcaptcha" placeholder="" title="">
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            
              <div class="alert alert-danger btn-sm margin-top-alert" id="errorCaptcha" role="alert" style="display: none;">
            		<strong><i class="glyphicon glyphicon-alert myicon-right"></i> {{Lang::get('auth.error_captcha')}}</strong>
            	</div>
            </div>
            @endif
         
           <button type="submit" id="buttonSubmit" class="btn btn-block btn-lg btn-main custom-rounded">{{ trans('auth.sign_up') }}</button>
          </form>
     </div><!-- Login Form -->
		
 </div><!-- /COL MD -->
 
</div><!-- ROW -->
 
 </div><!-- row -->
 
 <!-- container wrap-ui -->

@endsection

@section('javascript')
	
	<script type="text/javascript">

 @if( $settings->captcha == 'on' )     
/*
 *  ==============================================  Captcha  ============================== * /
 */
   var captcha_a = Math.ceil( Math.random() * 5 );
   var captcha_b = Math.ceil( Math.random() * 5 );
   var captcha_c = Math.ceil( Math.random() * 5 );
   var captcha_e = ( captcha_a + captcha_b ) - captcha_c;
  
function generate_captcha( id ) {
	var id = ( id ) ? id : 'lcaptcha';
	$("#" + id ).html( captcha_a + " + " + captcha_b + " - " + captcha_c + " = ").attr({'placeholder' : captcha_a + " + " + captcha_b + " - " + captcha_c, title: 'Captcha = '+captcha_a + " + " + captcha_b + " - " + captcha_c });
}
$("input").on("click", function(){
    if($(this).hasClass('form-error')){
        $(this).removeClass('form-error');
    }
});
$("select").on("change", function(){
    if($(this).hasClass('form-error')){
        $(this).removeClass('form-error');
    }
});
$("input").attr('autocomplete','off');
generate_captcha('lcaptcha');

$(document).on('click','#buttonSubmit', function(e){
   	e.preventDefault();
   	var captcha        = $("#lcaptcha").val();
    	if( captcha != captcha_e ){
    		$('.wrap-loader').hide();
				var error = true;
		        $("#errorCaptcha").fadeIn(500);
		        $('#lcaptcha').focus();
		        
		        return false;
		      } else {
		      	$(this).css('display','none');
    			$('.auth-social').css('display','none');
    			$('<div class="btn-block text-center"><i class="fa fa-cog fa-spin fa-3x fa-fw fa-loader"></i></div>').insertAfter('#signup_form');
   				$('#signup_form').submit();
		      }
    });
    
    @else
    	
	$('#buttonSubmit').click(function(){
    	$(this).css('display','none');
    	$('.auth-social').css('display','none');
    	$('<div class="btn-block text-center"><i class="fa fa-cog fa-spin fa-3x fa-fw fa-loader"></i></div>').insertAfter('#signup_form');
    });
    
    @endif
    
    @if (count($errors) > 0)
    	scrollElement('#dangerAlert');
    @endif
    
    @if (session('notification'))
    	$('#signup_form, #dangerAlert').remove();
    @endif

</script>


@endsection
