function myFavorite(){
	this.addFav = function (page_id) {
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
				return false;
			},

		});
	},
	this.delFav = function (page_id) {
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
				return false;
			},

		});
	}


	/**
	 * お気に入り削除機能
	 */
	function addDelFavStatus(page_id){
		var fav_html  = "<a href=\"javascript:void(0);\" class=\"js-del-fav\" data-id=\""+page_id+"\">";
		    fav_html += "<i class=\"pe-7s-star is-fav\"></i>";
		    fav_html += "<p class=\"sm-size\">お気に入り削除</p></a>";
		$('#fav_controller').html(fav_html);
		return;
	}

	/**
	 * お気に入り機能
	 */
	function addAddFavStatus(page_id){
		var fav_html  = "<a href=\"javascript:void(0);\" class=\"js-add-fav\" data-id=\""+page_id+"\">";
		    fav_html += "<i class=\"pe-7s-star\"></i>";
		    fav_html += "<p class=\"sm-size\">お気に入り追加</p></a>";
		$('#fav_controller').html(fav_html);
		return;
	}
}