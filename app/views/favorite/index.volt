
{{ content() }}

{% set same_day = '' %}
<div class="list-group">
{% if page.items == null %}
	<h6>お気に入りはまだありません。</h6>
{% endif %}
{% for fav in page.items  %}
	{% if same_day != date('Y年m月d日', fav.update_time | strtotime) %}
		<label>{{ date('Y年m月d日', fav.update_time | strtotime) }}</label>
		{% set same_day = date('Y年m月d日', fav.update_time | strtotime) %}
	{% endif %}
		<div class="list-group-item" id="{{ fav.id }}">
			<button type="button" class="btn btn-default pull-right gray-color"  onclick="delFav('{{ fav.id }}','{{ fav.page_id }}')">
			  <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			</button>
			<a href="{{ url("topic/index/" ~ fav.page_id) }}" class="fav-list">
				<div class="row">
					<div class="col-xs-9">
						<h6>{{ fav.title }}</h6>
						{% if fav.video_url != null  %}
							<small>{{ fav.video_url }}</small>
						{% else %}
							<small>{{ fav.user_name }}</small>
						{% endif %}
					</div>
				</div>
			</a>
		</div>
	{% endfor  %}
</div>
{% if page.total_pages > 1 %}
	<div class="col-xs-12 text-center">
	    <div class="btn-group" role="group" >
	        {{ link_to("favorite/index", '<i class="icon-fast-backward"></i> 1', "class": "btn btn-default") }}
	        {{ link_to("favorite/index?page=" ~ page.before, '<i class="icon-step-backward"></i> 前の', "class": "btn btn-default") }}
	        <a href="javascript:void(0);" class="btn btn-default">{{ page.current ~ '/' ~ page.total_pages }} </a>
	        {{ link_to("favorite/index?page=" ~ page.next, '<i class="icon-step-forward"></i> 次の', "class": "btn btn-default") }}
	        {{ link_to("favorite/index?page=" ~ page.last, '<i class="icon-fast-forward"></i> ' ~ page.total_pages, "class": "btn btn-default") }}
	    </div>
	</div>
{% endif %}