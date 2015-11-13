<nav class="navbar navbar-inverse navbar-fixed-top">
  	<div class="col-xs-8">
  		<div class="inner-nav padding-top-10">
			<?php echo $this->tag->form(array('search/doSearch', 'method' => 'post', 'enctype' => 'multipart/form-data')); ?>
				<fieldset>
					<?php foreach ($form as $element) { ?>
						<?php echo $element; ?>
					<?php } ?>
				</fieldset>
			</form>
		</div>
  	</div>
  	<div class="col-xs-4">
  		<div class="inner-nav padding-top-10 pull-right">
			<?php echo $this->tag->linkTo(array('back/index', '<button type="submit" class="btn btn-default btn-sm">キャンセル</button>')); ?>
		</div>
  	</div>
</nav>

<div class="col-xs-12 text-center">
    <h5 class="visible-xs"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>検察履歴</h5>
    <h3 class="hidden-xs"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>検察履歴</h3>
    <hr>
</div>

<?php echo $this->getContent(); ?>
<div class="row ">
    <?php if (isset($topics)) { ?>
        <div class="list-group">
        <?php foreach ($topics as $topic) { ?>
	        <div class="col-xs-12 history-<?php echo $topic->page_id; ?>" >
	        	<div  class="col-xs-10">
					<a href=<?php echo $this->url->get('search/history/' . $topic->page_id); ?> class="list-group-item ">
						<div class="row reset-margin-right">
		                    <div class="col-xs-12 col-xs-offset-1 reset-col-xs-offset-1 padding-5">
		                        <div class="col-xs-4 col-md-2 no-pa">
		                            <div href = "#" class = "thumbnail ">
		                                <?php if (isset($topic->picture_url)) { ?>
		                                    <img class="" src = <?php echo $topic->picture_url; ?> alt = "">
		                                <?php } elseif (isset($topic->video_thumbnail_url)) { ?>
		                                    <img class="center-pic" src = <?php echo $topic->video_thumbnail_url; ?> alt = "">
		                                <?php } else { ?>
		                                    <img class="center-pic" src = <?php echo $topic->user_picture_url; ?> alt = "">
		                                <?php } ?>
		                            </div>
		                        </div>
		                        <div class="col-xs-8 col-md-10 no-padding">
		                            <div class="matome-title col-xs-12 no-padding">
		                                <?php if (isset($topic->title)) { ?>
		                                    <h2 class="matome-titile mobile-size title visible-xs"><?php echo $topic->title; ?></h2>
		                                    <h3 class="matome-titile hidden-xs"><?php echo $topic->title; ?></h3>
		                                <?php } ?>
		                            </div>
		                            <div class="col-xs-12 no-padding">
		                                <div class="col-xs-4 col-md-4 no-padding" >
		                                    <h6 class="mobile-size views visible-xs">
		                                        <span class="glyphicon glyphicon-eye-open"></span>
		                                        <small>
		                                            <?php if (isset($topic->views)) { ?>
		                                                <?php echo $topic->views; ?>
		                                            <?php } else { ?>
		                                                <?php echo 0; ?>
		                                            <?php } ?>
		                                        </small>
		                                    </h6>
		                                    <h6 class="hidden-xs">
		                                        <span class="glyphicon glyphicon-eye-open"></span>
		                                        <small>
		                                            <?php if (isset($topic->views)) { ?>
		                                                <?php echo $topic->views; ?>
		                                            <?php } else { ?>
		                                                <?php echo 0; ?>
		                                            <?php } ?>
		                                        </small>
		                                    </h6>
		                                </div>
		                                <div class="col-xs-8 col-md-8 no-padding">
		                                    <h6 class="mobile-size date-time visible-xs">
		                                        最後更新日:
		                                        <small>
		                                            <?php if (isset($topic->update_time)) { ?>
		                                                <?php echo date('Y年m月d日', strtotime($topic->update_time)); ?>
		                                            <?php } ?>
		                                        </small>
		                                    </h6>
		                                    <h6 class="hidden-xs">
		                                        最後更新日:
		                                        <small>
		                                            <?php if (isset($topic->update_time)) { ?>
		                                                <?php echo date('Y年m月d日', strtotime($topic->update_time)); ?>
		                                            <?php } ?>
		                                        </small>
		                                    </h6>
		                                </div>
		                            </div>
		                        </div>
		                    </div>
		                </div>
					</a>
				</div>
				<div  class="col-xs-2 vertical-center">
					<?php echo $this->tag->checkField(array('history_check_' . $topic->page_id, 'name' => 'history_check[]', 'value' => $topic->page_id)); ?>
				</div>
	        </div>
		<?php } ?>
        </div>
    <?php } ?>
</div>


<div class="navbar navbar-inverse navbar-fixed-bottom" >
	<div class="container-fluid">
		<div class="nav-collapse">
			<ul class="nav navbar-nav" >
				<li id="fav_controller">
					<a href="javascript:void(0);" id="del_history" >
						<i class="pe-7s-trash "></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
