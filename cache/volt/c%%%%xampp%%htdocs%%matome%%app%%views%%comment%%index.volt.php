<?php echo $this->getContent(); ?>

<ul class="nav nav-tabs">
  <li role="presentation" id="link_tab" class="komatome-tab active" >
	<?php echo $this->tag->linkTo(array('javascript:void(0);', 'リンク', false)); ?>
  </li>
  <li role="presentation" id="pic_tab" class="komatome-tab">
  	<?php echo $this->tag->linkTo(array('comment/picture/' . $topic->page_id, '画像')); ?>
  </li>
  <li role="presentation" id="video_tab" class="komatome-tab">
	<?php echo $this->tag->linkTo(array('comment/video/' . $topic->page_id, '動画')); ?>
  </li>
  <li role="presentation" id="text_tab" class="komatome-tab">
  	<?php echo $this->tag->linkTo(array('comment/text/' . $topic->page_id, 'テキスト')); ?>
  </li>
</ul>
	<?php echo $this->tag->form(array('comment/createUrl/' . $topic->page_id, 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'comment_form')); ?>
		<fieldset>
			<div class="tab-content">
		        <div class="tab-pane active" id="link_area">
		        	<div class="row padding-top-down-10">
			        	<div class="col-xs-12">
							<?php foreach ($form as $element) { ?>
								<?php echo $element; ?>
							<?php } ?>
			        	</div>
		        	</div>
		        	<hr>
		        </div>
		    </div>
		    <div class="col-xs-12 text-center">
				<div class="control-group">
					<button class="btn btn-primary ladda-button js-submit" data-style="zoom-in"><span class="ladda-label">投稿する</span></button>
				</div>
		    </div>
		</fieldset>

	</form>

