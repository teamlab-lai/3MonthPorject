<?php
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
/**
 * PostController
 *
 *  post on matome APP
 *  まとめトッピークを投稿する機能
 */
class PostController extends FbMethodController
{

    public function initialize()
    {
        $this->tag->setTitle('Post');
        $this->view->title ='まとめを作る';
        $this->auth = $this->getAuth();

    	$this->assets->addCss('css/jquery.urlive.css');
        $this->assets->addCss('css/font-awesome.min.css');
    	$this->assets->addCss('css/post.css');

        $this->assets->addJs('js/jquery.urlive.js');
        $this->assets->addJs('js/post.js');

        parent::initialize();
    }

    public function indexAction()
    {
    	if(empty($this->auth)){
    		$this->flash->notice('ログインしてください。');
            return $this->forward('session/index');
    	}
    	$this->view->form = new PostForm;
    }

    /**
     * 新しいまとめトピックを投稿します
     */
    public function createAction(){
    	libxml_use_internal_errors(true);
    	$auth = $this->getAuth();
    	$form = new PostForm;

    	//アップロードファイルを検察します
        if ($this->request->hasFiles(true) == true) {

            foreach ($this->request->getUploadedFiles() as $file){
            	//タイプを検察します
            	if ($this->_imageCheck($file->getType())) {
            		//サイズを検察します
                    if($file->getSize() > 1024000){
                    	$this->flash->error('1024KB未満のファイルだけアップロードできます。');
                    	return $this->response->redirect('post/index');
                    }else{

                    	//アップロードフォルダーを検察します
                		$path = rtrim($this->uploadDir, '/\\') . '/' .date('Y_m_d').'/';
                		if (!file_exists($path)) {
	                		try{
	                        	if (!@mkdir($path)) {
								    $error = error_get_last();
								    throw new Exception($error['message'], 1);
								}

	                		}catch(Exception $ex) {
							   //echo "Error: " . $ex->getMessage();
							   $this->flash->error('エラーがありますから、もう一度やり直してください。');
							   return $this->response->redirect('post/index');
							}
						}

						//ファイルをアップロードします
						try{
							$file->moveTo($path. $file->getName());
							//新しい名前を直します
							$new_path = rtrim($this->uploadDir, '/\\') . '/' .date('Y_m_d').'/'.date('His').'_'.$auth['id'].'.'.$file->getExtension();
							rename($path. $file->getName() , $new_path);

						}catch(Exception $ex) {
						   //echo "Error: " . $ex->getMessage();
						   $this->flash->error('エラーがありますから、もう一度やり直してください。');
						   return $this->response->redirect('post/index');
						}
                    }
                }else{
                	$this->flash->error('JPG、JPEG、GIF、PNGファイルだけアップロードできます');
                	return $this->response->redirect('post/index');
                }
            }
    	}

    	// POST　parameterを検察します
    	$post_data = $this->request->getPost();
    	$topics = new Topics();
        if (!$form->isValid($post_data, $topics)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->response->redirect('post/index');
        }

        //FB APIで投稿する機能
		$type = 'message';

		//もしユーザーはadminレベル
		if($auth['isAdmin'] == true){
			$this->fb->setDefaultAccessToken($auth['adminToken']);
		}else{
			$this->fb->setDefaultAccessToken($auth['token']);
		}

		$message = $post_data['title'] ." \r\n\r\n ". $post_data['description'];
		$post_info = array(
				'message' => $message,
			);

		if(isset($post_data['url_preview']) && $post_data['url_preview'] != null ){
			$type = 'url';
			$method = '/feed';
			$post_info['link'] = $post_data['url_preview'];

			//動画のembedのURLのウェブサイトを探します
			if(preg_match("/vimeo/i",$post_data['url_preview']) == true){

				//VimeoウェブサイトのURL
				$video_embed_url = $this->_getVimeoEmbedUrl($post_data['url_preview']);

			}elseif(preg_match("/youtube/i",$post_data['url_preview']) == true){
				//YoutubeウェブサイトのURL
				$video_embed_url = $this->_getYoutubeEmbedUrl($post_data['url_preview']);

			}else{
				$video_embed_url = null;
			}
		}

		if($this->request->hasFiles(true) == true){
			$type = 'upload';
			$method = '/photos';
			$pic_dir = "http://" . $_SERVER['SERVER_NAME'] . $this->url->getStatic().$new_path;
			$post_info['source'] = $this->fb->fileToUpload($pic_dir);
		}

		if(!isset($method)){
			$method = '/feed';
		}
		$batch = [
		  'post' => $this->fb->request('POST', '/'.$this->FbPageId.$method, $post_info ),
		];

		try {
		  $responses = $this->fb->sendBatchRequest($batch);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			$this->flash->error('エラーがありますから、もう一度やり直してください。');
		 	return $this->response->redirect('post/index');
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$this->flash->error('エラーがありますから、もう一度やり直してください。');
			return $this->response->redirect('post/index');
		}

		foreach ($responses as $key => $response) {
		  if ($response->isError()) {
				$e = $response->getThrownException();
				/*
				echo '<p>Error! Facebook SDK Said: ' . $e->getMessage() . "\n\n";
				echo '<p>Graph Said: ' . "\n\n";
				var_dump($e->getResponse());
				*/
				$this->flash->error('エラーがありますから、もう一度やり直してください。');
				return $this->response->redirect('post/index');
		  } else {
		  		$post_response = json_decode($response->getBody());
		  		if($type == 'upload'){
		  			$page_id = $post_response->post_id;
		  		}elseif($type == 'url' || $type =='message'){
		  			$page_id = $post_response->id;
		  		}
		  	}
		}

  		$get_post_info = $this->_getFbPageInfo($page_id);
  		$ipaddress = $this->_get_client_ip();
  		$loc = $this->_get_loc($ipaddress);

  		//DBで情報をセープします
		$topics->user_name 		  	 = ($auth['isAdmin'] == ture) ? $auth['adminName'] : $auth['name'];
		$topics->user_fb_id		  	 = ($auth['isAdmin'] == ture) ? $auth['adminId'] : $auth['id'];
		$topics->user_picture_url	 = ($auth['isAdmin'] == ture) ? $auth['adminPicture'] : $auth['picture'];
		$topics->page_id		  	 = $page_id;
		$topics->title		 		 = $this->request->getPost("title", "striptags");
		if($type == 'upload'){
			$topics->picture_url	 = $get_post_info['attachment_image'];
		}elseif($type == 'url'){
			$topics->video_url		  	 = $get_post_info['link'];
			$topics->video_thumbnail_url = $get_post_info['attachment_image'];
		}
		$topics->embed_video_url 	 = ( isset($video_embed_url) && $video_embed_url != null ) ? $video_embed_url : null;
 		$topics->description		 = $this->request->getPost("description", "striptags");
		$topics->ip_location		 = $ipaddress;
		$topics->longitude			 = ( isset($loc['longitude']) && $loc['longitude'] != null) ? $loc['longitude'] : null;
		$topics->latitude			 = ( isset($loc['latitude']) && $loc['latitude'] != null) ? $loc['latitude'] : null;
		$topics->views		  		 = 0;
		$topics->update_time		 = $get_post_info['updated_time'];

		if ($topics->save() == false) {
			 foreach ($topics->getMessages() as $message) {
			 	$this->flash->error($message);
            }

            return $this->response->redirect('post/index');
    	}
    	$this->flash->success('投稿しました');
  		$this->response->redirect('index/index');
    }

    /**
     * まとめトピックを削除する機能
     * @return array REST API スタイル答え
     */
    public function deleteAction(){
    	$this->view->disable();
    	$request = new Request();
    	$response = new Response();
    	$auth = $this->getAuth();
    	$del_process = false;

    	if ($request->isPost()) {
			if ($request->isAjax()) {
				$page_id = $this->request->getPost("page_id");
				if($page_id != null){

					$result = $this->_fbPageDelete($page_id);
					if($result == true){
						//DBでトッピークを削除します
						$del_process = $this->doDeleteAction($page_id);
					}
			    }
			}
		}else{
    		 $response->setJsonContent(
	            array(
	                'status'   => 'ERROR',
                	'messages' => 'POST方はなければなりません。'
	            )
	        );
    		return $response;
		}

		if($del_process == true){
			$response->setJsonContent(
	            array(
	                'status'   => 'OK',
	                'redirect_url' => "http://" . $_SERVER['SERVER_NAME'] . $this->url->getStatic('index'),
	            )
	        );
		}else{
			$response->setJsonContent(
	            array(
	                'status'   => 'ERROR',
	                'messages' => 'エラーがありますから、もう一度お願いします。'
	            )
	        );
		}
		return $response;
    }

    /**
     * DBにまとめトッピングを削除する機能
     * @param  string $page_id まとめトッピングのID
     * @return boolean true/false
     */
    public function doDeleteAction($page_id){
    	//まとめを削除する機能
    	try{
    		$transactionManager = new TransactionManager();
  			$transaction = $transactionManager->get();

  			$topic = Topics::findFirst(
			array(
				'(page_id = :page_id:)',
				'bind' => array('page_id' => $page_id),
				)
			);

  			if($topic != false){
	  			$topic->setTransaction($transaction);
	  			//トッピングを削除すます
				if(!$topic->delete()){
					$transaction->rollback("まとめトッピングを削除できません。");
				}
			}

			$user_favs = Favorite::find(
				array(
				'(page_id = :page_id:)',
				'bind' => array('page_id' => $page_id),
				)
			);

			foreach($user_favs AS $user_fav){
				$user_fav->setTransaction($transaction);
				//お気に入りを削除します
				if (!$user_fav->delete()) {
					$transaction->rollback("お気に入りデータを削除できません。");
	        	}
			}

			$transaction->commit();
			return true;

    	}catch (Phalcon\Mvc\Model\Transaction\Failed $e) {
    		//var_dump($e->getMessage());
    		return false;
		}
    }
}
