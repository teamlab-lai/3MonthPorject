
$(document).ready(function(){

	var like_method = new FbMethod();

	$(document).on('click', '.js-like', function(){

		var comment_id = $(this).attr('data-id');
		if(comment_id == null){
			return false;
		}
		like_method.giveLike(comment_id);
		return true;

	});

	$(document).on('click', '.js-dislike', function(){

		var comment_id = $(this).attr('data-id');
		if(comment_id == null){
			return false;
		}
		like_method.concelLike(comment_id);
		return true;
	});
});
