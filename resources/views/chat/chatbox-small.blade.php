
<?php

$class = ($t->type == 'message' && $user_id == $t->user->id ? 'st2':'');

?>


<div class="chat-msg {{$class}}">
	<p>{{$t->body}}</p> 
	<span>{{$t->created_at}}</span>
</div>
