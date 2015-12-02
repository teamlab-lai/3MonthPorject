{{ content() }}
<div class="row ">
	<div class="col-xs-12">
		<div class="col-xs-12 color-white margin-top-10 padding-10">
		{% if auth['isAdmin'] == true or comment.user_fb_id == auth['id'] %}
			<div class="col-xs-4 pull-right no-padding padding-bottom-10">
				<button type="button" class="btn btn-default  btn-xs pull-right" data-toggle="modal" data-target="#confirm-delete" aria-label="Left Align">
					<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
				</button>
			</div>
		{% endif %}
			<div class="col-xs-12 have-border ">
				<div class="col-xs-12  no-padding">
					{% if comment.url_comment != null %}
						{{ link_to( comment.url_comment , comment.url_comment , false ) }}
					{% elseif comment.picture_thumbnail_url != null %}
						{% if comment.picture_title != null %}
							<h6>{{ comment.picture_title }}</h6>
						{% endif %}
							<div href = "#" class = "thumbnail">
								<img class="center-pic" src = {{ comment.picture_thumbnail_url }} alt = "">
							</div>
						{% if comment.picture_description != null %}
							<small>{{ comment.picture_description }}</small>
						{% endif %}
					{% elseif comment.video_url != null %}
						{% if comment.video_title != null %}
							<h6>{{ comment.video_title }}</h6>
						{% endif %}
						{% if comment.video_type == 'video'  %}
							<div class="embed-responsive embed-responsive-16by9">
		                        <iframe class="embed-responsive-item" src="{{ comment.video_url }}"></iframe>
		                    </div>
						{% elseif comment.video_type == 'photo' %}
							<div href = "#" class = "thumbnail">
								<img class="center-pic" src = {{ comment.video_thumbnail_url }} alt = "">
							</div>
						{% elseif comment.video_type == 'website' %}
							{% if comment.video_thumbnail_url != null %}
								<div href = "#" class = "thumbnail">
									<img class="center-pic" src = {{ comment.video_thumbnail_url }} alt = "">
								</div>
							{% endif %}
							{{ link_to( comment.video_url , "リング" , false ) }}
						{% endif %}
						{% if comment.video_description != null %}
							<small>{{ comment.video_description }}</small>
						{% endif %}
					{% else %}
						<small>{{ comment.text_comment }}</small>
					{% endif %}
				</div>
			</div>
			<div class="col-xs-12 margin-top-20 margin-bottom-10">
				<a href="javascript:void(0);" class="a-container">
					<div class="col-xs-6 text-center no-padding vertical-center">
						<div class="col-xs-12 no-padding vertical-center md-size">
							<span>{{ comment.user_name }}</span>
						</div>
						<div class="col-xs-12 text-center no-padding vertical-center visible-xs small-size font-gray">
							<span>最後更新日:</span>
							<small>{{ date('Y年m月d日', comment.update_time | strtotime) }}</small>
						</div>
						<div class="col-xs-12 text-center no-padding vertical-center hidden-xs font-gray">
							<span>最後更新日:</span>
							<small>{{ date('Y年m月d日', comment.update_time | strtotime) }}</small>
						</div>
					</div>
				</a>
				{{ link_to("comment/index/" ~ comment.page_id ,
							"class":"a-container",
							'<div class="col-xs-5 text-center no-padding vertical-center">
								<div class="btn-group btn-group-xs" role="group" aria-label="">
									<button type="button" class="btn btn-default">
										<small>このまとめに投稿する</small>
										<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
									</button>
								</div>
							</div>'
					) }}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 padding-10 font-gray">
		<div class="col-xs-12 " id="fb_like_btn">
			{% if is_liked == true %}
				<a href="javascript:void(0);" class="font-gray like-button js-dislike" data-id="{{ comment.comment_id }}"><small>いいね!を取り消す</small></a>
			{% else %}
				<a href="javascript:void(0);" class="font-gray like-button js-like" data-id="{{ comment.comment_id }}"><small>いいね!</small></a>
			{% endif %}
		</div>
		<div class="col-xs-12" id="likes_area">
		{% if likes > 0 %}
			<small>{{ likes }}がいいね！と言っています</small>
		{% endif %}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 ">
		<div class="list-group">
		{% if comment_box['result'] != false %}
			{% for comment_detail in comment_box['comments'] %}
				<div class="list-group-item no-border no-background-color have-bottom-line">
					<div class="row  ">
						<div class="col-xs-3">
							<a href="javascript:void(0);" class="thumbnail no-margin no-border ">
								{% if comment_detail['user_photo'] == null %}
									<img src="/matome/img/default-page.png" alt="">
								{% else %}
									<img src="{{ comment_detail['user_photo'] }}" alt="">
								{% endif %}
							</a>
						</div>
						<div class="col-xs-9 no-padding">
							<div class="col-xs-12">
								<a href="javascript:void(0);">
									<small>{{ comment_detail['user_name'] }}</small>
								</a>
							</div>
							<div class="col-xs-12 comment-message">
								{% if comment_detail['message'] != null %}
									<span >{{ comment_detail['message'] }}</span>
								{% elseif comment_detail['url_message'] != null %}
									{{ link_to( comment_detail['url_message'] , '<span>' ~ comment_detail['url_message'] ~'</span>', false) }}
								{% endif %}
							</div>
							{% if comment_detail['thumbnail_picture'] != null and (comment_detail['type'] == 'video' or comment_detail['type'] == 'photo' or comment_detail['type'] == 'website') %}
								<div class="col-xs-6  ">
									<a href="{{ comment_detail['url'] }}" class="thumbnail no-margin no-border ">
										{% if comment_detail['type'] == 'video' %}
											<div class="caption">
							                    <i class="glyphicon glyphicon-play-circle"></i>
							                </div>
										{% endif %}
								    	<img src="{{ comment_detail['thumbnail_picture'] }}" alt="">
								    </a>
							    </div>
							{% endif %}
						</div>
					</div>
				</div>
			{% endfor %}
		{% endif %}
		<div class="list-group-item no-border no-background-color js-new-comment-block">
			<div class="row ">
				<div class="col-xs-3">
					<a href="javascript:void(0);" class="thumbnail no-margin no-border ">
						{% if user_info['user_photo'] == null %}
							<img src="/matome/img/default-page.png" alt="">
						{% else %}
							<img src="{{ user_info['user_photo'] }}" alt="">
						{% endif %}
					</a>
				</div>
				<div class="col-xs-9 no-padding">
					<div class="col-xs-12">
						<a href="javascript:void(0);">
							<small>{{ user_info['user_name'] }}</small>
						</a>
					</div>
					<div class="col-xs-12">
						<div class="input-group input-group-sm">
							<input type="text" class="form-control js-comment-input" placeholder="コメントする" aria-describedby="sizing-addon3" data-id="{{ comment.comment_id }}">
							<div class="input-group-btn">
	 							<button type="button" class="btn btn-default btn-xs js-comment-submit">
									<span class="glyphicon glyphicon-pencil js-input-submit-icon" aria-hidden="true"></span>
								</button>
  							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="navbar navbar-inverse navbar-fixed-bottom" >
	<div class="container-fluid">
		<div class="nav-collapse">
			<ul class="nav navbar-nav" >
				<li id="fav_controller">
					{% if is_fav == false %}
						<a href="javascript:void(0);"  class="js-add-fav" data-id='{{ comment.page_id }}'>
							<i class="pe-7s-star "></i>
							<p class="sm-size">お気に入り追加</p>
						</a>
					{% else %}
						<a href="javascript:void(0);" class="js-del-fav" data-id='{{ comment.page_id }}'>
							<i class="pe-7s-star is-fav"></i>
							<p class="sm-size">お気に入り削除</p>
						</a>
					{% endif %}

				</li>
			</ul>
		</div>
	</div>
</div>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">削除確認</h4>
            </div>

            <div class="modal-body">
                <p>このまとめコメントを削除しますか？</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
                <a href="javascript:void(0);" class="btn btn-danger btn-ok ladda-button" data-style="zoom-in"><span class="ladda-label" onclick="delComment('{{ comment.comment_id }}');">はい</span></a>
            </div>
        </div>
    </div>
</div>