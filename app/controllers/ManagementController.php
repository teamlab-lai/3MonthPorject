<?php

class ManagementController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Management');
        $this->view->title ='管理';
        $this->assets->addCss('css/management.css');
        parent::initialize();
    }

    public function indexAction()
    {
    	$this->view->version = $this->version;
    }
}
