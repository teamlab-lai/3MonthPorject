
<?php echo $this->getContent(); ?>

<?php echo $this->tag->form(array('post/create', 'method' => 'post', 'enctype' => 'multipart/form-data')); ?>
	<fieldset>

		<?php foreach ($form as $element) { ?>
			<div align="left">
    			<?php if ($element->getName() == 'url_preview') { ?>
    				<h6>まとめの表紙画像を設定</h6>
    			<?php } elseif ($element->getName() == 'file_upload') { ?>
    				<h6 id="madaha_title">または</h6>
    			<?php } ?>
			</div>
			<div class="control-group padding <?php echo $element->getName(); ?>">
			    <?php echo $element->label(array('class' => 'control-label hidden')); ?>
			    <div class="controls">
			    	<div class="row">
				    	<?php if ($element->getName() == 'url_preview' || $element->getName() == 'file_upload') { ?>

							<?php if ($element->getName() == 'url_preview') { ?>
				    			<div class="col-xs-8 form-group no-padding">
				    				<?php echo $element; ?>
				    			</div>
				    		<?php } else { ?>
				    			<div class="col-xs-7 form-group no-padding">
									<div href = "#" class = "thumbnail">
										<img id="image_preview" name="image_preview" class="center-pic short-img" src = '#' alt = "" onerror="this.src='<?php echo $this->url->get('img/default-img.jpg'); ?>'">
									</div>
								</div>
							<?php } ?>
			    			<?php if ($element->getName() == 'url_preview') { ?>
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

							<?php } else { ?>
								<div class="col-xs-5 form-group no-padding">
									<div class="col-xs-7 no-padding">
										<span class="btn btn-default btn-file glyphicon glyphicon-upload form-control" aria-hidden="true">
										    <?php echo $element; ?>
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
							<?php } ?>
				    	<?php } else { ?>
			    			<div class="form-group">
			    				<?php echo $element; ?>
			    			</div>
				    	<?php } ?>
			    	</div>
			    </div>
			</div>

		<?php } ?>

		<div class="control-group ">
		    <?php echo $this->tag->submitButton(array('投稿する', 'class' => 'btn btn-primary js-submit')); ?>
		</div>
	</fieldset>

</form>