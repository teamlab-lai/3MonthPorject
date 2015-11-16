$(function() {

    $('.comment-items').on('click', function() {
        window.location.href = $(this).data('link');
    });
});

/**
 * お気に入り
 */
function addFav( page_id ){
	$.ajax({
		url: '/matome/favorite/create',
		data: {
			// format: 'json',
			'page_id':page_id
		},
		type: 'POST',
		//dataType: 'jsonp',
		success: function(result) {
			result = $.parseJSON(result);
			var status = result.status;
			if(status != 'OK'){
				alert(result.messages);
				return;
			}else{
				$('.favorite-count').html(result.fav_total);
				addDelFavStatus(page_id);
			}
		},
		error: function() {
			alert('エラーがありますから、もう一度お願いします。');
		},

	});
}

/**
 * お気に入り削除機能
 */
function addDelFavStatus(page_id){
	var fav_html  = "<a href=\"javascript:void(0);\" id=\"del_fav\" onclick=\"delFav('"+page_id+"');\">";
	    fav_html += "<i class=\"pe-7s-star is-fav\"></i>";
	    fav_html += "<p class=\"sm-size\">お気に入り削除</p></a>";
	$('#fav_controller').html(fav_html);
	return;
}

/**
 * お気に入りを削除する機能
 */
function delFav( page_id ){
	$.ajax({
		url: '/matome/favorite/delete',
		data: {
			'page_id':page_id
		},
		type: 'POST',
		success: function(result) {
			result = $.parseJSON(result);
			var status = result.status;
			if(status != 'OK'){
				alert(status.messages);
				return;
			}else{
				$('.favorite-count').html(result.fav_total);
				addAddFavStatus(page_id);
			}
		},
		error: function() {
			alert('エラーがありますから、もう一度お願いします。');
		},

	});
}

/**
 * お気に入り機能
 */
function addAddFavStatus(page_id){
	var fav_html  = "<a href=\"javascript:void(0);\" id=\"add_fav\" onclick=\"addFav('"+page_id+"');\">";
	    fav_html += "<i class=\"pe-7s-star\"></i>";
	    fav_html += "<p class=\"sm-size\">お気に入り追加</p></a>";
	$('#fav_controller').html(fav_html);
	return;
}

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
