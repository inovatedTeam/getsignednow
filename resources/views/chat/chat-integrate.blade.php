


<?php


$user = \Auth::user();

$number_record = 25;

?>

<link rel="stylesheet" type="text/css" href="{{ asset('/public/chat/css/jquery.mCustomScrollbar.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/public/chat/chat.css')}}">

<div class="chatbox-list" style="right: 126.5px;">
	@foreach($user->participants as $p)
		<div class="chatbox" data-thread="{{$p->thread->id}}">
			<div class="chat-mg" id="ctconversation" data-thread="{{$p->thread->id}}">
				<a href="#" title=""><img src="{{ $p->thread->sender($user->id)->getAvatar() }}" alt=""></a>
			</div>
			<div class="conversation-box">
				<div class="con-title mg-3">
					<div class="chat-user-info">
						<img style="max-width:40px" src="{{ $p->thread->sender($user->id)->getAvatar() }}" alt="">
						<h3>{{ $p->thread->Sender($user->id)->name }} <span class="status-info"></span></h3>
					</div>
					<div class="st-icons">
						<a href="#" title=""><i class="la la-cog"></i></a>
						<a href="#" title=""  class="close-chat"><i class="la la-minus-square"></i></a>
						<a href="#" title="" class="close-chat"><i class="la la-close"></i></a>
					</div>
				</div>
				<div class="chat-hist">

				</div><!--chat-list end-->
				<div class="typing-msg">
					<form class="box-send" method="POST">
						<input type="hidden" name="type" value="add"/>
						<input type="hidden" name="thread" value="{{$p->thread->id}}"/>
						<textarea name="content" placeholder="Type a message here"></textarea>
						<button type="submit"><i class="fa fa-send"></i></button>
					</form>
					<ul class="ft-options">
						<li><a href="#" title=""><i class="la la-smile-o"></i></a></li>
						<li><a href="#" title=""><i class="la la-camera"></i></a></li>
						<li><a href="#" title=""><i class="fa fa-paperclip"></i></a></li>
					</ul>
				</div><!--typing-msg end-->
			</div><!--chat-history end-->
		</div>

	@endforeach


	<div class="chatbox">
		<div class="chat-mg bx">
			<a href="#" title=""><img src="/public/img/chat.png" alt=""></a>
			<span>{{ Auth::user()->Unread() }}</span>
		</div>
		<div class="conversation-box">
			<div class="con-title">
				<h3>Messages</h3>
				<a href="#" title="" class="close-chat"><i class="la la-minus-square"></i></a>
			</div>
			<div class="chat-list">
				@foreach($user->participants as $p)
					<div class="conv-list active open-sub-room" data-thread="{{$p->thread->id}}">
						<div class="usrr-pic">
							<img src="{{ $p->thread->sender($user->id)->getAvatar() }}" alt="">
							<span class="active-status active"></span>
						</div>
						<div class="usy-info">
							<h3 >{{ $p->thread->Sender($user->id)->name }}</h3>
						</div>
						<div class="ct-time">
							<span>1:55 PM</span>
						</div>
						<span class="msg-numbers">{{ $p->thread->UnreadMessage($user->id) }}</span>
					</div>
				@endforeach
			</div><!--chat-list end-->
		</div><!--conversation-box end-->
	</div>
</div>

@section('javascript')

	<script type="text/javascript" src="{{ asset('/public/chat/js/jquery.mCustomScrollbar.js')}}"></script>

	<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.js"></script>

	<script>

        $("#email_messanger").click(function() {

            var id = $(".chat-mg").attr('data-thread');
            var chat_box = $(".chatbox[data-thread="+id+"]");

            if(!chat_box.hasClass('loaded')){
                bindMessToBox(id,number_record);
            }
            $("#ctconversation").next(".conversation-box").toggleClass("active");
            return false;
        });

        var pusher = new Pusher('a67405a003f557ce006c', {
            cluster: 'us2',
            encrypted: true
        });

        var mychanel = 'user-{{$user->id}}';
        var channel = pusher.subscribe(mychanel);
        channel.bind('MessageSent', function(data) {
            console.log(data);
            $.post('{{route('my-chat-mess')}}',{_token:'{{ csrf_token() }}',type:'create_mess_box_small',mess:data.id},function(data){

                var box =  $(".chatbox[data-thread="+data[0]+"]");
                box.find('.mCSB_container').append(data[1]);
                box.find('.chat-hist').mCustomScrollbar("scrollTo","bottom",{
                    scrollInertia:300});
            });
        });

        var token = '{{ csrf_token() }}';

        var number_record = parseInt('{{$number_record }}');

        $('.open-sub-room').click(function(){

            var id = $(this).attr('data-thread');

            var chat_box = $(".chatbox[data-thread="+id+"]");

            if(!chat_box.hasClass('loaded')){

                bindMessToBox(id,number_record);

            }

            chat_box.toggle();

        });
        $(".close-chat").on("click", function(){


            $(".conversation-box").removeClass("active");
            return false;
        });

        function renderBoxChat(content,mytime){


            var box = $('<div></div>',{
                class:"chat-msg st2"

            });

            var p = $('<p>'+content+'</p>',{

            });

            var span = $('<span>'+mytime+'</span>');

            box.append(p);
            box.append(span);
            return box;

        }

        $(document).keypress(function(e) {
            if(e.which == 13) {

                var focused = $(':focus');

                if(focused.attr('name') == 'content'){
                    focused.closest('form').submit();
                }
            }
        });
        $('.box-send').submit(function(e){
            e.preventDefault();

            var k = $(this).serializeArray();

            if(k[2].value == ""){

                return false;
            }
            var result  = {
                _token:token,
                type:k[0].value,
                thread:k[1].value,
                content:k[2].value

            }
            var box = renderBoxChat(k[2].value,moment().format());

            $(this).closest('.conversation-box').find('#mCSB_1_container').append(box);


            $(this).closest('.conversation-box').find('.chat-hist').mCustomScrollbar(
                "scrollTo","bottom",{
                    scrollInertia:300}
            );

            $(this).find('textarea').val(null);
            $.post('{{route('my-chat-api')}}',result);
        });


        $(".chat-mg").on("click", function(){

            var id = $(this).attr('data-thread');

            var chat_box = $(".chatbox[data-thread="+id+"]");

            if(!chat_box.hasClass('loaded')){

                bindMessToBox(id,number_record);

            }

            $(this).next(".conversation-box").toggleClass("active");
            return false;
        });
        function bindMessToBox(id,number){

            var id = id;
            var send = {
                type:'last-record',
                thread_id:id,
                number:number,
                view:'chat.chatbox-small',
                _token:token
            };

            $.post('{{route('my-chat-mess')}}',send,function(data){

                var chat_box = $(".chatbox[data-thread="+id+"]");
                chat_box.addClass('loaded');
                //console.log(data.rw);
                chat_box.find('.chat-hist').append(data.raw);
                chat_box.find('.chat-hist').mCustomScrollbar("scrollTo","bottom",{
                    scrollInertia:300});
                chat_box.find('.chat-hist').mCustomScrollbar({
                    theme:"dark",
                    callbacks:{
                        onCreate: function(){
                            console.log('create');
                            // disable conversation box
//                            console.log("conversation-box");
//                            $(".chatbox .conversation-box").trigger("click");
                        }
                    }
                });

            });

        }
	</script>
@endsection