
@extends('app')


@section('css')
	<link rel="stylesheet" type="text/css" href="{{ asset('/public/chat/css/jquery.mCustomScrollbar.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{ asset('/public/chat/chat.css')}}">
@endsection



@section('content')
    <?php
    $current_user = \Auth::user();
    $toUser = ($thread != null ? $thread->Sender($current_user->id):null);
    $number_record = 35;

    $total_mess = ($thread != null ? $thread->messages->count():null);
    if($total_mess <= $number_record){
        $mess = $thread->messages;
    }else{
        $mess = $thread->messages->slice($total_mess-$number_record,$number_record);
    }
    ?>


	<style>



	</style>

	<div class="jumbotron index-header jumbotron_set jumbotron-cover @if( Auth::check() ) session-active-cover @endif">
		<div class="container wrap-jumbotron position-relative">
			<h1 class="title-site txt-left" id="titleSite">{{$settings->welcome_text}}</h1>
			<p class="subtitle-site txt-left"><strong>{{$settings->welcome_subtitle}}</strong></p>
		</div><!-- container wrap-jumbotron -->
	</div><!-- jumbotron -->
	<div class="container margin-bottom-40 padding-top-40">
		<div class="col-md-4">

			<ul class="nav nav-pills nav-stacked">
				<li class="margin-bottom-5">
					<!-- **** list-group-item **** -->
					<a href="{{route('my-chat')}}" class="list-group-item active ">
						<i class="icon icon-pencil myicon-right"></i> Messages
					</a> <!-- **** ./ list-group-item **** -->
				</li>
				@foreach($user->participants as $p)
					<li class="margin-bottom-5">
						<!-- **** list-group-item **** -->

						<a href="#" class="list-group-item ">
							<div class="usr-msg-details">
								<div class="usr-ms-img">
									<img src="{{ $p->thread->Sender($current_user->id)->getAvatar() }}" alt="">
									<span class="msg-status"></span>
								</div>
								<div class="usr-mg-info">
									<h3>{{ $p->thread->Sender($current_user->id)->name }}</h3>
									<p>{{ $p->thread->LastMessage($user->id)->body }}</p>
								</div><!--usr-mg-info end-->
								<span class="posted_time">{{ $p->thread->LastMessage($user->id)->created_at }}</span>
								<span class="msg-notifc">{{ $p->thread->UnreadMessage($user->id) }}</span>
							</div><!--usr-msg-details end-->
							<div class="clearfix"></div>
						</a>
						<!-- **** ./ list-group-item **** -->


					</li>
				@endforeach
			</ul>
		</div>
		<div class="col-md-8">

			<div class="main-conversation-box">
				<div class="message-bar-head" style="position:relative">
					<div class="usr-msg-details">
						<div class="usr-ms-img">
							<img src="{{ $thread->sender($current_user->id)->getAvatar() }}" alt="">
						</div>
						<div class="usr-mg-info">
							<h3>{{ $thread->sender($current_user->id)->name }}</h3>
							<p>Online</p>
						</div><!--usr-mg-info end-->
					</div>
					<a href="#" title=""><i class="fa fa-ellipsis-v"></i></a>
				</div>
				<div class="messages-line" id="box">
					<div class="MessageItem-fetchMore" style="padding: 5px 0px;text-align: center;color: #139ff0;">
						Load earlier messages...
					</div>
					@foreach($mess as $t)
						@include('chat.chatbox',['t'=>$t,'user_id'=>$current_user->id])
					@endforeach
				</div>
				<div class="message-send-area">
					<form>
						<div class="mf-field">
							<input id="my-content" placeholder="Type a message here">
							<button data-avatar="{{$current_user->getAvatar()}}" id="send" data-thread="{{$thread->id}}" type="submit">Send</button>
						</div>
						<ul>
							<li><a href="#" title=""><i class="fa fa-smile-o"></i></a></li>
							<li><a href="#" title=""><i class="fa fa-camera"></i></a></li>
							<li><a href="#" title=""><i class="fa fa-paperclip"></i></a></li>
						</ul>
					</form>
				</div>
			</div>

		</div>
	</div>

@endsection


@section('javascript')
	<script type="text/javascript" src="{{ asset('/public/chat/js/jquery.mCustomScrollbar.js')}}"></script>
	<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.js"></script>
	<script>

        $("#box").mCustomScrollbar({
            theme:"dark",
            alwaysShowScrollbar:1,
            callbacks:{
                onCreate: function(){

                    console.log('load');
                },
                onInit: function(){

                    console.log('done');
                }
            }
        });

        $('#box').mCustomScrollbar("scrollTo","bottom",{
            scrollInertia:500});

        var pusher = new Pusher('a67405a003f557ce006c', {
            cluster: 'us2',
            encrypted: true
        });
        var mychanel = 'user-{{$current_user->id}}';
        var channel = pusher.subscribe(mychanel);
        channel.bind('MessageSent', function(data) {
            console.log(data);
            $.post('{{route('my-chat-api')}}',{_token:'{{ csrf_token() }}',type:'create_mess_box',mess:data.id},function(data){
                $('#mCSB_1_container').append(data);


                $('#box').mCustomScrollbar("scrollTo","bottom",{
                    scrollInertia:300});
            });
        });

        $(document).keypress(function(e) {
            if(e.which == 13) {
                document.getElementById("#send").click();
            }
        });



        function renderBox(body,my_date,avatar){

            var box = $('<div></div>',{
                'class':"main-message-box st3",
            });



            var mess_dt = $('<div></div>',{
                'class':"message-dt st3"
            });

            var mess_inner = $('<div></div>',{
                'class':"message-inner-dt"
            });
            var tx_pox = "<p>"+body+"</p>";

            var span ="<span>"+my_date+"</span>";

            var mess_usr = $('<div></div>',{
                'class':"messg-usr-img"
            });

            var img = '<img src="'+avatar+'" alt="" class="mCS_img_loaded">';

            mess_inner.append(tx_pox);


            mess_dt.append(mess_inner);
            mess_dt.append(span);
            mess_usr.append(img);

            box.append(mess_dt);
            box.append(mess_usr);

            return box;




        }
        $('#send').click(function(e){
            e.preventDefault();
            var to = $(this).attr('data-thread');
            var content = $('#my-content').val();

            if(content == ""){

                return false;
            }
            var avatar_path = $(this).attr('data-avatar');


            var box_content = renderBox(content,moment().format(),avatar_path);

            console.log(box_content);

            $('#mCSB_1_container').append(box_content);
            $('#box').mCustomScrollbar("scrollTo","bottom",{
                scrollInertia:300});
            //$('#box').mCustomScrollbar("update");
            $('.wrap-loader').remove();
            $.post('{{route('my-chat-api')}}',{_token:'{{ csrf_token()}}',type:'add',thread:to,content:content},function(data){
                $('.wrap-loader').hide();
                //$('#mCSB_1_container').append(data);

                $('#my-content').val('');
                //$('#box').mCustomScrollbar("scrollTo","bottom","bottom");
                //$('#mCSB_1_container').scrollTop($('#mCSB_1_container')[0].scrollHeight);
            });
        });
	</script>

    </script>
@endsection

