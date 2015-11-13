<?php
use Phalcon\Paginator\Adapter\Model as Paginator;
class DetailController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Comment Detail');
        $this->view->title ='コメント';
        parent::initialize();
       //$this->view->auth = $this->auth = $this->getAuth();
    }

    public function indexAction()
    {


    }
}
