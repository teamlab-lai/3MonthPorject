function FbMethod() {


	this.result = {
		result: false,
		message: null
	};

    this.giveLike = function (fb_page_id) {
        if( fb_page_id == null){
        	this.result['message'] = 'fb page id is not correct.';
            return this.result;
        }

        var fb_result;
        var fb_message;
        var fb_likes;
        $.ajax({
            url: '/matome/FbMethod/likeCreate',
            data: {
                'fb_page_id':fb_page_id
            },
            type: 'POST',
            async: false,
            success: function(result) {
                result = $.parseJSON(result);
                fb_result = result.status;
                fb_message = result.messages;
                fb_likes = result.likes;
            },
            error: function() {
               fb_message = 'エラーがありますから、もう一度お願いします。';
            },

        });
        this.result['result'] = fb_result;
        this.result['likes'] = fb_likes;
        this.result['message'] = fb_message;
        return this.result;
    },

    this.concelLike = function (fb_page_id) {
        if( fb_page_id == null){
        	this.result['message'] = 'fb page id is not correct.';
            return this.result;
        }

        var fb_result;
        var fb_message;
        var fb_likes;
        $.ajax({
            url: '/matome/FbMethod/likeDelete',
            data: {
                'fb_page_id':fb_page_id
            },
            type: 'POST',
            async: false,
            success: function(result) {
                result = $.parseJSON(result);
                fb_result = result.status;
                fb_message = result.messages;
                fb_likes = result.likes;
            },
            error: function() {
               fb_message = 'エラーがありますから、もう一度お願いします。';
            },

        });
        this.result['result'] = fb_result;
        this.result['likes'] = fb_likes;
        this.result['message'] = fb_message;
        return this.result;
    }

    this.postComment = function (fb_page_id ,input_message) {
        if( fb_page_id == null){
            this.result['message'] = 'fb page id is not correct.';
            return this.result;
        }
        if(input_message == null || input_message.length <= 0){
            this.result['message'] = '入力してください';
            return this.result;
        }

        var fb_result;
        var fb_message;
        var fb_comment_info;
        $.ajax({
            url: '/matome/FbMethod/postComment',
            data: {
                'fb_page_id':fb_page_id,
                'input_message':input_message
            },
            type: 'POST',
            async: false,
            success: function(result) {
                result = $.parseJSON(result);
                fb_result = result.status;
                fb_message = result.messages;
                fb_comment_info = result.comment_info;
            },
            error: function() {
               fb_message = 'エラーがありますから、もう一度お願いします。';
            },

        });
        this.result['result'] = fb_result;
        this.result['message'] = fb_message;
        this.result['comment_info'] = fb_comment_info;
        return this.result;
    }
};
