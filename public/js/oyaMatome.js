
$(function() {

	var like_method = new FbMethod();

	$(document).on('click', '.js-like', function(){
		var comment_id = $(this).attr('data-id');
		if(comment_id == null){
			return false;
		}

		var result = like_method.giveLike(comment_id);
		if(result.result == 'ERROR'){
			alert(result.message);
			return false;
		}else{
			var html = "<a href=\"javascript:void(0);\" class=\"font-gray like-button js-dislike\" data-id=\""+comment_id+"\"><small>いいね!を取り消す</small></a>";
			$('#fb_like_btn').html(html);
			if(result.likes > 0){
				$('#likes_area').html("<small>"+result.likes+"がいいね！と言っています</small>");
				$('#likes_area').show();
			}else{
				$('#likes_area').hide();
			}
		}
	});

	$(document).on('click', '.js-dislike', function(){

		var comment_id = $(this).attr('data-id');
		if(comment_id == null){
			return false;
		}

		var result = like_method.concelLike(comment_id);
		if(result.result == 'ERROR'){
			alert(result.message);
			return false;
		}else{
			var html = "<a href=\"javascript:void(0);\" class=\"font-gray like-button js-like\" data-id=\""+comment_id+"\"><small>いいね!</small></a>";
			$('#fb_like_btn').html(html);
			if(result.likes > 0){
				$('#likes_area').html("<small>"+result.likes+"がいいね！と言っています</small>");
				$('#likes_area').show();
			}else{
				$('#likes_area').hide();
			}
		}
	});
});
