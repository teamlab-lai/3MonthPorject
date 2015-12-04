<?php
use Phalcon\Mvc\Url;
/**
 * SessionController
 *
 * Allows to authenticate users
 * ユーザーのログイン情報を記録します
 */
class SessionController extends FbMethodController
{
    public function initialize()
    {
        $this->tag->setTitle('Login/Logout');
        $this->assets->addCss('css/session.css');
        $this->view->title ='ログイン';
        parent::initialize();
    }

    /**
     * generate fb login url
     * fb apiでログイン機能を作ります
     *
     */
    public function indexAction()
    {
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = [
            'email',
            'user_posts' ,//for share link into fb page
            'user_photos',
            'user_likes',
            'user_location', // for get user location
            'publish_pages', //for post ingo fb page and get page token if user is admin
            'publish_actions',//for post ingo fb page
            'manage_pages',//get page token if user is admin
        ]; // optional
        $url = new Url();
        $return_url = "http://" . $_SERVER['SERVER_NAME'] . $this->url->getStatic('session/login');
        $loginUrl = $helper->getLoginUrl($return_url, $permissions);

        $this->view->loginUrl = $loginUrl;
    }

    /**
     * Register an authenticated user into session data
     * ユーザーの情報を記録します
     *
     * @param  Object $userNode User information come from FB api/FB APIでユーザー情報
     * @param  Object $accessToken User token information come from FB api/FB APIでユーザーTOKEN情報
     * @param  Array $adminInfo fab page admin lv information/FB APIでFBページADMIN情報
     *
     * @return boolean
     */
    private function _registerSession($userNode , $accessToken , $adminInfo)
    {
        $this->session->set('matome_auth', array(
            'id'              =>$userNode->getId(),
            'name'            =>$userNode->getName(),
            'picture'         => $userNode['picture']['url'],
            'token'           =>$accessToken->getValue(),
            'ExpiresAt'       =>$accessToken->getExpiresAt(),
            'isExpired'       =>$accessToken->isExpired(),
            'isLongLived'     =>$accessToken->isLongLived(),
            'isAppAccessToken'=>$accessToken->isAppAccessToken(),
            'isAdmin'         =>($adminInfo['is_admin'] == true) ? true : false,
            'adminId'         =>($adminInfo['is_admin'] == true) ? $adminInfo['id'] : null,
            'adminName'       =>($adminInfo['is_admin'] == true) ? $adminInfo['name'] : null,
            'adminPicture'    =>($adminInfo['is_admin'] == true) ? $adminInfo['picture'] : null,
            'adminToken'      =>($adminInfo['is_admin'] == true) ? $adminInfo['token'] : null,
        ));
        return true;
    }

    /**
     * This action authenticate and logs an user into the FB
     *　fb apiからユーザーのtokenを取ります
     */
    public function loginAction()
    {
        $auth = $this->getAuth();

        if($auth){
            return $this->forward('index/');
        }
        $helper = $this->fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
            if($helper->getError()){
                $this->flash->error('Graphからエラーが有ります: ' . $helper->getErrorDescription());
                return $this->forward('session/index');
            }elseif($accessToken == null){
                return $this->forward('session/index');
            }
            $result = $this->_getUserFbProfileInfo($accessToken);
            if( $result['result'] == false){
              $this->flash->error($result['message']);
              return $this->forward('session/index');
            }else{
              $this->_registerSession($result['userNode'] , $result['accessToken'] , $result['adminInfo']);
            }
            return $this->forward('index/');

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $this->flash->error('Graphからエラーが有ります: ' . $e->getMessage());
            return $this->forward('session/index');
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          // When validation fails or other local issues
          $this->flash->error('Facebook SDKからエラーが有ります: ' . $e->getMessage());
            return $this->forward('session/index');
        }
    }

    /**
     * Finishes the active session redirecting to the index
     *　ユーザーはログアウトします
     *
     */
    public function logoutAction()
    {
        $this->session->remove('matome_auth');
        return $this->forward('index/index');
    }


    public function testLoginAction(){
       $this->session->set('matome_auth', array(
            'id'              =>'1082845298392991',
            'name'            =>'賴彥伸',
            'picture'         =>'https://scontent.xx.fbcdn.net/hprofile-xaf1/v/t1.0-1/c0.12.50.50/p50x50/406774_343552225655639_746615155_n.jpg?oh=72c295dceb9d0f25858a4e11db84532e&oe=56EDDC5B',
            'token'           =>'CAAI9eKdjJYEBAHsWm7HPMFf6tTHuB1zMMyMkroEPQAOJMegY93p2dVuobhpmNlBGBTVaXLidluxvRfBFOnj8E4bGDsKpjqH6hGKHQZBwzPXYJm2bZA3ZBPiB08vx9sbZC8g18XaqGHTU7kreInYQRJc5GOBLyqcJGH00eGuWoQ4VS8NhrDzWdXzIL0YkCGmGuhnXvhfdzg7razSaD3uZB',
            'ExpiresAt'       =>null,
            'isExpired'       =>null,
            'isLongLived'     =>false,
            'isAppAccessToken'=>false,
            'isAdmin'         =>false,
            'adminId'         =>null,
            'adminName'       =>null,
            'adminPicture'    =>null,
            'adminToken'      =>null,
        ));
        $this->response->redirect('index/index');
    }
}
