
{{ content() }}
<div class="row ">
	<div class="col-xs-12 color-black padding-top-down-10">
		<div class="col-xs-4 ">
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
		<div class="col-xs-8">
			<h6 class="topic-title visible-xs" align="left">{{ topic.title }}</h6>
			<h2 class="topic-title hidden-xs" align="left">{{ topic.title }}</h2>
			<div class="col-xs-9 no-padding">
				<div class="col-xs-6 view-count no-padding">
					<div class="visible-xs pull-left" >
						<span class="glyphicon glyphicon-eye-open"></span>
						<small >{{ topic.views }}</small>
					</div>
					<div class="hidden-xs">
						<span class="glyphicon glyphicon-eye-open float-type"></span>
						<h6 align="left">{{ topic.views }}</h6>
					</div>
				</div>
				<div class="col-xs-6 fav-count no-padding">
					<div class="visible-xs pull-left">
						<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
						<small class="favorite-count">{{ topic.favorite_count }}</small>
					</div>
					<div class="hidden-xs">
						<span class="glyphicon glyphicon-star float-type"></span>
						<h6 align="left" class="favorite-count">{{ topic.favorite_count }}</h6>
					</div>
				</div>
				<div class="col-xs-12 update-time no-padding">
					<div class="visible-xs pull-left">
						<span>最後更新日:</span>
						<small>{{ date('Y年m月d日', topic.update_time | strtotime) }}</small>
					</div>
					<div class="hidden-xs pull-left">
						<span>最後更新日:</span>
						<small>{{ date('Y年m月d日', topic.update_time | strtotime) }}</small>
					</div>
				</div>
			</div>
			<div class="col-xs-3 ">
				<div class="btn-group" role="group" aria-label="">
					{{ link_to("location/index/" ~ topic.page_id , '<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-map-marker"></span></button>') }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 color-gray">

		{% if topic.user_picture_url == null  %}
		{% set topic.user_picture_url = '/matome/img/default-page.png' %}
		{% endif %}
		{{ link_to("topic/oyaMatome/" ~ topic.page_id,
					'class':"a-container",
					'<div class="col-xs-6 no-padding vertical-center">
				 		<div class="col-xs-4 no-padding vertical-center">
							<div href = "#" class = "thumbnail sm-margin">
							<img class="center-pic" src = ' ~ topic.user_picture_url ~ ' alt = "">
							</div>
						</div>
						<div class="col-xs-7 text-center no-padding vertical-center font-gray md-size">
							<span>' ~ topic.user_name ~ 'さん</span>
						</div>
					</div>'
			)
		}}
		{{ link_to("comment/index/" ~ topic.page_id ,
				"class":"a-container",
				'<div class="col-xs-5 text-center no-padding vertical-center">
					<div class="btn-group btn-group-xs" role="group" aria-label="">
						<button type="button" class="btn btn-default">
							このまとめに投稿する
							<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
						</button>
					</div>
				</div>'
		) }}
	</div>
</div>
<div class="row">
	<div class="col-xs-12 ">
		<div class="list-group">
			{% for comment in page.items %}
			<div class="list-group-item no-border no-background-color have-bottom-line comment-items" data-link='{{ url('topic/koMatome/' ~ comment.comment_id) }}'>
				<div class="row text-center" >
				{% if comment.url_comment != null  %}
					<h6>{{ comment.url_comment }}</h6>
					{{ link_to(comment.url_comment ,"class":"font-gray md-size", 'リンクをオープンする' ,false )  }}
				{% elseif comment.picture_url != null %}
					<h6>{{ comment.picture_title }}</h6>
						<a href="javascript:void(0);" class="thumbnail">
					    	<img src="{{ comment.picture_thumbnail_url }}" alt="">
					    </a>
				{% elseif comment.video_url != null %}
					<h6>{{ comment.video_title }}</h6>
					{% if comment.video_type == 'video'  %}
						<div class="embed-responsive embed-responsive-16by9">
	                        <iframe class="embed-responsive-item" src="{{ comment.video_url }}"></iframe>
	                    </div>
					{% elseif comment.video_type == 'website' %}
						{% if comment.video_thumbnail_url != null %}
							<div href = "#" class = "thumbnail">
								<img class="center-pic" src = {{ comment.video_thumbnail_url }} alt = "">
							</div>
						{% endif %}
						{{ link_to(comment.video_url ,"class":"font-gray md-size", "リングを" ,false )  }}
					{% else %}
						<div class="col-xs-12">
							<a href="javascript:void(0);" class="thumbnail">
						    	<img src="{{ comment.video_thumbnail_url }}" alt="">
						    </a>
						</div>
					{% endif  %}
				{% elseif comment.text_comment != null %}
					<h6>{{ comment.text_comment }}</h6>
				{% else %}

				{% endif %}
				</div>
			</div>
			{% endfor %}
		</div>
	</div>
</div>
<div class="navbar navbar-inverse navbar-fixed-bottom" >
	<div class="container-fluid">
		<div class="nav-collapse">
			<ul class="nav navbar-nav" >
			{% if page.total_pages > 1 %}
				<li>
					{{ link_to("topic/index/" ~ topic.page_id ~ "?page=" ~ page.before, '<i class="pe-7s-angle-left"></i><p class="sm-size">前のコメント</p>') }}
			{% endif %}
				</li>
				{% if auth != null %}
					<li id="fav_controller">
						{% if is_fav == false %}
							<a href="javascript:void(0);" class="js-add-fav" data-id='{{ topic.page_id }}' >
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
				{% endif %}
			{% if page.total_pages > 1 %}
				<li>
					{{ link_to("topic/index/" ~ topic.page_id ~ "?page=" ~ page.next, '<i class="pe-7s-angle-right"></i><p class="sm-size">次のコメント</p>') }}
				</li>
			{% endif %}
			</ul>
		</div>
	</div>
</div>
