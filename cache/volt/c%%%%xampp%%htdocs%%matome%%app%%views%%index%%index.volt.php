
<?php echo $this->getContent(); ?>

<div class="row">
    <div class="col-xs-12 pull-left">
            <h5 class="visible-xs">まとめ</h5>
            <h3 class="hidden-xs">まとめ</h3>
    </div>
</div>
<div class="row ">
    <?php if (isset($page->items)) { ?>
        <div class="list-group">
        <?php foreach ($page->items as $topic) { ?>
            <a href=<?php echo $this->url->get('topic/index/' . $topic->page_id); ?> class="list-group-item">
                <div class="row reset-margin-right">
                    <div class="col-xs-11 col-xs-offset-1 reset-col-xs-offset-1">
                        <div class="col-xs-4 col-md-2">
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
        <?php } ?>
        </div>
    <?php } ?>
    <?php if ($page->total_pages > 1) { ?>
        <div class="col-xs-12 text-center">
            <div class="btn-group" role="group" >
                <?php echo $this->tag->linkTo(array('index/index', '<i class="icon-fast-backward"></i> 1', 'class' => 'btn btn-default')); ?>
                <?php echo $this->tag->linkTo(array('index/index?page=' . $page->before, '<i class="icon-step-backward"></i> 前の', 'class' => 'btn btn-default')); ?>
                <a href="javascript:void(0);" class="btn btn-default"><?php echo $page->current . '/' . $page->total_pages; ?> </a>
                <?php echo $this->tag->linkTo(array('index/index?page=' . $page->next, '<i class="icon-step-forward"></i> 次の', 'class' => 'btn btn-default')); ?>
                <?php echo $this->tag->linkTo(array('index/index?page=' . $page->last, '<i class="icon-fast-forward"></i> ' . $page->total_pages, 'class' => 'btn btn-default')); ?>
            </div>
        </div>
    <?php } ?>
</div>
