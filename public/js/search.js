$(function() {
	$('#del_history').click(function () {

		var historys = [];
		$(':checkbox:checked').each(function(i){
        	historys[i] = $(this).val();
        });

        delHistory(historys);
	});
});

function delHistory(historys){
	if(historys.length === 0){
		return false;
	}

	$.ajax({
		url: '/matome/search/deleteHistory',
		data: {
			'historys':historys
		},
		type: 'POST',
		success: function(result) {
			result = $.parseJSON(result);
			var status = result.status;
			if(status != 'OK'){
				alert(result.messages);
				return;
			}else{
				var deleted_ids = result.deleted_ids;
				jQuery.each(deleted_ids, function(index, id) {
				   $(".history-" + id).remove();
				});
			}
		},
		error: function() {
			alert('エラーがありますから、もう一度お願いします。');
		},

	});

}