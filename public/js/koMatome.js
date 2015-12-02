
$(function() {

	var fb_method = new FbMethod();

	$(document).on('click', '.js-like', function(){
		var comment_id = $(this).attr('data-id');
		if(comment_id == null){
			alert('エラーが有ります。');
			return false;
		}
		fb_method.giveLike(comment_id);
	});

	$(document).on('click', '.js-dislike', function(){

		var comment_id = $(this).attr('data-id');
		if(comment_id == null){
			alert('エラーが有ります。');
			return false;
		}

		fb_method.concelLike(comment_id);
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
		fb_method.postComment(comment_id ,input_content);

	});

});

function disabledCommentSubmitBtn(){
	$('.js-comment-submit').addClass('disabled');
	$('.js-comment-submit').html('<i class="icon-spinner icon-spin"></i>');
}

function enabledCommentSubmitBtn(){
	$('.js-comment-submit').removeClass('disabled');
	$('.js-comment-submit').html('<span class="glyphicon js-input-submit-icon glyphicon-pencil" aria-hidden="true"></span>');
}

/**
 * FBコメントページを削除します
 * @param  string comment_id FBコメントID
 */
function delComment(comment_id){
	var loading = Ladda.create( document.querySelector( '.btn-ok' ) );
	loading.start();
	$.ajax({
		url: '/matome/comment/delete',
		data: {
			'comment_id':comment_id
		},
		type: 'POST',
		success: function(result) {

			result = $.parseJSON(result);
			var status = result.status;
			if(status != 'OK'){
				alert(result.messages);
				loading.stop();
				return;
			}else{
				window.location.replace(result.redirect_url);
			}

		},
		error: function() {
			alert('エラーがありますから、もう一度お願いします。');
		},

	});
}
