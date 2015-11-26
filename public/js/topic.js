$(function() {

	var my_favorite = new myFavorite();

    $('.comment-items').on('click', function() {
        window.location.href = $(this).data('link');
    });


	$(document).on('click', '.js-add-fav', function(){
		my_favorite.addFav($(this).data('id'));
	});

	$(document).on('click', '.js-del-fav', function(){
		my_favorite.delFav($(this).data('id'));
	});
});

/**
 * FBトッピークを削除します
 * @param  string page_id FBトッピークID
 */
function delMatome(page_id){
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

/**
 * FBコメントページを削除します
 * @param  string comment_id FBコメントID
 */
function delComment(comment_id){
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
