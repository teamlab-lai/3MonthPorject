var can_submit = false;
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
		//&& $(this).val() != ''
		console.log($(this).val().length);
		if( $(this).val().length >= 1 ){
			hideFilePreview();
		}else{
			showFilePreview();
		}
	});

	$('#url_preview').preview({
            key: 'd1d8a01558f548dbbaccadee4a079a9f', // Sign up for a key: http://embed.ly/pricing
            bind: true,
            query: {
                autoplay: 0,
                maxwidth: 1000
            }
        })
        .on('loading', function() {
            $(this).prop('disabled', true);
            $('#preview_url_attach').html('<i class="icon-spinner icon-spin"></i>');
            hideFilePreview();
            $('#clear_url').addClass('disabled');
        }).on('loaded', function() {
            $(this).prop('disabled', false);
            $('#preview_url_attach').html('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>');
            $('#clear_url').removeClass('disabled');
            can_submit = true;

        })

    $('#preview_url_attach').bind('click', function(e) {
        $('#url_preview').trigger('preview');
    });

    $('form').submit(function(e){
    	if($('#url_preview').val().length > 0){
    		if( can_submit == false){
	    		$('#url_preview').trigger('preview');
	    		return false;
	    	}else{
	    		return true;
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

function hideUrlPreview(){
	$('#madaha_title').hide();
    $('.url_preview').hide();
}

function showUrlPreview(){
	$('#madaha_title').show();
	$('.url_preview').show();
}

function hideFilePreview(){
	$('#madaha_title').hide();
    $('.file_upload').hide();
}

function showFilePreview(){
	$('#madaha_title').show();
    $('.file_upload').show();
}

function clearURL(){
	$('#url_preview').val('');
	$('.selector-wrapper').empty();
	showFilePreview();
}