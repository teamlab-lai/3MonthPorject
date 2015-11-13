
<?php echo $this->getContent(); ?>

<div class="row">
    <div class="col-xs-2 col-xs-offset-3 col-sm-offset-5 center">

    <?php echo $this->tag->linkTo(array($loginUrl, ' login', 'class' => '', 'local' => false)); ?>

    <!--
        <fb:login-button scope="public_profile,email,user_location,publish_pages,publish_actions" data-size="xlarge" onlogin="checkLoginState();">
        </fb:login-button>
       -->
    </div>
</div>
<div id="fb-root"></div>
