<?php
class LocationController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Location');
        $this->view->title ='位置情報';
        parent::initialize();
       //$this->view->auth = $this->auth = $this->getAuth();
    }

    public function indexAction($page_id)
    {

    }
}
