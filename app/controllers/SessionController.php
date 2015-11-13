<?php
use Phalcon\Mvc\Url;
/**
 * SessionController
 *
 * Allows to authenticate users
 * ユーザーのログイン情報を記録します
 */
class SessionController extends ControllerBase
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
     * Calling FB api to get user's information and token
     * fb apiでユーザーの情報を取ります
     *
     * @param  Object $accessToken Usertoken information come from FB api/FB APIでユーザーTOKEN情報
     *
     * @return boolean
     */
    private function _getFbId($accessToken){
        //もしユーザーのTOKEN時間は短い
        if (! $accessToken->isLongLived()) {
          // Exchanges a short-lived access token for a long-lived one
          try {
            $oAuth2Client = $this->fb->getOAuth2Client();
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
          } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $this->flash->error('エラーが有ります: ' . $helper->getMessage());
                return $this->forward('session/index');
          }
        }

        $this->fb->setDefaultAccessToken($accessToken->getValue());

        try {
            $user_response = $this->fb->get('/me?fields=id,name,picture');
            $userNode = $user_response->getGraphUser();

            //許可をチェックします
            $promession_response = $this->fb->get('/'.$userNode->getId().'/permissions');
            $promession_response = $promession_response->getDecodedBody();
            foreach($promession_response['data'] AS $index=>$permission){
                if($permission['status'] == 'declined'){
                    $this->flash->error('パーミッションはなければなりません。');
                    return false;
                }
            }

            //ADMINレベルをチェックします
            $admin_user_response = $this->fb->get('/me/accounts?fields=access_token,perms,id,category,cover,name');
            $amdinUserNode = $admin_user_response->getDecodedBody();
            $adminInfo = array(
              'is_admin'=>false,
              'id'=>null,
              'name'=>null,
              'picture'=>null,
              'token'=>null,
              );

            foreach($amdinUserNode['data'] AS $index=>$fanPageInfo){
              if($fanPageInfo['id'] == $this->FbPageId){
                if(in_array('CREATE_CONTENT', $fanPageInfo['perms'])){
                  $adminInfo = array(
                    'is_admin'=>true,
                    'id'=>$fanPageInfo['id'],
                    'name'=>$fanPageInfo['name'],
                    'picture'=>(isset($fanPageInfo['cover']['source'])) ? $fanPageInfo['cover']['source'] : null,
                    'token'=>$fanPageInfo['access_token'],
                    );
                }
              }
            }

            return $this->_registerSession($userNode , $accessToken , $adminInfo);

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $this->flash->error('Graphからエラーが有ります: ' . $e->getMessage());
            return false;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            $this->flash->error('Facebook SDKらエラーが有ります: ' . $e->getMessage());
            return false;
        }


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
            $result = $this->_getFbId($accessToken);
            if( $result == false){
                return $this->forward('session/index');
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
}
