

{{ content() }}
<div class="row ">
    {% if topics is defined %}
        <div class="list-group">
        {% for topic in topics  %}
            <a href={{ url("search/history/" ~ topic.page_id) }} class="list-group-item">
                <div class="row reset-margin-right">
                    <div class="col-xs-11 col-xs-offset-1 reset-col-xs-offset-1">
                        <div class="col-xs-4 col-md-2">
                            <div href = "#" class = "thumbnail">
                                {% if topic.picture_url  is defined %}
                                    <img class="center-pic" src = {{ topic.picture_url }} alt = "">
                                {% elseif topic.video_thumbnail_url is defined %}
                                    <img class="center-pic" src = {{ topic.video_thumbnail_url }} alt = "">
                                {% else %}
                                    <img class="center-pic" src = {{ topic.user_picture_url }} alt = "">
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
</div>