<ul class="nav nav-pills nav-stacked">
			<li class="margin-bottom-5">
				<!-- **** list-group-item **** -->	
		  <a href="{{ url('account') }}" class="list-group-item @if(Request::is('account'))active @endif"> 
		  	<i class="icon icon-pencil myicon-right"></i> {{ trans('users.account_settings') }} 
		  	</a> <!-- **** ./ list-group-item **** -->
			</li>
				
		  	<li class="margin-bottom-5">
		  		<!-- **** list-group-item **** -->	
		  <a href="{{ url('account/password') }}" class="list-group-item @if(Request::is('account/password'))active @endif"> 
		  	<i class="icon icon-lock myicon-right"></i> {{ trans('auth.password') }} 
		  	</a> <!-- **** ./ list-group-item **** -->
		  	</li>
		 
		  	<li class="margin-bottom-5">
		  		<!-- **** list-group-item **** -->	
		  <a href="{{ url('account/campaigns') }}" class="list-group-item @if(Request::is('account/campaigns'))active @endif"> 
		  	<i class="ion ion-speakerphone myicon-right"></i> {{ trans('misc.campaigns') }} 
		  	</a> <!-- **** ./ list-group-item **** -->
		  	</li>
		  	 	@if( Auth::user()->network()->id == 1)	
		  	<li class="margin-bottom-5">
		  	   
		  		<!-- **** list-group-item **** -->	
		  <a href="{{ url('account/donations') }}" class="list-group-item @if(Request::is('account/donations'))active @endif"> 
		  	<i class="ion ion-social-usd myicon-right"></i> {{ trans('misc.donations') }} 
		  	</a> <!-- **** ./ list-group-item **** -->
		  	</li>
		 
		  	<li class="margin-bottom-5">
		  		<!-- **** list-group-item **** -->	
		  <a href="{{ url('account/withdrawals') }}" class="list-group-item @if(Request::is('account/withdrawals'))active @endif"> 
		  	<i class="fa fa-money myicon-right"></i> {{ trans('misc.withdrawals') }} 
		  	</a> <!-- **** ./ list-group-item **** -->
		  	</li>
		  	 	@endif
		</ul>