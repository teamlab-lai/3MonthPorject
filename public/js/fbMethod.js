function FbMethod() {
    this.giveLike = function (fb_page_id) {
        if( fb_page_id == null){
        	this.result['message'] = 'fb page id is not correct.';
            return this.result;
        }
        $('#fb_like_btn').hide();
        $('#fb_like_btn').after('<div class="col-xs-12 js-like-loading"><i class="icon-spinner icon-spin"></i></div>');
        $.ajax({
            url: '/matome/FbMethod/likeCreate',
            data: {
                'fb_page_id':fb_page_id
            },
            type: 'POST',
            success: function(result) {
                result = $.parseJSON(result);

                $('.js-like-loading').remove();
                $('#fb_like_btn').show();
                if(result.status == 'OK'){
                    giveLikeSuccess(result , fb_page_id);
                    return true;
                }else{
                    alert(result.message);
                    return false;
                }

            },
            error: function() {
                alert('エラーがありますから、もう一度お願いします。');
                $('.js-like-loading').remove();
                $('#fb_like_btn').show();
                return false;
            },

        });

    },

    this.concelLike = function (fb_page_id) {
        if( fb_page_id == null){
        	this.result['message'] = 'fb page id is not correct.';
            return this.result;
        }
        $('#fb_like_btn').hide();
        $('#fb_like_btn').after('<div class="col-xs-12 js-like-loading"><i class="icon-spinner icon-spin"></i></div>');
        $.ajax({
            url: '/matome/FbMethod/likeDelete',
            data: {
                'fb_page_id':fb_page_id
            },
            type: 'POST',
            success: function(result) {
                result = $.parseJSON(result);
                $('.js-like-loading').remove();
                $('#fb_like_btn').show();
                if(result.status == 'OK'){
                    concelLikeSuccess(result , fb_page_id);
                    return true;
                }else{
                    alert(result.message);
                    return false;
                }

            },
            error: function() {
                alert('エラーがありますから、もう一度お願いします。');
                $('.js-like-loading').remove();
                $('#fb_like_btn').show();
                return false;
            },

        });

    },

    this.postComment = function (fb_page_id ,input_message) {
        if( fb_page_id == null){
            this.result['message'] = 'fb page id is not correct.';
            return this.result;
        }
        if(input_message == null || input_message.length <= 0){
            this.result['message'] = '入力してください';
            return this.result;
        }

        $.ajax({
            url: '/matome/FbMethod/postComment',
            data: {
                'fb_page_id':fb_page_id,
                'input_message':input_message
            },
            type: 'POST',
            success: function(result) {
                result = $.parseJSON(result);
                fb_result = result.status;
                fb_message = result.messages;
                fb_comment_info = result.comment_info;

                if(result.status == 'ERROR'){
                   alert(result.messages);
                   enabledCommentSubmitBtn();
                   return false;
                }else{
                    giveCommentSuccess(result);
                    return true;
                }
            },
            error: function() {
               alert('エラーがありますから、もう一度お願いします。');
               enabledCommentSubmitBtn();
               return false;
            },

        });
    }

    function giveLikeSuccess(result , comment_id) {
        var html = "<a href=\"javascript:void(0);\" class=\"font-gray like-button js-dislike\" data-id=\""+comment_id+"\"><small>いいね!を取り消す</small></a>";
        $('#fb_like_btn').html(html);
        if(result.likes > 0){
            $('#likes_area').html("<small>"+result.likes+"がいいね！と言っています</small>");
            $('#likes_area').show();
        }else{
            $('#likes_area').hide();
        }
        return true;
    }

    function concelLikeSuccess(result , comment_id){
        var html = "<a href=\"javascript:void(0);\" class=\"font-gray like-button js-like\" data-id=\""+comment_id+"\"><small>いいね!</small></a>";
        $('#fb_like_btn').html(html);
        if(result.likes > 0){
            $('#likes_area').html("<small>"+result.likes+"がいいね！と言っています</small>");
            $('#likes_area').show();
        }else{
            $('#likes_area').hide();
        }
        return true;
    }

    function giveCommentSuccess(result){
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
        return true;
    }

    function enabledCommentSubmitBtn(){
        $('.js-comment-submit').removeClass('disabled');
        $('.js-comment-submit').html('<span class="glyphicon js-input-submit-icon glyphicon-pencil" aria-hidden="true"></span>');
        return true;
    }

    function ValidURL(str) {
        var pat = /^https?:\/\/|^\/\//i;
        if (pat.test(str)){
            return true;
        }else{
            return false;
        }
    }

};
