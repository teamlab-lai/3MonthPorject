
{{ content() }}

<div class="row">
    <div class="col-xs-12 pull-left">
            <h5 class="visible-xs">まとめ</h5>
            <h3 class="hidden-xs">まとめ</h3>
    </div>
</div>
<div class="row ">
    {% if page.items is defined %}
        <div class="list-group">
        {% for topic in page.items  %}
            <a href={{ url("topic/index/" ~ topic.page_id) }} class="list-group-item">
                <div class="row reset-margin-right">
                    <div class="col-xs-11 col-xs-offset-1 reset-col-xs-offset-1">
                        <div class="col-xs-4 col-md-2">
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
                        <div class="col-xs-8 col-md-10 no-padding">
                            <div class="matome-title col-xs-12 no-padding">
                                {% if topic.title  is defined %}
                                    <h2 class="matome-titile mobile-size title visible-xs">{{  topic.title  }}</h2>
                                    <h3 class="matome-titile hidden-xs">{{ topic.title }}</h3>
                                {% endif %}
                            </div>
                            <div class="col-xs-12 no-padding">
                                <div class="col-xs-4 col-md-4 no-padding" >
                                    <h6 class="mobile-size views visible-xs">
                                        <span class="glyphicon glyphicon-eye-open"></span>
                                        <small>
                                            {% if topic.views  is defined %}
                                                {{ topic.views }}
                                            {% else %}
                                                {{ 0 }}
                                            {% endif %}
                                        </small>
                                    </h6>
                                    <h6 class="hidden-xs">
                                        <span class="glyphicon glyphicon-eye-open"></span>
                                        <small>
                                            {% if topic.views  is defined %}
                                                {{ topic.views }}
                                            {% else %}
                                                {{ 0 }}
                                            {% endif %}
                                        </small>
                                    </h6>
                                </div>
                                <div class="col-xs-8 col-md-8 no-padding">
                                    <h6 class="mobile-size date-time visible-xs">
                                        最後更新日:
                                        <small>
                                            {% if topic.update_time  is defined %}
                                                {{ date('Y年m月d日', topic.update_time | strtotime) }}
                                            {% endif %}
                                        </small>
                                    </h6>
                                    <h6 class="hidden-xs">
                                        最後更新日:
                                        <small>
                                            {% if topic.update_time  is defined %}
                                                {{ date('Y年m月d日', topic.update_time | strtotime) }}
                                            {% endif %}
                                        </small>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        {% endfor  %}
        </div>
    {% endif %}
    {% if page.total_pages > 1 %}
        <div class="col-xs-12 text-center">
            <div class="btn-group" role="group" >
                {{ link_to("index/index", '<i class="icon-fast-backward"></i> 1', "class": "btn btn-default") }}
                {{ link_to("index/index?page=" ~ page.before, '<i class="icon-step-backward"></i> 前の', "class": "btn btn-default") }}
                <a href="javascript:void(0);" class="btn btn-default">{{ page.current ~ '/' ~ page.total_pages }} </a>
                {{ link_to("index/index?page=" ~ page.next, '<i class="icon-step-forward"></i> 次の', "class": "btn btn-default") }}
                {{ link_to("index/index?page=" ~ page.last, '<i class="icon-fast-forward"></i> ' ~ page.total_pages, "class": "btn btn-default") }}
            </div>
        </div>
    {% endif %}
</div>
