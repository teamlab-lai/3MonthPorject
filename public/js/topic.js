$(function() {

	var my_favorite = new myFavorite();

    $('.comment-items').on('click', function() {
    	//$(this).hide();
    	//$(this).after('<div class="col-xs-12 text-center"><i class="icon-spinner icon-spin icon-large large-2x"></i></div>');
        window.location.href = $(this).data('link');
    });


	$(document).on('click', '.js-add-fav', function(){
		my_favorite.addFav($(this).data('id'));
	});

	$(document).on('click', '.js-del-fav', function(){
		my_favorite.delFav($(this).data('id'));
	});
});


