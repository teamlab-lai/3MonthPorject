
<?php echo $this->getContent(); ?>
<div class="row margin-top-50">
	<div class="col-xs-6 col-xs-offset-3 col-sm-offset-3 ">
		<?php echo $this->tag->linkTo(array($loginUrl, 'class' => 'thumbnail', '<img src="/matome/img/F_icon.svg.png" alt="">', 'local' => false)); ?>
	</div>
	<div class="col-xs-6 col-xs-offset-3 col-sm-offset-3 ">
		<?php echo $this->tag->linkTo(array('/matome/session/testLogin', 'class' => '', '携帯電話テストログイン', 'local' => false)); ?>
	</div>
</div>
