@extends('admin.layout')

@section('css')
<link href="{{ asset('public/plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/css/main.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
    <div class="wrap-loader">
		<i class="fa fa-cog fa-spin fa-3x fa-fw cog-loader"></i>
		<i class="fa fa-cog fa-spin fa-3x fa-fw cog-loader-small"></i>
	</div>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h4>
            {{ trans('admin.admin') }} 
            <i class="fa fa-angle-right margin-separator"></i>
            {{ trans('admin.members') }}
            	<i class="fa fa-angle-right margin-separator"></i> 
            		{{ trans('misc.add_new') }}
          </h4>

        </section>

        <!-- Main content -->
        <section class="content">

        	<div class="content">
        		
       <div class="row">
       	
       	<div class="col-md-12">
    
        	<div class="box box-danger">
                <div class="box-header with-border">
                  <h3 class="box-title">{{ trans('misc.add_new') }}</h3>
                </div><!-- /.box-header -->
               
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ url('panel/admin/member/add') }}" enctype="multipart/form-data">
                	
                	<input type="hidden" name="_token" value="{{ csrf_token() }}">
			
					@include('errors.errors-forms')
                    @if (count($errors) > 0)
                        <?php $err = $errors->default->toArray();?> 
                    @endif
                                    
                	<!-- *********** AVATAR ************* -->
                		
            		<div class="text-center">
            			<img src="{{ asset('public/avatar/default.jpg')}}" alt="User" width="100" height="100" class="img-rounded avatarUser"  />
            		</div>
            		
            		<div class="text-center">
            			<button type="button" class="btn btn-default btn-border btn-sm" id="avatar_file" style="margin-top: 10px;">
        	    		<i class="icon-camera myicon-right"></i> {{ trans('misc.change_avatar') }}
        	    		</button>
        	    		<input type="file" name="photo" id="uploadAvatar" accept="image/*" style="visibility: hidden;">
            		</div>

			        <!-- *********** AVATAR ************* -->
                                
                    <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.network') }}</label>
                      <div class="col-sm-10">
                        <select name="network_type_id" class="form-control {{((isset($err['network_type_id'])) ? " form-error" : "")}}" >
                            <option value="">{{ trans('admin.network') }}</option>
                                @foreach(  App\Models\Networks::get() as $network ) 	
                                    <option value="{{$network->id}}">{{ $network->network_type }}</option>
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
                        <input type="text" value="" name="first_name" class="form-control {{((isset($err['first_name'])) ? " form-error" : "")}}" value="{{ old('first_name') }}" placeholder="{{ trans('admin.first_name') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                 
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.last_name') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="" name="last_name" class="form-control {{((isset($err['last_name'])) ? " form-error" : "")}}" value="{{ old('last_name') }}" placeholder="{{ trans('admin.last_name') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.company_name') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="{{ trans('admin.company_name') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.mobile_number') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="" name="mobile_number" value="{{ old('mobile_number') }}" class="form-control {{((isset($err['mobile_number'])) ? " form-error" : "")}}" placeholder="{{ trans('admin.mobile_number') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->                                    
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('auth.email') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="" name="email" value="{{ old('email') }}" class="form-control {{((isset($err['email'])) ? " form-error" : "")}}" placeholder="{{ trans('auth.email') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('admin.state') }}</label>
                      <div class="col-sm-10">
                        <input type="text" value="" name="state" value="{{ old('state') }}" class="form-control {{((isset($err['state'])) ? " form-error" : "")}}" placeholder="{{ trans('admin.state') }}">
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
                                    <option value="{{$country->id}}">{{ $country->country_name }}</option>
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
                      		<option value="normal">{{trans('admin.normal')}} {{trans('admin.normal_user')}}</option>
                      		<option value="admin">{{trans('misc.admin')}}</option>
                          </select>
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('auth.password') }}</label>
                      <div class="col-sm-10">
                        <input type="password" value="" name="password" class="form-control {{((isset($err['password'])) ? " form-error" : "")}}" placeholder="{{ trans('auth.password') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  
                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('auth.confirm_password') }}</label>
                      <div class="col-sm-10">
                        <input type="password" value="" name="password_confirmation" class="form-control {{((isset($err['password'])) ? " form-error" : "")}}" placeholder="{{ trans('auth.confirm_password') }}">
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
                		
        		</div><!-- /.row -->
        		
        	</div><!-- /.content -->
        	
          <!-- Your Page Content Here -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
@endsection

@section('javascript')
	
	<!-- icheck -->
	<script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
	
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
	
	$(document).on('click','#avatar_file',function () {
		var _this = $(this);
	    $("#uploadAvatar").trigger('click');
    	     _this.blur();
	});
	$(document).on('change', '#uploadAvatar', function(){
        $('.wrap-loader').show();
        var input = $(this);
        if (input[0].files && input[0].files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('.avatarUser').attr('src', e.target.result);
            }
        
            reader.readAsDataURL(input[0].files[0]);
          }
        $('.wrap-loader').hide();  
    });
	</script>
	

@endsection
