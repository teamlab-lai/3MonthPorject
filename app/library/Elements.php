<?php

use Phalcon\Mvc\User\Component;

/**
 * Elements
 *
 * Helps to build UI elements for the application
 */
class Elements extends Component
{
    private $_hiddenMenuController = array(
            'search','post','topic','comment','location'
        );

    private $_hiddenSubMenuController = array(
            'search'=>array(
                'index',
                ),
        );

    private $_headerMenu = array(
        'navbar-left' => array(
            'index' => array(
                'caption' => 'ホーム',
                'action' => 'index',
                'icon'=>'pe-7s-home',
                'hidden_controller'=>array('post','topic','comment','location'),
            ),
            'favorite' => array(
                'caption' => 'お気に入り',
                'action' => 'index',
                'icon'=>'pe-7s-like',
                'hidden_controller'=>array('session','post','topic','comment','location'),
            ),
            'session' => array(
                'caption' => 'ログイン',
                'action' => 'index',
                'icon'=>'pe-7s-lock',
                'hidden_controller'=>array('post','topic','comment','location'),
            ),
        ),
    );

    private $_headerLeftSubMenu = array(
        'navbar-left' => array(
            'reload' => array(
                'caption' => '再読み込み',
                'action' => 'index',
                'icon'=>'glyphicon glyphicon-refresh',
                'hidden_controller'=>array('session','management','favorite','search','topic','errors','post','comment','location'),
            ),
            'back'=>array(
                'caption' => '戻る',
                'action' => 'index',
                'icon'=>'glyphicon glyphicon-arrow-left',
                'hidden_controller'=>array('index','session','management','favorite','errors'),
            ),
        ),
    );

    private $_headerRightSubMenu = array(
        'navbar-left' => array(
            'post' => array(
                'caption' => '投稿',
                'action' => 'index',
                'icon'=>'glyphicon glyphicon-pencil',
                'hidden_controller'=>array('session','management','favorite','search','topic','errors','post','comment','location'),
            ),
            'search' => array(
                'caption' => '検索',
                'action' => 'index',
                'icon'=>'glyphicon glyphicon-search',
                'hidden_controller'=>array('session','management','favorite','search','errors','post','comment','location'),
            ),
            'session' => array(
                'caption' => 'ログアウト',
                'action' => 'logout',
                'hidden_controller'=>array('index','session','favorite','search','topic','errors','post','comment','location'),
            ),
        ),
    );


    /**
     * Builds header menu with left and right items
     *
     * @return string
     */
    public function getMainMenu()
    {
        $auth = $this->session->get('matome_auth');
        //already login
        if ($auth) {
            unset($this->_headerMenu['navbar-left']['session']);
            $this->_headerMenu['navbar-left']['management'] = array(
                'caption' => '管理',
                'action' => 'index',
                'icon'=>'pe-7s-config',
                'hidden_controller'=>array('session','post','topic','comment'),
            );
        } else {
            //do not show page link
            unset($this->_headerMenu['navbar-left']['favorite']);
            //unset($this->_headerRightSubMenu['navbar-left']['post']);
        }

        $controllerName = $this->view->getControllerName();

        if( in_array($controllerName, $this->_hiddenMenuController)){
            return;
        }
        foreach ($this->_headerMenu as $position => $menu) {
            echo '<nav class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">';
            echo '<div class="container-fluid">';
            echo '<div class="nav-collapse">';
            echo '<ul class="nav navbar-nav" >';
            foreach ($menu as $controller => $option) {
                //if we do not have to hidden this link in this page
                if(!in_array($controllerName, $option['hidden_controller']) ){
                    if ($controllerName == $controller) {
                        echo '<li class="active">';
                    } else {
                        echo '<li>';

                    }
                    if(!isset($option['icon'])){
                        echo $this->tag->linkTo($controller . '/' . $option['action'],  $option['caption']);
                    }else{
                        echo $this->tag->linkTo($controller . '/' . $option['action'], '<i class="'.$option['icon'].'"></i><p>'.$option['caption'].'</p>');
                    }

                    echo '</li>';
                }else{
                    echo '<li></li>';
                 }
            }
            echo '</ul>';
            echo '</div>';
            echo '</div>';
            echo '</nav>';
        }
    }

    /**
     * Builds sub header menu with left items
     *
     * @return string
     */
    public function getLeftSubMenu(){
        $controllerName = $this->view->getControllerName();
        $html = $this->getSubMenuHtml($this->_headerLeftSubMenu , $controllerName);
        echo $html;
    }

    /**
     * Builds sub header menu with right items
     *
     * @return string
     */
    public function getRightSubMenu(){
        $controllerName = $this->view->getControllerName();
        $html = $this->getSubMenuHtml($this->_headerRightSubMenu , $controllerName);
        echo $html;
    }

    private function getSubMenuHtml($subMenu , $controllerName){
        foreach ($subMenu as $position => $menu) {
            $html_content = '<div class="list-inline">';
            foreach ($menu as $controller => $option) {
                if(!in_array($controllerName, $option['hidden_controller']) ){
                    if(!isset($option['icon'])){
                        $html_content .= $this->tag->linkTo($controller . '/' . $option['action'], '<button type="submit" class="btn btn-default btn-sm">'.$option['caption'].'</button>'  );
                        //$html_content .= $this->tag->linkTo($controller . '/' . $option['action'],  $option['caption']);
                    }else{
                        $html_content .= $this->tag->linkTo($controller . '/' . $option['action'], '<span class="'.$option['icon'].'" aria-hidden="true">');
                    }
                    /*
                    if($controller == 'back'){
                        $html_content .= $this->tag->linkTo($this->request->getServer('HTTP_REFERER' ), '<span class="'.$option['icon'].'" aria-hidden="true">', FALSE);
                    }elseif(!isset($option['icon'])){
                        $html_content .= $this->tag->linkTo($controller . '/' . $option['action'], '<button type="submit" class="btn btn-default btn-sm">'.$option['caption'].'</button>'  );
                        //$html_content .= $this->tag->linkTo($controller . '/' . $option['action'],  $option['caption']);
                    }else{
                        $html_content .= $this->tag->linkTo($controller . '/' . $option['action'], '<span class="'.$option['icon'].'" aria-hidden="true">');
                    }
                    */
                }
            }
            $html_content .= '</div>';
        }
        return $html_content;
    }

    /**
     * トップNAVを表示する機能
     *
     * @return boolean TrueはトップNAVを表示します
     */
    public function showSubMenu(){
        $controllerName = $this->view->getControllerName();
        $actionName = $this->view->getActionName();

        foreach($this->_hiddenSubMenuController AS $controller=>$actions){
            if($controllerName == $controller){
                if( in_array($actionName, $actions)){
                    return false;
                }

            }
        }

        return true;
    }
}
