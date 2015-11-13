<?php

class ReloadController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Reload');
        $this->view->title ='再読み込み';
        parent::initialize();
    }

    public function indexAction()
    {
        return  $this->response->redirect('index/index');
    }
}
