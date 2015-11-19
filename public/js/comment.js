var can_submit = false;
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

	$('#video_url').preview({
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
            $('.submit').prop('disabled', true);
        }).on('loaded', function() {
            $(this).prop('disabled', false);
            $('#preview_url_attach').html('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>');
            $('.submit').prop('disabled', false);
            can_submit = true;
        })

    $('#preview_url_attach').bind('click', function(e) {
        $('#video_url').trigger('preview');
    });

    $('#video_form').submit(function(e){
        if($('#video_url').val().length > 0){
            if( can_submit == false){
                $('#video_url').trigger('preview');
                return false;
            }else{
                return true;
            }
        }
    });

	/**
	 * アップロードのファイルを削除します
	 */
	$("#clear_video_url").click(function(){
		clearURL();
	});
});


function clearURL(){
	$('#video_url').val('');
	$('.selector-wrapper').empty();
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