@extends('admin.layout')

@section('css')
<link href="{{ asset('public/plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/css/main.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="wrap-loader">
		<i class="fa fa-cog fa-spin fa-3x fa-fw cog-loader"></i>
		<i class="fa fa-cog fa-spin fa-3x fa-fw cog-loader-small"></i>
	</div>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h4>
            {{ trans('admin.admin') }} 
            	<i class="fa fa-angle-right margin-separator"></i> 
            		{{ trans('admin.edit') }}
            		
            		<i class="fa fa-angle-right margin-separator"></i> 
            		{{ $data->name }}
          </h4>

        </section>

        <!-- Main content -->
        <section class="content">

        	<div class="content">
        		
       <div class="row">
       	
       	<div class="col-md-9">
    
        	<div class="box box-danger">
                <div class="box-header with-border">
                  <h3 class="box-title">{{ trans('admin.edit') }}</h3>
                </div><!-- /.box-header -->
               
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ url('panel/admin/members/'.$data->id) }}" enctype="multipart/form-data">
                	
                	<input type="hidden" name="_token" value="{{ csrf_token() }}">
                	<input type="hidden" name="_method" value="PUT">	
			
					@include('errors.errors-forms')
                                        @if (count($errors) > 0)
                                            <?php $err = $errors->default->toArray();?> 
                                        @endif
                                        
                <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.network') }}</label>
                      <div class="col-sm-10">
                        <select name="network_type_id" class="form-control {{((isset($err['network_type_id'])) ? " form-error" : "")}}" >
                            <option value="" disabled></option>
                                @foreach(  App\Models\Networks::get() as $network ) 	
                                    <option @if( $data->network_type_id == $network->id ) selected="selected" @endif value="{{$network->id}}">{{ $network->network_type }}</option>
                                @endforeach
                          </select>
                      </div>
                    </div>
                  </div>
                  <!-- /.box-body -->
                 <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.first_name') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="{{ $data->name }}" name="first_name" class="form-control {{((isset($err['first_name'])) ? " form-error" : "")}}" placeholder="{{ trans('admin.first_name') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                 
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.last_name') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="{{ $data->last_name }}" name="last_name" class="form-control {{((isset($err['last_name'])) ? " form-error" : "")}}" placeholder="{{ trans('admin.last_name') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.company_name') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="{{ $data->company_name }}" name="company_name" class="form-control" placeholder="{{ trans('admin.company_name') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.mobile_number') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="{{ $data->mobile_number }}" name="mobile_number" class="form-control {{((isset($err['mobile_number'])) ? " form-error" : "")}}" placeholder="{{ trans('admin.mobile_number') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->                                    
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('auth.email') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="{{ $data->email }}" name="email" class="form-control {{((isset($err['email'])) ? " form-error" : "")}}" placeholder="{{ trans('auth.email') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.state') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="{{ $data->state }}" name="state" class="form-control {{((isset($err['state'])) ? " form-error" : "")}}" placeholder="{{ trans('admin.state') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.country') }}</label>
                      <div class="col-sm-10">
                        <select name="countries_id" class="form-control {{((isset($err['countries_id'])) ? " form-error" : "")}}" >
                      		<option value="">{{trans('misc.select_your_country')}}</option>
                                @foreach(  App\Models\Countries::orderBy('country_name')->get() as $country ) 	
                                    <option @if( $data->countries_id == $country->id ) selected="selected" @endif value="{{$country->id}}">{{ $country->country_name }}</option>
                                @endforeach
                          </select>
                      </div>
                    </div>
                  </div>
                  <!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.role') }}</label>
                      <div class="col-sm-10">
                        <select name="role" class="form-control" >
                      		<option @if($data->role == 'normal') selected="selected" @endif value="normal">{{trans('admin.normal')}} {{trans('admin.normal_user')}}</option>
                      		<option @if($data->role == 'admin') selected="selected" @endif value="admin">{{trans('misc.admin')}}</option>
                          </select>
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('auth.password') }}</label>
                      <div class="col-sm-10">
                        <input type="password" value="" name="password" class="form-control" placeholder="{{ trans('admin.password_no_change') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <div class="box-footer">
                  	 <a href="{{ url('panel/admin/members') }}" class="btn btn-default">{{ trans('admin.cancel') }}</a>
                    <button type="submit" class="btn btn-success pull-right">{{ trans('admin.save') }}</button>
                  </div><!-- /.box-footer -->
                </form>
              </div>
              
        </div><!-- /. col-md-9 -->
        
        <div class="col-md-3">
        	<!-- *********** AVATAR ************* -->
		
		<form action="{{url('panel/admin/member/uploadAvatar')}}" method="POST" id="formAvatar" accept-charset="UTF-8" enctype="multipart/form-data">
    		<input type="hidden" name="_token" value="{{ csrf_token() }}">
    		
    		<div class="block-block text-center center">
        		<img src="{{asset('public/avatar').'/'.$data->avatar}}" width="100%" class="avatarUser thumbnail img-responsive">
        	</div>
    		
    		<div class="text-center">
    			<button type="button" class="btn btn-default btn-border btn-sm" id="avatar_file" style="margin-bottom: 10px;">
	    		<i class="icon-camera myicon-right"></i> {{ trans('misc.change_avatar') }}
	    		</button>
	    		<input type="hidden" name="user_id" value="{{$data->id}}">
	    		<input type="file" name="photo" id="uploadAvatar" accept="image/*" style="visibility: hidden;">
    		</div>
			
		    </form>
			<!-- *********** AVATAR ************* -->
			
        	        	        	
        	<ol class="list-group">
			<li class="list-group-item"> {{trans('admin.registered')}} <span class="pull-right color-strong">{{ date('d M, y', strtotime($data->date)) }}</span></li>
			
			<li class="list-group-item"> {{trans('admin.status')}} <span class="pull-right color-strong">{{ ucfirst($data->status) }}</span></li>
			
			<li class="list-group-item"> {{trans('misc.country')}} <span class="pull-right color-strong">@if( $data->countries_id != '' ) {{ $data->country()->country_name }} @else {{ trans('admin.not_established') }} @endif</span></li>
			
			<li class="list-group-item"> {{trans_choice('misc.campaigns_plural', 0)}} <strong class="pull-right color-strong">{{ App\Helper::formatNumber( $data->campaigns()->count() ) }}</strong></li>
                        
                        <li class="list-group-item"> {{trans_choice('misc.registration_ip', 0)}} <strong class="pull-right color-strong">@if( $data->registration_ip_address != '' || $data->registration_ip_address != null) {{ $data->registration_ip_address }} @else {{ '' }} @endif</strong></li>
                        
                        <li class="list-group-item"> {{trans_choice('misc.last_login_ip', 0)}} <strong class="pull-right color-strong">@if( $data->registration_ip_address != '' || $data->last_login_ip_address != null) {{ $data->last_login_ip_address }} @else {{ '' }} @endif</strong></li>
					</ol>
		
		<div class="block-block text-center">
		{!! Form::open([
			            'method' => 'DELETE',
			            'route' => ['user.destroy', $data->id],
			            'class' => 'displayInline'
				        ]) !!}
	            	{!! Form::submit(trans('admin.delete'), ['data-url' => $data->id, 'class' => 'btn btn-lg btn-danger btn-block margin-bottom-10 actionDelete']) !!}
	        	{!! Form::close() !!}
	        </div>
		
		</div><!-- col-md-3 -->        			        		
        		
        		</div><!-- /.row -->
        		
        	</div><!-- /.content -->
        	
          <!-- Your Page Content Here -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
@endsection

@section('javascript')
	
	<!-- icheck -->
	<script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('public/js/jquery.form.js') }}" type="text/javascript"></script>
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
	$(".actionDelete").click(function(e) {
   	e.preventDefault();
   	   	
   	var element = $(this);
	var id     = element.attr('data-url');
	var form    = $(element).parents('form');
	
	element.blur();
	
	swal(
		{   title: "{{trans('misc.delete_confirm')}}",  
		text: "{{trans('admin.delete_user_confirm')}}",
		  type: "warning", 
		  showLoaderOnConfirm: true,
		  showCancelButton: true,   
		  confirmButtonColor: "#DD6B55",  
		   confirmButtonText: "{{trans('misc.yes_confirm')}}",   
		   cancelButtonText: "{{trans('misc.cancel_confirm')}}",  
		    closeOnConfirm: false, 
		    }, 
		    function(isConfirm){  
		    	 if (isConfirm) {   
		    	 	form.submit(); 
		    	 	//$('#form' + id).submit();
		    	 	}
		    	 });
		    	 
		    	 
		 });
		 
		//Flat red color scheme for iCheck
        $('input[type="radio"]').iCheck({
          radioClass: 'iradio_flat-red'
        });
		//<<<<<<<=================== * UPLOAD AVATAR  * ===============>>>>>>>//
	
	$(document).on('click','#avatar_file',function () {
		var _this = $(this);
	    $("#uploadAvatar").trigger('click');
    	     _this.blur();
	});
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
