
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


/**
 * FBトッピークを削除します
 * @param  string page_id FBトッピークID
 */
function delMatome(page_id){
    var loading = Ladda.create( document.querySelector( '.btn-ok' ) );
	loading.start();
	$.ajax({
		url: '/matome/post/delete',
		data: {
			'page_id':page_id
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