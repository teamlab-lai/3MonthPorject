<?php

class ManagementController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Management');
        $this->view->title ='管理';
        parent::initialize();
    }

    public function indexAction()
    {

    }
}
