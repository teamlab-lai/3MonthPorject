<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

    protected function initialize()
    {

        date_default_timezone_set('Asia/tokyo');
        $this->tag->prependTitle('TeamLab-');
        $this->view->setTemplateAfter('main');

        $this->saveBreadcrumb($this->router->getControllerName(), $this->router->getActionName() );

        //$this->session->remove('breadcrumb');
        //var_dump($this->session->get('breadcrumb'));
        //var_dump($this->router->getRewriteUri());
    }

    protected function forward($uri)
    {
        $uriParts = explode('/', $uri);
        $params = array_slice($uriParts, 2);
    	return $this->dispatcher->forward(
    		array(
    			'controller' => $uriParts[0],
    			'action' => $uriParts[1],
                'params' => $params
    		)
    	);
    }

    protected function getAuth()
    {
        return $this->session->get('matome_auth');
    }

    /**
     * アプロードのファイルのタイプを確認します
     * @param  string $extension Extension (eg 'jpg')
     *
     * @return boolean
     */
    protected function _imageCheck($extension)
    {
        $allowedTypes = [
            'image/gif',
            'image/jpg',
            'image/png',
            'image/jpeg'
        ];

        return in_array($extension, $allowedTypes);
    }

    /**
     * ユーザーの行動をレコードします
     * @param  string $controller Controller名前
     * @param  string $action Action名前
     */
    protected function saveBreadcrumb($controller, $action){

        //このアレイのController名前またはactionをレコードしません
        //array[Controller名前]=>[ 'Action名前','Action名前',...]
        $skip_controller = array(
            'js',
            'css',
            'search'=>array(
                    'doSearch',
                    'history',
                    'deleteHistory'
                ),
            'reload',
            'post',
            'session',
            'back',
            'favorite'=>array(
                    'create',
                    'delete',
                ),
            'errors',
            'comment',
        );

        $breadcrumb = $this->session->get('breadcrumb');
        $temp_uri = null;

        //アレイのController名前またはactionをレコードしません
        if($controller != null){
            if( isset($skip_controller[$controller]) && in_array($action, $skip_controller[$controller])){
                return;
            }elseif( in_array($controller, $skip_controller) ){
                return;
            }
        }

        $current_uri = $this->router->getRewriteUri();
        $current_uri = ltrim($current_uri, '/');

        //始める
        if(!$breadcrumb){
            $this->session->set('breadcrumb',array($current_uri) );
        }else{
            //ゆーざーはIndexのページがいったら
            if($current_uri == 'index/index' || $current_uri == '' || $current_uri == '/'){
                //始める
                $this->session->set('breadcrumb',array($current_uri) );
            }else{
                //レコード
                $last_uri = end($breadcrumb);
                if($last_uri != $current_uri && $current_uri != '' && $current_uri != null){
                    if($temp_uri != null && $temp_uri != $last_uri){
                        array_push($breadcrumb , $temp_uri);
                    }else{
                        array_push($breadcrumb , $current_uri);
                    }
                    $this->session->set('breadcrumb',$breadcrumb);
                }
            }

        }

    }
}
