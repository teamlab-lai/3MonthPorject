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

<?php echo $this->flash->output(); ?>
<?php echo $this->getContent(); ?>
