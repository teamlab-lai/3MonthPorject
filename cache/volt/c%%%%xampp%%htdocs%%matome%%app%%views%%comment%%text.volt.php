
<?php echo $this->getContent(); ?>
<ul class="nav nav-tabs">
  <li role="presentation" id="link_tab" class="komatome-tab " >
	<?php echo $this->tag->linkTo(array('comment/index/' . $topic->page_id, 'リンク')); ?>
  </li>
  <li role="presentation" id="pic_tab" class="komatome-tab">
  	<?php echo $this->tag->linkTo(array('comment/picture/' . $topic->page_id, '画像')); ?>
  </li>
  <li role="presentation" id="video_tab" class="komatome-tab">
	<?php echo $this->tag->linkTo(array('comment/video/' . $topic->page_id, '動画')); ?>
  </li>
  <li role="presentation" id="text_tab" class="komatome-tab active">
  	<?php echo $this->tag->linkTo(array('javascript:void(0);', 'テキスト', false)); ?>
  </li>
</ul>
	<?php echo $this->tag->form(array('comment/createText/' . $topic->page_id, 'method' => 'post', 'enctype' => 'multipart/form-data')); ?>
		<fieldset>
			<div class="tab-content">
		        <div class="tab-pane active" id="text_area">
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
				    <?php echo $this->tag->submitButton(array('投稿する', 'class' => 'btn btn-primary')); ?>
				</div>
		    </div>
		</fieldset>

	</form>

