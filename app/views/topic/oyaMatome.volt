{{ content() }}
<div class="row ">
	<div class="col-xs-12">
		<div class="col-xs-12 color-white margin-top-10 padding-10">
		{% if auth['isAdmin'] == true or topic.user_fb_id == auth['id'] %}
			<div class="col-xs-4 pull-right no-padding padding-bottom-10">
				<button type="button" class="btn btn-default  btn-xs pull-right" data-toggle="modal" data-target="#confirm-delete" aria-label="Left Align">
					<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
				</button>
			</div>
		{% endif %}
			<div class="col-xs-12 have-border ">
				<div class="col-xs-4  no-padding">
					<div href = "#" class = "thumbnail">
						{% if topic.picture_url  is defined %}
							<img class="center-pic" src = {{ topic.picture_url }} alt = "">
						{% elseif topic.video_thumbnail_url is defined %}
							<img class="center-pic" src = {{ topic.video_thumbnail_url }} alt = "">
						{% elseif topic.user_picture_url is defined %}
							<img class="center-pic" src = {{ topic.user_picture_url }} alt = "">
						{% else %}
							<img class="center-pic" src = '/matome/img/default-page.png' alt = "">
						{% endif %}
					</div>
				</div>
				<div class="col-xs-8 margin-top-20 ">
					<h6 class="topic-title visible-xs" align="left">{{ topic.title }}</h6>
					<h2 class="topic-title hidden-xs" align="left">{{ topic.title }}</h2>
				</div>

				{% if topic.embed_video_url != null %}
					<div class="col-xs-12">
						<div class="embed-responsive embed-responsive-16by9">
					        <iframe class="embed-responsive-item" src="{{ topic.embed_video_url }}"></iframe>
					    </div>
				    </div>
				{% endif %}

				<div class="col-xs-12 no-padding font-gray">
					<small class="visible-xs">{{ topic.description }}</small>
					<span class="hidden-xs">{{ topic.description }}</span>
				</div>
			</div>

			<div class="col-xs-12 margin-top-20 margin-bottom-10">
				<a href="javascript:void(0);" class="a-container">
					<div class="col-xs-6 no-padding vertical-center">
						<div class="col-xs-12 text-center no-padding vertical-center md-size">
							<span>{{ topic.user_name }}</span>
						</div>
						<div class="col-xs-12 text-center no-padding vertical-center visible-xs small-size font-gray">
							<span>最後更新日:</span>
							<small>{{ date('Y年m月d日', topic.update_time | strtotime) }}</small>
						</div>
						<div class="col-xs-12 text-center no-padding vertical-center hidden-xs font-gray">
							<span>最後更新日:</span>
							<small>{{ date('Y年m月d日', topic.update_time | strtotime) }}</small>
						</div>
					</div>
				</a>
				{{ link_to("comment/index/" ~ topic.page_id ,
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
				<a href="javascript:void(0);"  data-loading-text="Loading..." class="font-gray like-button js-dislike" data-id="{{ topic.page_id }}"><small>いいね!を取り消す</small></a>
			{% else %}
				<a href="javascript:void(0);" data-loading-text="Loading..." class="font-gray like-button js-like" data-id="{{ topic.page_id }}"><small>いいね!</small></a>
			{% endif %}
		</div>
		<div class="col-xs-12" id="likes_area">
		{% if likes > 0 %}
			<small>{{ likes }}がいいね！と言っています</small>
		{% endif %}
		</div>
	</div>
</div>
<div class="navbar navbar-inverse navbar-fixed-bottom" >
	<div class="container-fluid">
		<div class="nav-collapse">
			<ul class="nav navbar-nav" >
				<li id="fav_controller">
					{% if is_fav == false %}
						<a href="javascript:void(0);" class="js-add-fav" data-id='{{ topic.page_id }}'>
							<i class="pe-7s-star "></i>
							<p class="sm-size">お気に入り追加</p>
						</a>
					{% else %}
						<a href="javascript:void(0);" class="js-del-fav" data-id='{{ topic.page_id }}'>
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
                <p>このまとめトッピングを削除しますか？</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
                <a href="javascript:void(0);" class="btn btn-danger btn-ok ladda-button" data-style="zoom-in"><span class="ladda-label" onclick="delMatome('{{ topic.page_id }}');">はい</span></a>
            </div>
        </div>
    </div>
</div>