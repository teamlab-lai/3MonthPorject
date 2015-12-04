<?php
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;
/**
 * FbMethodController
 *
 *  FB APIの機能
 */
class FbMethodController extends ControllerBase
{

    protected function initialize()
    {
        $this->auth = $this->getAuth();
        parent::initialize();
    }

    /**
     * Calling FB api to get user's information and token
     * fb apiでユーザーの情報を取ります
     *
     * @param  Object $accessToken Usertoken information come from FB api/FB APIでユーザーTOKEN情報
     *
     * @return array $result ゆーざーのFB情報
     */
    protected function _getUserFbProfileInfo($accessToken){

      $result = array(
        'result'=>false,
        'message'=>null,
        'userNode'=>null,
        'adminInfo'=>null,
        'accessToken'=>$accessToken,
        );

        //もしユーザーのTOKEN時間は短い
        if (! $accessToken->isLongLived()) {
          // Exchanges a short-lived access token for a long-lived one
          try {
            $oAuth2Client = $this->fb->getOAuth2Client();
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
          } catch (Facebook\Exceptions\FacebookSDKException $e) {

                $result['message'] = 'エラーが有ります: ' . $helper->getMessage();
                return $result;
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
                    $result['message'] = 'パーミッションはなければなりません。';
                    return $result;
                }
            }

            //ADMINレベルをチェックします
            $admin_user_response = $this->fb->get('/me/accounts?fields=access_token,perms,id,category,cover,name,picture');
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
                    'picture'=>(isset($fanPageInfo['picture']['data']['url'])) ? $fanPageInfo['picture']['data']['url'] : null,
                    //'picture'=>(isset($fanPageInfo['cover']['source'])) ? $fanPageInfo['cover']['source'] : null,
                    'token'=>$fanPageInfo['access_token'],
                    );
                }
              }
            }

            $result = array(
              'result'=>true,
              'message'=>null,
              'userNode'=>$userNode,
              'adminInfo'=>$adminInfo,
              'accessToken'=>$accessToken,
            );
            return $result;

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $result['message'] = 'Graphからエラーが有ります: ' . $e->getMessage();
            return $result;

        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            $result['message'] = 'Facebook SDKらエラーが有ります: ' . $e->getMessage();
            return $result;
        }
    }


    /**
     * FB APIでページの情報を取ります
     * @param  string $fb_id FB　ID
     * @return array  $page_info ページの情報
     */
    protected function _getFbPageInfo($page_id){
        if($this->auth['isAdmin'] == true){
            $this->fb->setDefaultAccessToken($this->auth['adminToken']);
        }else{
            $this->fb->setDefaultAccessToken($this->auth['token']);
        }

        try {
            $page_info = array();

            $page_response = $this->fb->get('/'.$page_id."?fields=updated_time,message,attachments{media},link,from");
            $pageNode = $page_response->getDecodedBody();
            $page_info['updated_time'] = date('Y-m-d H:i:s',strtotime($pageNode['updated_time']) );
            $page_info['attachment_image'] = (isset($pageNode['attachments'])) ? $pageNode['attachments']['data'][0]['media']['image']['src'] : null;
            $page_info['link'] = isset($pageNode['link']) ? $pageNode['link'] : null;
            $page_info['user_name'] = $pageNode['from']['name'];
            $page_info['user_id'] = $pageNode['from']['id'];
            return $page_info;

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            //$this->flash->error('Graphからエラーが有ります: ' . $e->getMessage());
            return false;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
           // $this->flash->error('Facebook SDKらエラーが有ります: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * FBでコメントの情報を取ります
     * @param  string $comment_id コメントID
     * @return array $comment_info コメントの情報
     */
    protected function _getFbCommentInfo($comment_id){
        if($this->auth['isAdmin'] == true){
            $this->fb->setDefaultAccessToken($this->auth['adminToken']);
        }else{
            $this->fb->setDefaultAccessToken($this->auth['token']);
        }
         try {
            $comment_info = array();

            $comment_response = $this->fb->get('/'.$comment_id."?fields=id,created_time,from,message,attachment");
            $commentNode = $comment_response->getDecodedBody();
            $comment_info['message'] = $commentNode['message'];
            $comment_info['updated_time'] = date('Y-m-d H:i:s',strtotime($commentNode['created_time']) );
            $comment_info['attachment_image'] = (isset($commentNode['attachment'])) ? $commentNode['attachment']['media']['image']['src'] : null;
            if( isset($commentNode['attachment']) ){
                if( false !==  strpos($commentNode['attachment']['type'], 'video')){
                    $comment_info['attachment_type'] = 'video';
                }elseif( false !==  strpos($commentNode['attachment']['type'], 'photo') ){
                    $comment_info['attachment_type'] = 'photo';
                }else{
                    $comment_info['attachment_type'] = 'website';
                }

                $comment_info['link'] = isset($commentNode['attachment']['url']) ? $commentNode['attachment']['url'] : null;
            }
            $comment_info['user_name'] = $commentNode['from']['name'];
            $comment_info['user_id'] = $commentNode['from']['id'];

            //ユーザーの画像を取ります
            try{
                $user = $this->fb->get('/'.$commentNode['from']['id'].'?fields=picture');
                $user = $user->getDecodedBody();
                $user_photo = isset($user['picture']['data']['url']) ? $user['picture']['data']['url'] : null;
            }catch(Facebook\Exceptions\FacebookResponseException $e) {
                $user_photo = null;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $user_photo = null;
            }
            $comment_info['user_photo'] = $user_photo;
            return $comment_info;

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            //$this->flash->error('Graphからエラーが有ります: ' . $e->getMessage());
            return false;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
           // $this->flash->error('Facebook SDKらエラーが有ります: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * FBでコメントの情報を投稿する機能
     * @param  string $page_id トッピングID
     * @param  array $post_info 投稿の資料
     * @param  array $pic_dir　アプロードダータURL
     * @return array $result 投稿結果
     */
    protected function _postOnFb($page_id , $post_info , $pic_dir = null ){
        $result = array(
            'result' => false,
            'comment_id' => null,
            );
        //もしユーザーはadminレベル
        if($this->auth['isAdmin'] == true){
            $this->fb->setDefaultAccessToken($this->auth['adminToken']);
        }else{
            $this->fb->setDefaultAccessToken($this->auth['token']);
        }

        //アプロードダータが有ったら
        if($pic_dir != null){
            $post_info['source'] = $this->fb->fileToUpload($pic_dir);
        }

        $batch = [
          'post' => $this->fb->request('POST', '/'.$page_id.'/comments', $post_info ),
        ];

        try {

          $responses = $this->fb->sendBatchRequest($batch);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return $result;

        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return $result;
        }

        foreach ($responses as $key => $response) {
            if ($response->isError()) {
                $e = $response->getThrownException();
                /*
                echo '<p>Error! Facebook SDK Said: ' . $e->getMessage() . "\n\n";
                echo '<p>Graph Said: ' . "\n\n";
                var_dump($e->getResponse());
                */
                return $result;
            } else {
                $post_response = json_decode($response->getBody());
                $comment_id = $post_response->id;
            }
        }

        $result['result'] = true;
        $result['comment_id'] = $comment_id;
        return $result;
    }

    /**
     * FB APIでLIKE数とCOMMENT数を取ります
     * @param  string $fb_id FBのトッピングIDまたは
     * @return  array $result LIKE数とCOMMENT数
     */
    protected function _getFBLikesAndComments( $fb_id ){
        $result = array(
            'result' => false,
            'likes' => 0,
            'comments' => 0,
            'is_liked'=> false,
            );

        if($this->auth['isAdmin'] == true){
            $this->fb->setDefaultAccessToken($this->auth['adminToken']);
            $post_user_id = $this->auth['adminId'];
        }else{
            $this->fb->setDefaultAccessToken($this->auth['token']);
            $post_user_id = $this->auth['id'];
        }

        try{
            $response = $this->fb->get('/'.$fb_id.'?fields=comments,likes');
            $response = $response->getDecodedBody();
            $result['comments'] = (isset($response['comments']['data'])) ? number_format(count($response['comments']['data'])) : 0;
            $result['likes'] = (isset($response['likes']['data'])) ? number_format(count($response['likes']['data'])) : 0;
            //ユーザーはライクを確認します
            if($result['likes'] != 0){
                 foreach( $response['likes']['data'] AS $index => $user){
                    if( $post_user_id == $user['id']){
                        $result['is_liked'] = true;
                        break;
                    }
                }
            }
            $result['result'] = true;

        }catch(Facebook\Exceptions\FacebookResponseException $e) {
            return  $result;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return  $result;
        }

        return  $result;
    }

    /**
     * FB APIでページを削除する機能
     * @param  string $fb_id FB　ID
     * @return boolean true/false
     */
    protected function _fbPageDelete($fb_id){

        try{
            //もしユーザーはadminレベル
            if($this->auth['isAdmin'] == true){
                $this->fb->setDefaultAccessToken($this->auth['adminToken']);
            }else{
                $this->fb->setDefaultAccessToken($this->auth['token']);
            }

            //FBでトッピークを削除します
            $delete_status = $this->fb->delete($fb_id);
            $delete_status = $delete_status->getDecodedBody();

            if($delete_status['success'] == true){
                return true;
            }

        }catch(Facebook\Exceptions\FacebookResponseException $e) {
            //var_dump($e->getMessage());
            return false;
        }catch(Facebook\Exceptions\FacebookSDKException $e) {
            //var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * FB APIでCOMMENTの子コメント内容を取ります
     * @param  string $comment_id コメントIDまたは
     * @return  array $result LIKE数とCOMMENT数
     */
    protected function _getCommentDetail($comment_id){
        $result = array(
            'result' => false,
            'comments' => null,
            );
        if($this->auth['isAdmin'] == true){
            $this->fb->setDefaultAccessToken($this->auth['adminToken']);
        }else{
            $this->fb->setDefaultAccessToken($this->auth['token']);
        }

        try{
            $response = $this->fb->get('/'.$comment_id.'?fields=comments{from,message,attachment,id}');
            $response = $response->getDecodedBody();
            if( !isset($response['comments']) ){
                return $result;
            }
            $comments = array();
            foreach($response['comments']['data'] AS $index => $comment){

                $user_name = $comment['from']['name'];
                //ユーザーの画像を取ります
                try{
                    $user = $this->fb->get('/'.$comment['from']['id'].'?fields=picture');
                    $user = $user->getDecodedBody();
                    $user_photo = isset($user['picture']['data']['url']) ? $user['picture']['data']['url'] : null;
                }catch(Facebook\Exceptions\FacebookResponseException $e) {
                    $user_photo = null;
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    $user_photo = null;
                }

                //ファイルが有ったら、タイプを確認します
                if( isset($comment['attachment'])){
                    $type = $comment['attachment']['type'];
                    if( false !== ($rst = strpos($type, "video")) ){
                        $type = 'video';
                    }elseif( false !== ($rst = strpos($type, "photo")) ){
                        $type = 'photo';
                    }else{
                        $type = 'website';
                    }
                }else{
                    $type = null;
                }

                //ファイルのサムネイルを取ります
                if( isset($comment['attachment']['media']['image']['src'])){
                    $thumbnail_picture = $comment['attachment']['media']['image']['src'];
                }else{
                    $thumbnail_picture = null;
                }

                //ファイルのURLを取ります
                if( isset($comment['attachment']['url'])){
                    $url = $comment['attachment']['url'];
                }else{
                    $url = null;
                }

                $comments[$index] = array(
                    'user_photo' => $user_photo,
                    'user_name' => $user_name,
                    'message' => ( !filter_var($comment['message'], FILTER_VALIDATE_URL) === false ) ? null : $comment['message'],
                    'url_message' => ( !filter_var($comment['message'], FILTER_VALIDATE_URL) === false ) ? $comment['message'] : null,
                    'type' => $type,
                    'thumbnail_picture'=>$thumbnail_picture,
                    'url'=>$url,
                );
            }
            $result['result'] = true;
            $result['comments'] = $comments;
            return $result;
        }catch(Facebook\Exceptions\FacebookResponseException $e) {
            return  $result;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return  $result;
        }
    }

    /**
     * FB APIでライクをあげます
     * @return array REST API スタイル答え
     */
    public function likeCreateAction(){
        $this->view->disable();
        $request = new Request();
        $response = new Response();
        $result = false;
        if ($request->isPost()) {
            if ($request->isAjax()) {
                $fb_page_id = $this->request->getPost("fb_page_id");
                if($fb_page_id == null){
                    $response->setJsonContent(
                        array(
                            'status'   => 'ERROR',
                            'messages' => ' エラーが有りました。'
                        )
                    );
                    return $response;
                }
                if($this->auth['isAdmin'] == true){
                    $this->fb->setDefaultAccessToken($this->auth['adminToken']);
                }else{
                    $this->fb->setDefaultAccessToken($this->auth['token']);
                }

                try{

                    $fb_response = $this->fb->post('/'.$fb_page_id.'/likes');
                    $fb_response = $fb_response->getDecodedBody();
                    $result = $fb_response['success'];

                    //like数を取ります
                    $comments_and_likes_num = $this->_getFBLikesAndComments($fb_page_id);
                   if( $comments_and_likes_num['result'] == true){
                        $likes_num = $comments_and_likes_num['likes'];
                    }

                }catch(Facebook\Exceptions\FacebookResponseException $e) {

                }catch(Facebook\Exceptions\FacebookSDKException $e) {

                }

                if($result == true){
                    $response->setJsonContent(
                        array(
                            'status'   => 'OK',
                            'likes'    => isset($likes_num) ? $likes_num : 0,
                        )
                    );
                }else{
                    $response->setJsonContent(
                        array(
                            'status'   => 'ERROR',
                            'messages' => ' エラーが有りました。'
                        )
                    );
                }
            }
        }else{
             $response->setJsonContent(
                array(
                    'status'   => 'ERROR',
                    'messages' => 'POST方はなければなりません。'
                )
            );
        }
        return $response;
    }

    /**
     * FB APIでライクをキャンセルします
     * @return array REST API スタイル答え
     */
    public function likeDeleteAction(){
        $this->view->disable();
        $request = new Request();
        $response = new Response();
        $result = false;
        if ($request->isPost()) {
            if ($request->isAjax()) {
                $fb_page_id = $this->request->getPost("fb_page_id");
                if($fb_page_id == null){
                    $response->setJsonContent(
                        array(
                            'status'   => 'ERROR',
                            'messages' => ' エラーが有りました。'
                        )
                    );
                    return $response;
                }
                if($this->auth['isAdmin'] == true){
                    $this->fb->setDefaultAccessToken($this->auth['adminToken']);
                }else{
                    $this->fb->setDefaultAccessToken($this->auth['token']);
                }

                try{

                    $fb_response = $this->fb->delete('/'.$fb_page_id.'/likes');
                    $fb_response = $fb_response->getDecodedBody();
                    $result = $fb_response['success'];

                    //like数を取ります
                    $comments_and_likes_num = $this->_getFBLikesAndComments($fb_page_id);
                   if( $comments_and_likes_num['result'] == true){
                        $likes_num = $comments_and_likes_num['likes'];
                    }

                }catch(Facebook\Exceptions\FacebookResponseException $e) {

                }catch(Facebook\Exceptions\FacebookSDKException $e) {

                }

                if($result == true){
                    $response->setJsonContent(
                        array(
                            'status'   => 'OK',
                            'likes'    => isset($likes_num) ? $likes_num : 0,
                        )
                    );
                }else{
                    $response->setJsonContent(
                        array(
                            'status'   => 'ERROR',
                            'messages' => ' エラーが有りました。'
                        )
                    );
                }
            }
        }else{
             $response->setJsonContent(
                array(
                    'status'   => 'ERROR',
                    'messages' => 'POST方はなければなりません。'
                )
            );
        }
        return $response;
    }

    /**
     * FB APIでコメントを返信します
     * @return array REST API スタイル答え
     */
    public function postCommentAction(){
        $this->view->disable();
        $request = new Request();
        $response = new Response();

        if ($request->isPost()) {
            if ($request->isAjax()) {
                $fb_page_id = $this->request->getPost("fb_page_id");
                $input_message = $this->request->getPost("input_message");

                if($fb_page_id == null || $input_message == null){
                    $response->setJsonContent(
                        array(
                            'status'   => 'ERROR',
                            'messages' => ' エラーが有りました。'
                        )
                    );
                    return $response;
                }

                $post_info = array(
                    'message' => $input_message,
                );

                $result = $this->_postOnFb( $fb_page_id, $post_info, null);

                $comment_info = $this->_getFbCommentInfo($result['comment_id']);
                if($result['result'] == true){

                    //トッピングの更新時間をアップデートします
                    $comment = Comments::findFirst(
                        array(
                        '(comment_id = :comment_id:)',
                        'bind' => array('comment_id' => $fb_page_id),
                        )
                    );

                    $topic_id = $comment->page_id;

                    $this->updateTopicCommentCount($topic_id);

                    $response->setJsonContent(
                        array(
                            'status'   => 'OK',
                            'message'  => null,
                            'comment_info' =>$comment_info
                        )
                    );
                }else{
                    $response->setJsonContent(
                        array(
                            'status'   => 'ERROR',
                            'messages' => ' エラーが有りました。'
                        )
                    );
                }
            }
        }else{
             $response->setJsonContent(
                array(
                    'status'   => 'ERROR',
                    'messages' => 'POST方はなければなりません。'
                )
            );
        }
        return $response;
    }
}
