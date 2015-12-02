<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <?php echo $this->tag->getTitle(); ?>
        <?php echo $this->tag->stylesheetLink('css/bootstrap.min.css'); ?>
        <?php echo $this->tag->stylesheetLink('css/bootstrap.rewrite.css'); ?>
        <?php echo $this->tag->stylesheetLink('css/ct-navbar.css'); ?>
        <?php echo $this->tag->stylesheetLink('css/pe-icon-7-stroke.css'); ?>
        <?php echo $this->tag->stylesheetLink('css/ladda-themeless.min.css'); ?>

        <?php echo $this->assets->outputCss(); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

    </head>
    <body>
        <?php echo $this->getContent(); ?>
        <?php echo $this->tag->javascriptInclude('js/jquery.min.js'); ?>
        <?php echo $this->tag->javascriptInclude('js/bootstrap.min.js'); ?>
        <?php echo $this->tag->javascriptInclude('js/utils.js'); ?>
        <?php echo $this->tag->javascriptInclude('js/ct-navbar.js'); ?>
        <?php echo $this->tag->javascriptInclude('js/spin.min.js'); ?>
        <?php echo $this->tag->javascriptInclude('js/ladda.min.js'); ?>
        <?php echo $this->assets->outputJs(); ?>
    </body>
</html>