var can_submit = false;
var can_detect_url = false;
$(function() {

    $('.komatome-tab').click(function(event){
        if ($(this).hasClass('disabled')) {
            return false;
        }
    });


    $('#pic_tab').click(function(event){
    	//$('.komatome-tab').removeClass("disabled");
    });

    $('#video_tab').click(function(event){
    	//$('.komatome-tab').addClass('disabled');
    });

	/**
	 * ファイルをアップロード
	 */
	$("#picture_url").change(function(){
	    readImg(this , 'image_preview');
	});

	$('#clear_picture_image').click(function(){
		clearImgFile('image_preview');
	});

    var typingTimer;                //timer identifier
    var doneTypingInterval = 2000;  //time in ms, 5 second for example

    $('#video_url').keyup(function(){
        can_detect_url = false;
        clearTimeout(typingTimer);
        if ($('#video_url').val) {
            typingTimer = setTimeout(startDetectUrl, doneTypingInterval);
        }else{
            can_detect_url = false;
        }
    });

    $('#video_url').keydown(function(){
        can_detect_url = false;
    });

    $('#video_url').on('input propertychange', function () {
        if(can_detect_url == false){
            $('#video_url').trigger('keyup');
            return;
        }
        $('.urlive-container').urlive('remove');
        can_submit = false;
         $('#video_url').urlive({
            callbacks: {
                onStart: function () {
                    disabledUrlPreviewInput();
                    $('.urlive-container').urlive('remove');
                },
                onSuccess: function (data) {
                    enabledUrlPreviewInput();
                    can_submit = true;
                },
                noData: function () {
                    enabledUrlPreviewInput();
                    $('.urlive-container').urlive('remove');
                },
                imgError: function () {
                    alert('URLが間違いますたら、もう一度お願いします。');
                    enabledUrlPreviewInput();
                    $('.urlive-container').urlive('remove');
                },
                onLoadEnd: function () {
                    enabledUrlPreviewInput();
                    can_submit = true;
                },

            }
        });
    }).trigger('input');

    $('#preview_url_attach').bind('click', function(e) {
        $('#video_url').trigger('input');
    });

    $('#comment_form').submit(function(e){
       var loading = Ladda.create( document.querySelector( '.js-submit' ) );
       loading.start();
        if( $('#video_url').length >= 1  &&  $('#video_url').val().length > 0){
            if( can_submit == false){
                $('#video_url').trigger('input');
                loading.stop();
                return false;
            }else{
                return true;
            }
        }
        return true;
    });

	/**
	 * アップロードのファイルを削除します
	 */
	$("#clear_video_url").click(function(){
		clearURL();
	});
});
function startDetectUrl(){
    can_detect_url = true;
    $('#video_url').trigger('input');
}

function clearURL(){
	$('#video_url').val('');
	$('.urlive-container').empty();
    $('.submit').prop('disabled', true);
}

/**
 * アップロードの画像を取ります
 */
function readImg(input , image_preview_id) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#'+image_preview_id).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * アップロードファイルを削除します
 */
function clearImgFile(image_preview_id){
	$('#'+image_preview_id).attr('src', '#');
}

/**
 * URL画像の試写を使用禁止です
 */
function disabledUrlPreviewInput(){
    $('#video_url').prop('disabled', true);
    $('#preview_url_attach').html('<i class="icon-spinner icon-spin"></i>');
    $('.submit').prop('disabled', true);
}

/**
 * URLの画像の試写を使用可能です
 */
function enabledUrlPreviewInput(){
    $('#video_url').prop('disabled', false);
    $('#preview_url_attach').html('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>');
    $('.submit').prop('disabled', false);
}