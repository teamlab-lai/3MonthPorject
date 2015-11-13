<?php echo $this->elements->getMainMenu(); ?>
<?php if ($this->elements->showSubMenu() == true) { ?>
    <nav class="navbar navbar-inverse navbar-fixed-top white-color">
        <div class="col-xs-4">
            <div class="inner-nav">
                <?php echo $this->elements->getLeftSubMenu(); ?>
            </div>
        </div>
        <div class="col-xs-4 ">
            <?php if (isset($title)) { ?>
                <p class="text-center visible-xs small-size"><?php echo $title; ?></p>
                <h2 class="text-center hidden-xs"><?php echo $title; ?></h2>
            <?php } ?>
        </div>
        <div class="col-xs-4">
            <div class="inner-nav">
                <?php echo $this->elements->getRightSubMenu(); ?>
            </div>
        </div>
     </nav>
<?php } ?>
<div class="container">
    <?php echo $this->flash->output(); ?>
    <?php echo $this->getContent(); ?>
</div>