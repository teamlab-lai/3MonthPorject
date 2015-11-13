<?php
use Phalcon\Paginator\Adapter\Model as Paginator;
class IndexController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Welcome');
        $this->view->title ='まとめホーム';
        $this->assets->addCss('css/index.css');
        parent::initialize();
       //$this->view->auth = $this->auth = $this->getAuth();
    }

    public function indexAction()
    {
        $numberPage = $this->request->getQuery("page", "int");
        $numberPage = (isset( $numberPage )) ? $numberPage : 1;
        $topics = Topics::find(
            array(
                'order' => "update_time DESC",
            )
        );

        $paginator = new Paginator(array(
            "data"  => $topics,
            "limit" => 20,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();

    }
}
