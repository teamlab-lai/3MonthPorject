
$(function() {

	var fb_method = new FbMethod();

	$(document).on('click', '.js-like', function(){
		var comment_id = $(this).attr('data-id');
		if(comment_id == null){
			alert('エラーが有ります。');
			return false;
		}

		var result = fb_method.giveLike(comment_id);
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
			alert('エラーが有ります。');
			return false;
		}

		var result = fb_method.concelLike(comment_id);
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

	$(document).on('click', '.js-comment-submit', function(){
		disabledCommentSubmitBtn();
		var input_content = $('.js-comment-input').val();
		if(input_content.length <= 0){
			enabledCommentSubmitBtn();
			alert('コメントを入力してください。');
			return false;
		}
		var comment_id = $('.js-comment-input').attr('data-id');
		if(comment_id == null){
			enabledCommentSubmitBtn();
			alert('エラーが有ります。');
			return false;
		}

		var result = fb_method.postComment(comment_id ,input_content);

		if(result.result == 'ERROR'){
			enabledCommentSubmitBtn();
			alert(result.message);
			return false;
		}else{
			var comment_info = result.comment_info;

			//コメントの内容はURLを確認します
			if(ValidURL(comment_info.message) == true){
				var message_html = '<a href="'+comment_info.message+'"><span>'+comment_info.message+'</span></a>'
			}else{
				var message_html = '<small>'+comment_info.message+'</small>';
			}

			//コメントはURLだたっら、サムネイルを作ります
			if(comment_info.attachment_type != null && comment_info.attachment_type == 'video' || comment_info.attachment_type == 'photo' || comment_info.attachment_type == 'website'){
				var thumbnail_html = '<div class="col-xs-6  ">';
					thumbnail_html += '<a href="'+comment_info.link+'" class="thumbnail no-margin no-border ">';
					if(comment_info.attachment_type == 'video'){
						thumbnail_html +=  '<div class="caption"><i class="glyphicon glyphicon-play-circle"></i></div>';
					}
					thumbnail_html +=  '<img src="'+comment_info.attachment_image+'" alt="">';
					thumbnail_html += '</a>';
					thumbnail_html +='</div>';
			}else{
				var thumbnail_html = '';
			}

			//新しいコメントを作ります
			var new_comment = '<div class="list-group-item no-border no-background-color have-bottom-line"><div class="row  "><div class="col-xs-3">';
				new_comment +='<a href="javascript:void(0);" class="thumbnail no-margin no-border "><img src="'+comment_info.user_photo+'" alt=""></a></div>';
				new_comment +='<div class="col-xs-9 no-padding">';
				new_comment +='<div class="col-xs-12"><a href="javascript:void(0);"><small>'+comment_info.user_name+'</small></a></div>';
				new_comment +='<div class="col-xs-12 comment-message">'+message_html+'</div>'+thumbnail_html;
				new_comment +='</div></div></div>';

			$( ".js-new-comment-block" ).before( new_comment );
			$('.js-comment-input').val('');
			enabledCommentSubmitBtn();
		}
	});

});

function ValidURL(str) {
	var pat = /^https?:\/\/|^\/\//i;
	if (pat.test(str))
	{
	   return true;
	}else{
		return false;
	}
}

function disabledCommentSubmitBtn(){
	$('.js-comment-submit').addClass('disabled');
	$('.js-input-submit-icon').removeClass('glyphicon-pencil');
	$('.js-input-submit-icon').addClass('glyphicon-refresh');
}

function enabledCommentSubmitBtn(){
	$('.js-comment-submit').removeClass('disabled');
	$('.js-input-submit-icon').addClass('glyphicon-pencil');
	$('.js-input-submit-icon').removeClass('glyphicon-refresh');
}