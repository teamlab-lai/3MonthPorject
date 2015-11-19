/**
 * お気に入りを削除する機能
 */
function delFav( id, page_id ){
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
				alert(result.messages);
				return;
			}else{
				$('#'+id).remove();
				window.location.reload();
			}
		},
		error: function() {
			alert('エラーがありますから、もう一度お願いします。');
		},

	});
}
