function FbMethod() {


	this.result = {
		result: false,
		message: null
	};

    this.giveLike = function (fb_page_id) {
        if( fb_page_id == null){
        	this.result['message'] = 'fb page id is not correct.';
        }

        $.ajax({
            url: '/matome/fbMethod/fbLikeCreate',
            data: {
                'fb_page_id':fb_page_id
            },
            type: 'POST',
            success: function(result) {
                result = $.parseJSON(result);

            },
            error: function() {
                alert('エラーがありますから、もう一度お願いします。');
            },

        });
        return this.result;
    },

    this.concelLike = function (fb_page_id) {
        if( fb_page_id == null){
        	this.result['message'] = 'fb page id is not correct.';
        }

        $.ajax({
            url: '/matome/fbMethod/fbLikeDelete',
            data: {
                'fb_page_id':fb_page_id
            },
            type: 'POST',
            success: function(result) {
                result = $.parseJSON(result);

            },
            error: function() {
                alert('エラーがありますから、もう一度お願いします。');
            },

        });

        return this.result;
    }
};
