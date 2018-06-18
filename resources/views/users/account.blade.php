<?php 
// ** Data User logged ** //
     $user = Auth::user();     
	  ?>
@extends('app')

@section('title') {{ trans('users.account_settings') }} - @endsection

@section('content') 
<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
      <div class="container wrap-jumbotron position-relative">
        <h2 class="title-site">{{ trans('users.account_settings') }}</h2>
      </div>
    </div>

<div class="container margin-bottom-40">
	
			<!-- Col MD -->
		<div class="col-md-8 margin-bottom-20">
	
			@if (session('notification'))
			<div class="alert alert-success btn-sm alert-fonts" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		{{ session('notification') }}
            		</div>
            	@endif
            	
			@include('errors.errors-forms')
			@if (count($errors) > 0)
                            <?php $err = $errors->default->toArray();?> 
                        @endif	
			
		
		<!-- *********** AVATAR ************* -->
		
		<form action="{{url('upload/avatar')}}" method="POST" id="formAvatar" accept-charset="UTF-8" enctype="multipart/form-data">
    		<input type="hidden" name="_token" value="{{ csrf_token() }}">
    		
    		<div class="text-center">
    			<img src="{{ asset('public/avatar').'/'.Auth::user()->avatar }}" alt="User" width="100" height="100" class="img-rounded avatarUser"  />
    		</div>
    		
    		<div class="text-center">
    			<button type="button" class="btn btn-default btn-border btn-sm" id="avatar_file" style="margin-top: 10px;">
	    		<i class="icon-camera myicon-right"></i> {{ trans('misc.change_avatar') }}
	    		</button>
	    		<input type="file" name="photo" id="uploadAvatar" accept="image/*" style="visibility: hidden;">
    		</div>
			
		    </form>
			<!-- *********** AVATAR ************* -->
		

			
		<!-- ***** FORM ***** -->	
       <form action="{{ url('account') }}" method="post" name="form">
          		
          	<input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('misc.network_misc') }}</label>
            	<select name="network_type_id" class="form-control {{((isset($err['network_type_id'])) ? " form-error" : "")}}" >
                      		<option value="" disabled>{{trans('misc.network_misc')}}</option>
                      	@foreach(  App\Models\Networks::get() as $network ) 	
                            <option @if( $user->network_type_id == $network->id ) selected="selected" @endif value="{{$network->id}}">{{ $network->network_type }}</option>
						@endforeach
                          </select>
            	    </div>
            <!-- ***** Form Group ***** -->    
                
            <!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('misc.first_name_misc') }}</label>
              <input type="text" class="form-control login-field custom-rounded {{((isset($err['first_name'])) ? " form-error" : "")}}" value="{{ e( $user->name ) }}" name="first_name" placeholder="{{ trans('misc.first_name_misc') }}" title="{{ trans('misc.first_name_misc') }}" autocomplete="off">
             </div><!-- ***** Form Group ***** -->
             
             <!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('misc.last_name_misc') }}</label>
              <input type="text" class="form-control login-field custom-rounded {{((isset($err['last_name'])) ? " form-error" : "")}}" value="{{ e( $user->last_name ) }}" name="last_name" placeholder="{{ trans('misc.last_name_misc') }}" title="{{ trans('misc.last_name_misc') }}" autocomplete="off">
             </div><!-- ***** Form Group ***** -->
		
             <!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('misc.company_name_misc') }}</label>
              <input type="text" class="form-control login-field custom-rounded" value="{{ e( $user->company_name ) }}" name="company_name" placeholder="{{ trans('misc.company_name_misc') }}" title="{{ trans('misc.company_name_misc') }}" autocomplete="off">
             </div><!-- ***** Form Group ***** -->
            <!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('misc.mobile_number_misc') }}</label>
              <input type="text" class="form-control login-field custom-rounded {{((isset($err['mobile_number'])) ? " form-error" : "")}}" value="{{ e( $user->mobile_number ) }}" name="mobile_number" placeholder="{{ trans('misc.mobile_number_misc') }}" title="{{ trans('misc.mobile_number_misc') }}" autocomplete="off">
             </div><!-- ***** Form Group ***** --> 
            <!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('auth.email') }}</label>
              <input type="email" class="form-control login-field custom-rounded {{((isset($err['email'])) ? " form-error" : "")}}" value="{{$user->email}}" name="email" placeholder="{{ trans('auth.email') }}" title="{{ trans('auth.email') }}" autocomplete="off">
            </div><!-- ***** Form Group ***** -->
            <!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('misc.state_misc') }}</label>
              <input type="text" class="form-control login-field custom-rounded {{((isset($err['state'])) ? " form-error" : "")}}" value="{{ e( $user->state ) }}" name="state" placeholder="{{ trans('misc.state_misc') }}" title="{{ trans('misc.state_misc') }}" autocomplete="off">
             </div><!-- ***** Form Group ***** -->  
             
            
         
         <!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('misc.country') }}</label>
            	<select name="countries_id" class="form-control {{((isset($err['countries_id'])) ? " form-error" : "")}}" >
                      		<option value="">{{trans('misc.select_your_country')}}</option>
                      	@foreach(  App\Models\Countries::orderBy('country_name')->get() as $country ) 	
                            <option @if( $user->countries_id == $country->id ) selected="selected" @endif value="{{$country->id}}">{{ $country->country_name }}</option>
						@endforeach
                          </select>
            	    </div>
         <!-- ***** Form Group ***** -->
           
           <button type="submit" id="buttonSubmit" class="btn btn-block btn-lg btn-main custom-rounded">{{ trans('misc.save_changes') }}</button>
                    
       </form><!-- ***** END FORM ***** -->
			
		</div><!-- /COL MD -->
		
		<div class="col-md-4">
			@include('users.navbar-edit')
		</div>
		

 </div><!-- container -->
 
 <!-- container wrap-ui -->
@endsection

@section('javascript')

<script type="text/javascript">
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
	//<<<<<<<=================== * UPLOAD AVATAR  * ===============>>>>>>>//
    $(document).on('change', '#uploadAvatar', function(){
    
    $('.wrap-loader').show();
    
   (function(){
	 $("#formAvatar").ajaxForm({
	 dataType : 'json',	
	 success:  function(e){
	 if( e ){
        if( e.success == false ){
		$('.wrap-loader').hide();
		
		var error = '';
                        for($key in e.errors){
                        	error += '' + e.errors[$key] + '';
                        }
		swal({   
    			title: "{{ trans('misc.error_oops') }}",   
    			text: ""+ error +"",   
    			type: "error",   
    			confirmButtonText: "{{ trans('users.ok') }}" 
    			});
		
			$('#uploadAvatar').val('');

		} else {
			
			$('#uploadAvatar').val('');
			$('.avatarUser').attr('src',e.avatar);
			$('.wrap-loader').hide();
		}
		
		}//<-- e
			else {
				$('.wrap-loader').hide();
				swal({   
    			title: "{{ trans('misc.error_oops') }}",   
    			text: '{{trans("misc.error")}}',   
    			type: "error",   
    			confirmButtonText: "{{ trans('users.ok') }}" 
    			});
    			
				$('#uploadAvatar').val('');
			}
		   }//<----- SUCCESS
		}).submit();
    })(); //<--- FUNCTION %
});//<<<<<<<--- * ON * --->>>>>>>>>>>
//<<<<<<<=================== * UPLOAD AVATAR  * ===============>>>>>>>//
</script>
@endsection