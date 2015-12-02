
{{ content() }}

{{ form('post/create', 'method': 'post', 'enctype': "multipart/form-data") }}
	<fieldset>

		{% for element in form %}
			<div align="left">
    			{% if element.getName() == 'url_preview' %}
    				<h6>まとめの表紙画像を設定</h6>
    			{% elseif element.getName() == 'file_upload' %}
    				<h6 id="madaha_title">または</h6>
    			{% endif %}
			</div>
			<div class="control-group padding {{ element.getName() }}">
			    {{ element.label(['class': 'control-label hidden']) }}
			    <div class="controls">
			    	<div class="row">
				    	{% if element.getName() == 'url_preview' or element.getName() == 'file_upload' %}

							{% if element.getName() == 'url_preview' %}
				    			<div class="col-xs-8 form-group no-padding">
				    				{{ element }}
				    			</div>
				    		{% else %}
				    			<div class="col-xs-7 form-group no-padding">
									<div href = "#" class = "thumbnail">
										<img id="image_preview" name="image_preview" class="center-pic short-img" src = '#' alt = "" onerror="this.src='{{ url('img/default-img.jpg') }}'">
									</div>
								</div>
							{% endif %}
			    			{% if element.getName() == 'url_preview' %}
				    			<div class="col-xs-4 form-group no-padding">
				    				<div class="col-xs-6 form-group no-padding">
						    			<button type="button" id="preview_url_attach" class="btn btn-default form-control" aria-label="Left Align">
											<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
										</button>
									</div>
									<div class="col-xs-6 form-group no-padding">
										<button id="clear_url" type="button" class="btn btn-default  form-control" aria-label="Left Align" style="text-align: center;">
											<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
										</button>
									</div>
								</div>
								<div class="urlive-container"></div>

							{% else %}
								<div class="col-xs-5 form-group no-padding">
									<div class="col-xs-7 no-padding">
										<span class="btn btn-default btn-file glyphicon glyphicon-upload form-control" aria-hidden="true">
										    {{ element }}
										</span>
									</div>
									<div class="col-xs-5 no-padding">
										<button id="clear_image" type="button" class="btn btn-default  form-control" aria-label="Left Align" style="text-align: center;">
											<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
										</button>
									</div>
									<div class="col-xs-12 no-padding">
										<h6><small>1024KB未満のJPG、JPEG、GIF、PNGファイルだけアップロードできます。</small></h6>
									</div>
								</div>
							{% endif %}
				    	{% else %}
			    			<div class="form-group">
			    				{{ element }}
			    			</div>
				    	{% endif %}
			    	</div>
			    </div>
			</div>

		{% endfor %}

		<div class="control-group ">
			<button class="btn btn-primary ladda-button js-submit" data-style="zoom-in"><span class="ladda-label">投稿する</span></button>
		</div>
	</fieldset>

</form>