
<?php echo $this->getContent(); ?>

<div class="jumbotron">
	<h2 class="visible-xs">Page not found</h2>
    <h1 class="hidden-xs">Page not found</h1>
    <p>Sorry, you have accesed a page that does not exist or was moved</p>
    <p><?php echo $this->tag->linkTo(array('index', 'Home', 'class' => 'btn btn-primary')); ?></p>
</div>