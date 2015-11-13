
<?php echo $this->getContent(); ?>

<div class="jumbotron">
	<h2 class="visible-xs">Internal Error</h2>
    <h1 class="hidden-xs">Internal Error</h1>
    <p>Something went wrong, if the error continue please contact us</p>
    <p><?php echo $this->tag->linkTo(array('index', 'Home', 'class' => 'btn btn-primary')); ?></p>
</div>