
<?php echo $this->getContent(); ?>
<div class="row ">
	<div class="col-xs-12 color-black padding-top-down-10">
		<div class="col-xs-4 ">
			<div href = "#" class = "thumbnail">
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
			<div class="col-xs-9 no-padding">
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
			<div class="col-xs-3 ">
				<div class="btn-group" role="group" aria-label="">
					<?php echo $this->tag->linkTo(array('location/index/' . $topic->page_id, '<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-map-marker"></span></button>')); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 color-gray">

		<?php if ($topic->user_picture_url == null) { ?>
		<?php $topic->user_picture_url = '/matome/img/default-page.png'; ?>
		<?php } ?>
		<?php echo $this->tag->linkTo(array('topic/oyaMatome/' . $topic->page_id, 'class' => 'a-container', '<div class="col-xs-6 no-padding vertical-center">
				 		<div class="col-xs-4 no-padding vertical-center">
							<div href = "#" class = "thumbnail sm-margin">
							<img class="center-pic" src = ' . $topic->user_picture_url . ' alt = "">
							</div>
						</div>
						<div class="col-xs-7 text-center no-padding vertical-center font-gray md-size">
							<span>' . $topic->user_name . 'さん</span>
						</div>
					</div>')); ?>
		<?php echo $this->tag->linkTo(array('comment/index/' . $topic->page_id, 'class' => 'a-container', '<div class="col-xs-5 text-center no-padding vertical-center">
					<div class="btn-group btn-group-xs" role="group" aria-label="">
						<button type="button" class="btn btn-default">
							このまとめに投稿する
							<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
						</button>
					</div>
				</div>')); ?>
	</div>
</div>
<div class="row">
	<div class="list-group">
	<?php foreach ($page->items as $comment) { ?>
		<div class="list-group-item comment-items" data-link='<?php echo $this->url->get('topic/koMatome/' . $comment->comment_id); ?>' >
			<div class="row text-center">
			<?php if ($comment->url_comment != null) { ?>
				<?php echo $this->tag->linkTo(array($comment->url_comment, 'class' => 'font-gray md-size', $comment->url_comment, false)); ?>
			<?php } elseif ($comment->picture_url != null) { ?>
				<h6><?php echo $comment->picture_title; ?></h6>

					<a href="javascript:void(0);" class="thumbnail">
				    	<img src="<?php echo $comment->picture_thumbnail_url; ?>" alt="">
				    </a>

			<?php } elseif ($comment->video_url != null) { ?>
				<h6><?php echo $comment->video_title; ?></h6>
				<?php if ($comment->video_type == 'video') { ?>
					<div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="<?php echo $comment->video_url; ?>"></iframe>
                    </div>
				<?php } elseif ($comment->video_type == 'website') { ?>
					<?php if ($comment->video_thumbnail_url != null) { ?>
						<div href = "#" class = "thumbnail">
							<img class="center-pic" src = <?php echo $comment->video_thumbnail_url; ?> alt = "">
						</div>
					<?php } ?>
					<?php echo $this->tag->linkTo(array($comment->video_url, 'class' => 'font-gray md-size', 'リング', false)); ?>
				<?php } else { ?>
					<div class="col-xs-12">
						<a href="javascript:void(0);" class="thumbnail">
					    	<img src="<?php echo $comment->video_thumbnail_url; ?>" alt="">
					    </a>
					</div>
				<?php } ?>
			<?php } elseif ($comment->text_comment != null) { ?>
				<small><?php echo $comment->text_comment; ?></small>
			<?php } else { ?>

			<?php } ?>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
<div class="navbar navbar-inverse navbar-fixed-bottom" >
	<div class="container-fluid">
		<div class="nav-collapse">
			<ul class="nav navbar-nav" >
			<?php if ($page->total_pages > 1) { ?>
				<li>
					<?php echo $this->tag->linkTo(array('topic/index/' . $topic->page_id . '?page=' . $page->before, '<i class="pe-7s-angle-left"></i><p class="sm-size">前のコメント</p>')); ?>
			<?php } ?>
				</li>
				<?php if ($auth != null) { ?>
					<li id="fav_controller">
						<?php if ($is_fav == false) { ?>
							<a href="javascript:void(0);" id="add_fav" onclick="addFav('<?php echo $topic->page_id; ?>');">
								<i class="pe-7s-star "></i>
								<p class="sm-size">お気に入り追加</p>
							</a>
						<?php } else { ?>
							<a href="javascript:void(0);" id="del_fav" onclick="delFav('<?php echo $topic->page_id; ?>');">
								<i class="pe-7s-star is-fav"></i>
								<p class="sm-size">お気に入り削除</p>
							</a>
						<?php } ?>

					</li>
				<?php } ?>
			<?php if ($page->total_pages > 1) { ?>
				<li>
					<?php echo $this->tag->linkTo(array('topic/index/' . $topic->page_id . '?page=' . $page->next, '<i class="pe-7s-angle-right"></i><p class="sm-size">次のコメント</p>')); ?>
				</li>
			<?php } ?>
			</ul>
		</div>
	</div>
</div>
