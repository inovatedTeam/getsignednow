
@if($t->type == 'message' && $user_id == $t->user->id)
	<div class="main-message-box st3">
		<div class="message-dt st3">
			<div class="message-inner-dt">
				<p style="max-width: 70%">{{$t->body}}</p>
			</div><!--message-inner-dt end-->
			<span>{{$t->created_at}}</span>
		</div><!--message-dt end-->
		<div class="messg-usr-img">
			<img src="{{ $t->user->getAvatar()}}" alt="" class="mCS_img_loaded"/>
		</div><!--messg-usr-img end-->
	</div><!--main-message-box end-->

@endif

@if($t->type == 'message' && $user_id != $t->user->id)

	<div class="main-message-box ta-right">
		<div class="message-dt" style="float:right">
			<div class="message-inner-dt">
				<p style="width: auto;max-width: 70%">{{$t->body}}</p>
			</div><!--message-inner-dt end-->
			<span>{{$t->created_at}}</span>
		</div><!--message-dt end-->
		<div class="messg-usr-img">
			<img src="{{ $t->user->getAvatar()}}" alt="" class="mCS_img_loaded"/>
		</div><!--messg-usr-img end-->
	</div>

@endif



