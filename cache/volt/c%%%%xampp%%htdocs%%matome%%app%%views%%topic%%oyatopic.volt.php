<?php echo $this->getContent(); ?>
<div class="row ">
	<div class="col-xs-12">
		<div class="col-xs-12 color-white margin-top-10 padding-10">
		<?php if ($auth['isAdmin'] == true || $topic->user_fb_id == $auth['id']) { ?>
			<div class="col-xs-4 pull-right no-padding padding-bottom-10">
				<button type="button" class="btn btn-default  btn-xs pull-right" data-toggle="modal" data-target="#confirm-delete" aria-label="Left Align">
					<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
				</button>
		<?php } ?>
			</div>
			<div class="col-xs-12 have-border ">
				<div class="col-xs-4  no-padding">
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
				<div class="col-xs-8 margin-top-20 ">
					<h6 class="topic-title visible-xs" align="left"><?php echo $topic->title; ?></h6>
					<h2 class="topic-title hidden-xs" align="left"><?php echo $topic->title; ?></h2>
				</div>
				<div class="col-xs-12 no-padding font-gray">
					<small class="visible-xs"><?php echo $topic->description; ?></small>
					<span class="hidden-xs"><?php echo $topic->description; ?></span>
				</div>
			</div>

			<div class="col-xs-12 margin-top-20 margin-bottom-10">
				<a href="javascript:void(0);" class="a-container">
					<div class="col-xs-6 no-padding vertical-center">
						<div class="col-xs-12 text-center no-padding vertical-center md-size">
							<span><?php echo $topic->user_name; ?></span>
						</div>
						<div class="col-xs-12 text-center no-padding vertical-center visible-xs small-size font-gray">
							<span>最後更新日:</span>
							<small><?php echo date('Y年m月d日', strtotime($topic->update_time)); ?></small>
						</div>
						<div class="col-xs-12 text-center no-padding vertical-center hidden-xs font-gray">
							<span>最後更新日:</span>
							<small><?php echo date('Y年m月d日', strtotime($topic->update_time)); ?></small>
						</div>
					</div>
				</a>
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

		<div class="col-xs-12 padding-10 font-gray">
			<small><?php echo $likes; ?> がいいね！と言っています</small>
		</div>
	</div>
</div>
<div class="navbar navbar-inverse navbar-fixed-bottom" >
	<div class="container-fluid">
		<div class="nav-collapse">
			<ul class="nav navbar-nav" >
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
			</ul>
		</div>
	</div>
</div>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">削除確認</h4>
                </div>

                <div class="modal-body">
                    <p>このまとめトッピングを削除しますか？</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
                    <a class="btn btn-danger btn-ok"  onclick="delMatome('<?php echo $topic->page_id; ?>');">はい</a>
                </div>
            </div>
        </div>
    </div>