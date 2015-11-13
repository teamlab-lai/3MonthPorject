
<?php echo $this->getContent(); ?>
<ul class="nav nav-tabs">
  <li role="presentation" id="link_tab" class="komatome-tab" >
	<?php echo $this->tag->linkTo(array('comment/index/' . $topic->page_id, 'リンク')); ?>
  </li>
  <li role="presentation" id="pic_tab" class="komatome-tab active">
  	<?php echo $this->tag->linkTo(array('javascript:void(0);', '画像', false)); ?>
  </li>
  <li role="presentation" id="video_tab" class="komatome-tab">
	<?php echo $this->tag->linkTo(array('comment/video/' . $topic->page_id, '動画')); ?>
  </li>
  <li role="presentation" id="text_tab" class="komatome-tab">
  	<?php echo $this->tag->linkTo(array('comment/text/' . $topic->page_id, 'テキスト')); ?>
  </li>
</ul>
	<?php echo $this->tag->form(array('comment/createPicture/' . $topic->page_id, 'method' => 'post', 'enctype' => 'multipart/form-data')); ?>
		<fieldset>
			<div class="tab-content">
		        <div class="tab-pane active" id="pic_area">
		            <div class="row padding-top-down-10">
						<?php foreach ($form as $element) { ?>
							<?php if ($element->getName() == 'picture_url') { ?>
			            		<div class="col-xs-7 form-group ">
									<div href = "#" class = "thumbnail lg">
										<img id="image_preview" name="image_preview" class="center-pic short-img" src = '#' alt = "" onerror="this.src='<?php echo $this->url->get('img/default-img.jpg'); ?>'">
									</div>
								</div>
								<div class="col-xs-5 form-group">
									<div class="col-xs-7 no-padding">
										<span class="btn btn-default btn-file glyphicon glyphicon-upload form-control" aria-hidden="true">
										    <?php echo $element; ?>
										</span>
									</div>
									<div class="col-xs-5 no-padding">
										<button id="clear_picture_image" type="button" class="btn btn-default  form-control" aria-label="Left Align" style="text-align: center;">
											<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
										</button>
									</div>
									<div class="col-xs-12 no-padding">
										<h6><small>1024KB未満のJPG、JPEG、GIF、PNGファイルだけアップロードできます。</small></h6>
									</div>
								</div>
							<?php } else { ?>
								<div class="col-xs-12 ">
									<?php echo $element; ?>
								<hr>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
		        </div>
		    </div>
		    <div class="col-xs-12 text-center">
				<div class="control-group">
				    <?php echo $this->tag->submitButton(array('投稿する', 'class' => 'btn btn-primary')); ?>
				</div>
		    </div>
		</fieldset>

	</form>

