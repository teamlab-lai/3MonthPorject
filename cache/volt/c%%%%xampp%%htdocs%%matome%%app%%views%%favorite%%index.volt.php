
<?php echo $this->getContent(); ?>

<?php $same_day = ''; ?>
<div class="list-group">
<?php if ($page->items == null) { ?>
	<h6>お気に入りはまだありません。</h6>
<?php } ?>
<?php foreach ($page->items as $fav) { ?>
	<?php if ($same_day != date('Y年m月d日', strtotime($fav->update_time))) { ?>
		<label><?php echo date('Y年m月d日', strtotime($fav->update_time)); ?></label>
		<?php $same_day = date('Y年m月d日', strtotime($fav->update_time)); ?>
	<?php } ?>
		<div class="list-group-item" id="<?php echo $fav->id; ?>">
			<button type="button" class="btn btn-default pull-right gray-color"  onclick="delFav('<?php echo $fav->id; ?>','<?php echo $fav->page_id; ?>')">
			  <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			</button>
			<a href="<?php echo $this->url->get('topic/index/' . $fav->page_id); ?>" class="fav-list">
				<div class="row">
					<div class="col-xs-9">
						<h6><?php echo $fav->title; ?></h6>
						<?php if ($fav->video_url != null) { ?>
							<small><?php echo $fav->video_url; ?></small>
						<?php } else { ?>
							<small><?php echo $fav->user_name; ?></small>
						<?php } ?>
					</div>
				</div>
			</a>
		</div>
	<?php } ?>
</div>
<?php if ($page->total_pages > 1) { ?>
	<div class="col-xs-12 text-center">
	    <div class="btn-group" role="group" >
	        <?php echo $this->tag->linkTo(array('favorite/index', '<i class="icon-fast-backward"></i> 1', 'class' => 'btn btn-default')); ?>
	        <?php echo $this->tag->linkTo(array('favorite/index?page=' . $page->before, '<i class="icon-step-backward"></i> 前の', 'class' => 'btn btn-default')); ?>
	        <a href="javascript:void(0);" class="btn btn-default"><?php echo $page->current . '/' . $page->total_pages; ?> </a>
	        <?php echo $this->tag->linkTo(array('favorite/index?page=' . $page->next, '<i class="icon-step-forward"></i> 次の', 'class' => 'btn btn-default')); ?>
	        <?php echo $this->tag->linkTo(array('favorite/index?page=' . $page->last, '<i class="icon-fast-forward"></i> ' . $page->total_pages, 'class' => 'btn btn-default')); ?>
	    </div>
	</div>
<?php } ?>