<?php 
// ** Data User logged ** //
     $user = Auth::user();
	 $settings = App\Models\AdminSettings::first();	 	 	 
	  ?>
@extends('app')

@section('title') {{ trans('misc.withdrawals') }} - @endsection

@section('content') 
<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
      <div class="container wrap-jumbotron position-relative">
        <h2 class="title-site">{{ trans('misc.withdrawals') }}</h2>
      </div>
    </div>

<div class="container margin-bottom-40">
	
		<!-- Col MD -->
		<div class="col-md-8 margin-bottom-20">

<div class="table-responsive">
   <table class="table table-striped"> 
   	
   	<h5><strong>{{ trans('misc.default_withdrawal') }}</strong>: @if( Auth::user()->payment_gateway == '' ) {{trans('misc.unconfigured')}} @else {{Auth::user()->payment_gateway}} @endif
   		
   		<a class="btn btn-xs btn-success pull-right margin-bottom-5" href="{{url('account/withdrawals/configure')}}">
	  <i class="fa fa-cog myicon-right"></i> {{trans('misc.configure')}}
	</a>
   		
   		</h5>
   	
   	
   	@if( $data->total() !=  0 && $data->count() != 0 )
   	<thead> 
   		<tr>
   		 <th class="active">ID</th>
   		  <th class="active">{{ trans('misc.campaign') }}</th>
          <th class="active">{{ trans('admin.amount') }}</th>
          <th class="active">{{ trans('misc.method') }}</th>
          <th class="active">{{ trans('admin.status') }}</th>
          <th class="active">{{ trans('admin.date') }}</th>
          <th class="active">{{ trans('admin.actions') }}</th>
          </tr>
   		  </thead> 
   		  
   		  <tbody> 

   		      @foreach( $data as $withdrawal )
   		         		      
                    <tr>
                      <td>{{ $withdrawal->id }}</td>
                      <td>
                      	<a title="{{$withdrawal->title}}" href="{{ url('campaign',$withdrawal->campaigns()->id) }}" target="_blank">{{ str_limit($withdrawal->campaigns()->title,20,'...') }} <i class="fa fa-external-link-square"></i></a>
                      	</td>
                      <td>{{ $settings->currency_symbol.$withdrawal->amount }}</td>
                      <td>{{ $withdrawal->gateway }}</td>
                      <td>
                      	@if( $withdrawal->status == 'paid' )
                      	<span class="label label-success">{{trans('misc.paid')}}</span>
                      	@else
                      	<span class="label label-warning">{{trans('misc.pending_to_pay')}}</span>
                      	@endif
                      </td>
                      <td>{{ date('d M, y', strtotime($withdrawal->date)) }}</td>
                      <td>
                
                @if( $withdrawal->status != 'paid' )      	
                      	{!! Form::open([
			            'method' => 'POST',
			            'url' => "delete/withdrawal/$withdrawal->id",
			            'class' => 'displayInline'
				        ]) !!}
				        				        
	            	{!! Form::button(trans('misc.delete'), ['class' => 'btn btn-danger btn-xs deleteW']) !!}
	        	{!! Form::close() !!}
	        	
	        	@else
	        	
	        	- {{trans('misc.paid')}} -
	        	
	        	@endif
	        	
                      	<!--<a href="javascript:void(0);" data-url="{{ url('delete/withdrawal',$withdrawal->id) }}" id="deleteW" class="btn btn-xs btn-danger" title="{{trans('misc.delete')}}">
                      		<i class="glyphicon glyphicon-remove-circle"></i> {{trans('misc.delete')}}
                      	</a>-->
                      	
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

@section('javascript')
<script type="text/javascript">

$(".deleteW").click(function(e) {
   	e.preventDefault();
   	   	
   	var element = $(this);
	var url     = element.attr('data-url');
	
	element.blur();
	
	swal(
		{   title: "{{trans('misc.delete_confirm')}}",  
		 text: "{{trans('misc.confirm_delete_withdrawal')}}",  
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
		    	 	$('.displayInline').submit();
		    	 	//window.location.href = url;
		    	 	}
		    	 });
		    	 
		    	 
		 });
</script>

@endsection