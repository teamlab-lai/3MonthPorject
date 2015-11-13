
<?php echo $this->getContent(); ?>
<div class="row ">
	<div class="col-xs-12 color-black padding-top-down-10">
		<div class="col-xs-4 ">
			<div href = "#" class = "thumbnail sm">
				<?php if (isset($topic->picture_url)) { ?>
					<img class="center-pic" src = <?php echo $topic->picture_url; ?> alt = "">
				<?php } elseif (isset($topic->video_thumbnail_url)) { ?>
					<img class="center-pic" src = <?php echo $topic->video_thumbnail_url; ?> alt = "">
				<?php } elseif (isset($topic->user_picture_url)) { ?>
					<img class="center-pic" src = <?php echo $topic->user_picture_url; ?> alt = "">
				<?php } else { ?>
					<img class="center-pic" src = '/matome/img/default-page.png' alt = "">
				<?php } ?>
			</div>
		</div>
		<div class="col-xs-8">
			<h6 class="topic-title visible-xs" align="left"><?php echo $topic->title; ?></h6>
			<h2 class="topic-title hidden-xs" align="left"><?php echo $topic->title; ?></h2>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 view-count no-padding">
					<div class="visible-xs pull-left" >
						<span class="glyphicon glyphicon-eye-open"></span>
						<small ><?php echo $topic->views; ?></small>
					</div>
					<div class="hidden-xs">
						<span class="glyphicon glyphicon-eye-open float-type"></span>
						<h6 align="left"><?php echo $topic->views; ?></h6>
					</div>
				</div>
				<div class="col-xs-6 fav-count no-padding">
					<div class="visible-xs pull-left">
						<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
						<small class="favorite-count"><?php echo $topic->favorite_count; ?></small>
					</div>
					<div class="hidden-xs">
						<span class="glyphicon glyphicon-star float-type"></span>
						<h6 align="left" class="favorite-count"><?php echo $topic->favorite_count; ?></h6>
					</div>
				</div>
				<div class="col-xs-12 update-time no-padding">
					<div class="visible-xs pull-left">
						<span>最後更新日:</span>
						<small><?php echo date('Y年m月d日', strtotime($topic->update_time)); ?></small>
					</div>
					<div class="hidden-xs pull-left">
						<span>最後更新日:</span>
						<small><?php echo date('Y年m月d日', strtotime($topic->update_time)); ?></small>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<ul class="nav nav-tabs">
  <li role="presentation" id="link_tab" class="komatome-tab active" >
	<?php echo $this->tag->linkTo(array('javascript:void(0);', 'リンク')); ?>
  </li>
  <li role="presentation" id="pic_tab" class="komatome-tab">
  	<?php echo $this->tag->linkTo(array('post/comment_gazo/' . $topic->page_id, '画像')); ?>
  </li>
  <li role="presentation" id="video_tab" class="komatome-tab">
	<?php echo $this->tag->linkTo(array('post/comment_video/' . $topic->page_id, '動画')); ?>
  </li>
  <li role="presentation" id="text_tab" class="komatome-tab">
  	<?php echo $this->tag->linkTo(array('post/comment_text/' . $topic->page_id, 'テキスト')); ?>
  </li>
</ul>
	<?php echo $this->tag->form(array('post/create', 'method' => 'post', 'enctype' => 'multipart/form-data')); ?>
		<fieldset>
			<div class="tab-content">
		        <div class="tab-pane active" id="link_area">
		        	<div class="row padding-top-down-10">
			        	<div class="col-xs-12">
							<?php foreach ($linkForm as $element) { ?>
								<?php echo $element; ?>
							<?php } ?>
			        	</div>
		        	</div>
		        	<hr>
		        </div>
		        <div class="tab-pane fade" id="pic_area">
		            <div class="row padding-top-down-10">
						<?php foreach ($pictureForm as $element) { ?>
							<?php if ($element->getName() == 'picture_area_file_upload') { ?>
			            		<div class="col-xs-7 form-group ">
									<div href = "#" class = "thumbnail lg">
										<img id="picture_area_image_preview" name="picture_area_image_preview" class="center-pic short-img" src = '#' alt = "" onerror="this.src='<?php echo $this->url->get('img/default-img.jpg'); ?>'">
									</div>
								</div>
								<div class="col-xs-5 form-group">
									<div class="col-xs-7 no-padding">
										<span class="btn btn-default btn-file glyphicon glyphicon-upload form-control" aria-hidden="true">
										    <?php echo $element; ?>
										</span>
									</div>
									<div class="col-xs-5 no-padding">
										<button id="clear_picture_area_image" type="button" class="btn btn-default  form-control" aria-label="Left Align" style="text-align: center;">
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
		        <div class="tab-pane fade" id="video_area">
		            <div class="row padding-top-down-10">
						<?php foreach ($videoForm as $element) { ?>
							<?php if ($element->getName() == 'video_area_url') { ?>
								<div class="col-xs-8 form-group">
				    				<?php echo $element; ?>
				    			</div>
								<div class="col-xs-4 form-group">
				    				<div class="col-xs-6 form-group no-padding">
						    			<button type="button" id="preview_url_attach" class="btn btn-default form-control" aria-label="Left Align">
											<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
										</button>
									</div>
									<div class="col-xs-6 form-group no-padding">
										<button id="clear_video_area_url" type="button" class="btn btn-default  form-control" aria-label="Left Align" style="text-align: center;">
											<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
										</button>
									</div>
								</div>
								<div class="col-xs-12 form-group">
									<div class="selector-wrapper"></div>
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
		        <div class="tab-pane fade" id="text_area">
		            <div class="row padding-top-down-10">
			        	<div class="col-xs-12">
							<?php foreach ($textForm as $element) { ?>
								<?php echo $element; ?>
							<?php } ?>
			        	</div>
		        	</div>
		        	<hr>
		        </div>
		    </div>
		    <div class="col-xs-12">

				<div class="control-group">
				    <?php echo $this->tag->submitButton(array('投稿する', 'class' => 'btn btn-primary')); ?>
				</div>
		    </div>
		</fieldset>

	</form>

