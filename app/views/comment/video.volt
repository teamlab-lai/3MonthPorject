
{{ content() }}
<ul class="nav nav-tabs">
  <li role="presentation" id="link_tab" class="komatome-tab " >
	{{ link_to("comment/index/" ~ topic.page_id , 'リンク') }}
  </li>
  <li role="presentation" id="pic_tab" class="komatome-tab">
  	{{ link_to("comment/picture/" ~ topic.page_id , '画像') }}
  </li>
  <li role="presentation" id="video_tab" class="komatome-tab active">
	{{ link_to("javascript:void(0);", '動画' , false) }}
  </li>
  <li role="presentation" id="text_tab" class="komatome-tab">
  	{{ link_to("comment/text/" ~ topic.page_id , 'テキスト') }}
  </li>
</ul>
	{{ form('comment/createVideo/' ~ topic.page_id, 'method': 'post', 'enctype': "multipart/form-data" , 'id':"comment_form") }}
		<fieldset>

			<div class="tab-content">
		        <div class="tab-pane active" id="video_area">
		            <div class="row padding-top-down-10">
						{% for element in form %}
							{% if element.getName() == 'video_url' %}
								<div class="col-xs-7 form-group">
				    				{{ element }}
				    			</div>
								<div class="col-xs-5 form-group">
				    				<div class="col-xs-6 form-group no-padding">
						    			<button type="button" id="preview_url_attach" class="btn btn-default form-control" aria-label="Left Align">
											<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
										</button>
									</div>
									<div class="col-xs-6 form-group no-padding">
										<button id="clear_video_url" type="button" class="btn btn-default  form-control" aria-label="Left Align" style="text-align: center;">
											<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
										</button>
									</div>
								</div>

							{% else %}
								<div class="col-xs-12 ">
									{{ element }}
								<hr>
								</div>
							{% endif %}
						{% endfor %}
					</div>
		        </div>
		    </div>
			<div class="urlive-container"></div>
		    <div class="col-xs-12 text-center">
		    	<hr>
				<div class="control-group ">
					<button class="btn btn-primary ladda-button js-submit" data-style="zoom-in"><span class="ladda-label">投稿する</span></button>
				</div>
		    </div>
		</fieldset>

	</form>

