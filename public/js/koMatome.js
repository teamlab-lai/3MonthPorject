
$(function() {

	var like_method = new FbMethod();

	$('.js-like').click(function(){
		var comment_id = $(this).attr('data-id');
		if(comment_id == null){
			return false;
		}

		var result = like_method.giveLike(comment_id);
		if(result.result == false){
			alert(result.message);
			return false;
		}
	});


	$('.js-dislike').click(function(){

		var comment_id = $(this).attr('data-id');
		if(comment_id == null){
			return false;
		}

		var result = like_method.concelLike();
		if(result.result == false){
			alert(result.message);
			return false;
		}
	});
});
