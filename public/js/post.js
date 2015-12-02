var can_submit = false;
var can_detect_url = false;
$(function() {

	/**
	 * ファイルをアップロード
	 */
	$("#file_upload").change(function(){
	    readImg(this);
	});

	/**
	 * アップロードのファイルを削除します
	 */
	$("#clear_image").click(function(){
		clearImgFile();
	});

	if($('#url_preview').val().length >= 1 ){
		hideFilePreview();
	}

	$('#url_preview').focus(function(){
		if( $(this).val().length >= 1 ){
			hideFilePreview();
		}else{
			showFilePreview();
		}
	});
	var typingTimer;                //timer identifier
	var doneTypingInterval = 2000;  //time in ms, 5 second for example

	$('#url_preview').keyup(function(){
		can_detect_url = false;
	    clearTimeout(typingTimer);
	    if ($('#url_preview').val) {
	    	typingTimer = setTimeout(startDetectUrl, doneTypingInterval);
	    }else{
	    	can_detect_url = false;
	    }
	});

    $('#url_preview').keydown(function(){
        can_detect_url = false;
    });

	$('#url_preview').on('input propertychange', function () {
		if(can_detect_url == false){
			$('#url_preview').trigger('keyup');
			return;
		}
		$('.urlive-container').urlive('remove');
		if($('#url_preview').val().length >= 1){
			hideFilePreview();
		}
		can_submit = false;
	    $('#url_preview').urlive({
	        callbacks: {
	            onStart: function () {
	            	disabledUrlPreviewInput();
	            	$('.urlive-container').urlive('remove');
	            },
	            onSuccess: function (data) {
	            	enabledUrlPreviewInput();
	            	$('.urlive-container').urlive('remove');
		            can_submit = true;
	            },
	            noData: function () {
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
        $('#url_preview').trigger('input');
    });

    $('form').submit(function(e){
    	var loading = Ladda.create( document.querySelector( '.js-submit' ) );
    	loading.start();
    	if($('#url_preview').val().length > 0){
    		if( can_submit == false){
	    		$('#url_preview').trigger('input');
	    		loading.stop();
	    		return false;
	    	}
	    }
    });

	/**
	 * アップロードのファイルを削除します
	 */
	$("#clear_url").click(function(){
		clearURL();
	});

});

function startDetectUrl(){
	can_detect_url = true;
	$('#url_preview').trigger('input');
}

/**
 * アップロードの画像を取ります
 */
function readImg(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#image_preview').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
        hideUrlPreview();
    }
}

/**
 * ファイルを査察します
 */
function imgCheck(file){

	//タイプを査察します
	var ext = $(file).val().split('.').pop().toLowerCase();
	if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
		alert("JPG、JPEG、GIF、PNGファイルだけアップロードしてください。");
	    return false;
	}

	//サイズを査察します
	var f = file.files[0];
	var file_size = (f.size||f.fileSize);
	if(file_size >= 1024000){
		alert("1024KB未満のファイルだけアップロードしてください。");
		return false;
	}

	return true;
}

/**
 * アップロードファイルを削除します
 */
function clearImgFile(){
	$('#image_preview').attr('src', '#');
	showUrlPreview();
}

/**
 *URLの画像の試写のところを隠します
 */
function hideUrlPreview(){
	clearURL();
	$('#madaha_title').hide();
    $('.url_preview').hide();
}

/**
 *URLの画像の試写のところを見せます
 */
function showUrlPreview(){
	$('#madaha_title').show();
	$('.url_preview').show();
}

/**
 *アプロードファイルのところを隠します
 */
function hideFilePreview(){
	clearImgFile()
	$('#madaha_title').hide();
    $('.file_upload').hide();
}

/**
 *アプロードファイルのところを見せます
 */
function showFilePreview(){
	$('#madaha_title').show();
    $('.file_upload').show();
}

/**
 *URLのところをクリーンします
 */
function clearURL(){
	$('#url_preview').val('');
	$('.urlive-container').empty();
	showFilePreview();
}

/**
 * URL画像の試写を使用禁止です
 */
function disabledUrlPreviewInput(){
    $('#url_preview').prop('disabled', true);
    $('#preview_url_attach').html('<i class="icon-spinner icon-spin"></i>');
    hideFilePreview();
    $('#clear_url').addClass('disabled');
}

/**
 * URLの画像の試写を使用可能です
 */
function enabledUrlPreviewInput(){
    $('#url_preview').prop('disabled', false);
    $('#preview_url_attach').html('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>');
    $('#clear_url').removeClass('disabled');
}
