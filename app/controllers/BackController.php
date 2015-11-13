<?php

/**
 * BackController
 *
 * go back to the last view
 */
class BackController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Back');
        $this->view->title ='戻る';
        parent::initialize();
    }

    public function indexAction(){
    	$breadcrumb = $this->session->get('breadcrumb');
    	if(!$breadcrumb){
    		$this->response->redirect('index/index');
    	}else{
    		array_pop($breadcrumb);
    		$redirect_url = end($breadcrumb);
    		array_pop($breadcrumb);
    		$this->session->set('breadcrumb',$breadcrumb);
    		if($redirect_url == '/'){
    			$redirect_url = 'index/index';
    		}
    		//$redirect_url = ltrim($redirect_url, '/');
    		$this->response->redirect($redirect_url);
    	}
    }

}
