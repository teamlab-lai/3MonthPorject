<div class="row ">
	<div class="col-xs-12 color-black padding-top-down-10">
		<div class="col-xs-4 ">
			<div href = "#" class = "thumbnail sm">
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
			<div class="col-xs-12 no-padding">
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
		</div>
	</div>
</div>

{{ flash.output() }}
{{ content() }}
