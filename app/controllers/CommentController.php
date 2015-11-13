<?php
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
/**
 * CommentController
 *
 *  comment on matome APP　
 *  まとめトッピークをコメントする機能
 */
class CommentController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Comment');
        $this->view->title ='コメントを作る';
        $this->auth = $this->getAuth();
    	$this->assets->addCss('css/preview.css');
        $this->assets->addCss('css/font-awesome.min.css');
    	$this->assets->addCss('css/comment.css');

    	$this->assets->addJs('js/comment.js');
        $this->assets->addJs('js/jquery.embedly.js');
        $this->assets->addJs('js/jquery.preview.js');

    	if(empty($this->auth)){
    		$this->flash->notice('ログインしてください。');
            return  $this->response->redirect('errors/show401');
    	}

        parent::initialize();
    }

    /**
     * まとめトッピングURLを投稿するページ
     * @param  string $page_id トッピークID
     */
    public function indexAction($page_id)
    {
    	if(!isset($page_id) || $page_id == null){
            $this->response->redirect('errors/show401');
            return;
        }

    	//ユーザに選択されたタイトル情報
    	$topic = Topics::findFirst(
			array(
			'(page_id = :page_id:)',
			'bind' => array('page_id' => $page_id),
			)
		);
        if($topic == false){
            $this->flash->notice("トピックが有りません。");
            $this->response->redirect('index/index');
            return;
        }else{
            $this->view->topic = $topic;
        }

		$this->view->form = new CommentUrlForm;
    }

    /**
     * まとめトッピング画像を投稿するページ
     * @param  string $page_id トッピークID
     */
    public function pictureAction($page_id){
    	if(!isset($page_id) || $page_id == null){
            $this->response->redirect('errors/show401');
            return;
        }

    	//ユーザに選択されたタイトル情報
    	$topic = Topics::findFirst(
			array(
			'(page_id = :page_id:)',
			'bind' => array('page_id' => $page_id),
			)
		);
        if($topic == false){
            $this->flash->notice("トピックが有りません。");
            $this->response->redirect('index/index');
            return;
        }else{
            $this->view->topic = $topic;
        }


        $this->view->form = new CommentPictureForm;
    }

    /**
     * まとめトッピング動画を投稿するページ
     * @param  string $page_id トッピークID
     */
    public function videoAction($page_id){
    	if(!isset($page_id) || $page_id == null){
            $this->response->redirect('errors/show401');
            return;
        }

    	//ユーザに選択されたタイトル情報
    	$topic = Topics::findFirst(
			array(
			'(page_id = :page_id:)',
			'bind' => array('page_id' => $page_id),
			)
		);
        if($topic == false){
            $this->flash->notice("トピックが有りません。");
            $this->response->redirect('index/index');
            return;
        }else{
            $this->view->topic = $topic;
        }

        $this->view->form = new CommentVideoForm;
    }

    /**
     * まとめトッピングテキストを投稿するページ
     * @param  string $page_id トッピークID
     */
    public function textAction($page_id){
    	if(!isset($page_id) || $page_id == null){
            $this->response->redirect('errors/show401');
            return;
        }

    	//ユーザに選択されたタイトル情報
    	$topic = Topics::findFirst(
			array(
			'(page_id = :page_id:)',
			'bind' => array('page_id' => $page_id),
			)
		);
        if($topic == false){
            $this->flash->notice("トピックが有りません。");
            $this->response->redirect('index/index');
            return;
        }else{
            $this->view->topic = $topic;
        }

        $this->view->form = new CommentTextForm;
    }

    /**
     * FBでコメントの情報を取ります
     * @param  string $comment_id コメントID
     */
    private function _getFbCommentInfo($comment_id){
    	if($this->auth['isAdmin'] == true){
			$this->fb->setDefaultAccessToken($this->auth['adminToken']);
		}else{
			$this->fb->setDefaultAccessToken($this->auth['token']);
		}
		 try {
        	$comment_info = array();

            $comment_response = $this->fb->get('/'.$comment_id."?fields=id,created_time,from,message,attachment");
            $commentNode = $comment_response->getDecodedBody();
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
    private function _postOnFb($page_id , $post_info , $pic_dir = null ){
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
     * トッピークのコメント数を伸びます
     * @param  string $page_id トッピークID
     */
    private function updateTopicCommentCount($page_id){

    	//ユーザに選択されたタイトル情報
    	$topic = Topics::findFirst(
			array(
			'(page_id = :page_id:)',
			'bind' => array('page_id' => $page_id),
			)
		);

    	$topic->comment_count ++ ;
    	$topic->update_time = date('Y-m-d H:i:s');
	    $topic->save();
    }

    /**
     * まとめトッピングURLを投稿する機能
     * @param  string $page_id トッピングID
     */
    public function createUrlAction($page_id){
    	$form = new CommentUrlForm;

		// POST　parameterを検察します
    	$post_data = $this->request->getPost();
        if (!$form->isValid($post_data)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->response->redirect('comment/index/'.$page_id);
        }

		$post_info = array(
				'message' => $post_data['url_comment'],
			);

		$post_fb_result = $this->_postOnFb($page_id , $post_info , null );

		if($post_fb_result == false){
			$this->flash->error('エラーがありますから、もう一度やり直してください。');
			return $this->response->redirect('comment/index/'.$page_id);
		}

		$comment_id = $post_fb_result['comment_id'];
		$get_comment_info = $this->_getFbCommentInfo($comment_id);

		$comments = new Comments();
		//DBで情報をセープします
		$comments->user_name 		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminName'] : $this->auth['name'];
		$comments->user_fb_id		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminId'] : $this->auth['id'];
		$comments->user_picture_url	 	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminPicture'] : $this->auth['picture'];
		$comments->page_id		  	 	 = $page_id;
		$comments->comment_id		  	 = $comment_id;
		$comments->url_comment			 = $post_data['url_comment'];

		if ($comments->save() == false) {
			foreach ($comments->getMessages() as $message) {
			 	$this->flash->error($message);
            }

            return $this->response->redirect('comment/index/'.$page_id);
    	}

    	$this->updateTopicCommentCount($page_id);

    	$this->flash->success('投稿しました');
  		$this->response->redirect('topic/index/'.$page_id);

    }

    /**
     * Attempt to determine the real file type of a file.
     *
     * @param  string $extension Extension (eg 'jpg')
     *
     * @return boolean
     */
    private function _imageCheck($extension)
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
     * まとめトッピングテキストを画像する機能
     * @param  string $page_id トッピングID
     */
    public function createPictureAction($page_id){

    	$form = new CommentPictureForm;
    	// POST　parameterを検察します
    	$post_data = $this->request->getPost();
        if (!$form->isValid($post_data)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->response->redirect('comment/picture/'.$page_id);
        }

    	//アップロードファイルを検察します
        if ($this->request->hasFiles(true) == true) {

            foreach ($this->request->getUploadedFiles() as $file){
            	//タイプを検察します
            	if ($this->_imageCheck($file->getType())) {
            		//サイズを検察します
                    if($file->getSize() > 1024000){
                    	$this->flash->error('1024KB未満のファイルだけアップロードできます。');
                    	return $this->response->redirect('comment/picture/'.$page_id);
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
							   return $this->response->redirect('comment/picture/'.$page_id);
							}
						}

						//ファイルをアップロードします
						try{
							$file->moveTo($path. $file->getName());
							//新しい名前を直します
							$new_path = rtrim($this->uploadDir, '/\\') . '/' .date('Y_m_d').'/'.date('His').'_'.$this->auth['id'].'.'.$file->getExtension();
							rename($path. $file->getName() , $new_path);

						}catch(Exception $ex) {
						   //echo "Error: " . $ex->getMessage();
						   $this->flash->error('エラーがありますから、もう一度やり直してください。');
						   return $this->response->redirect('comment/picture/'.$page_id);
						}
                    }
                }else{
                	$this->flash->error('JPG、JPEG、GIF、PNGファイルだけアップロードできます');
                	return $this->response->redirect('comment/picture/'.$page_id);
                }
            }
    	}else{
    		$this->flash->error('JPG、JPEG、GIF、PNGファイルだけアップロードできます');
            return $this->response->redirect('comment/picture/'.$page_id);
    	}

    	$pic_dir = "http://" . $_SERVER['SERVER_NAME'] . $this->url->getStatic().$new_path;

    	if( $post_data['picture_title'] != null || $post_data['picture_description'] != null){
    		$message = $post_data['picture_title'] ." \r\n\r\n ". $post_data['picture_description'];
    	}else{
    		$message = null;
    	}

		$post_info = array(
				'message' => $message,
			);

    	$post_fb_result = $this->_postOnFb($page_id , $post_info , $pic_dir);
 		if($post_fb_result == false){
			$this->flash->error('エラーがありますから、もう一度やり直してください。');
			return $this->response->redirect('comment/index/'.$page_id);
		}

		$comment_id = $post_fb_result['comment_id'];
		$get_comment_info = $this->_getFbCommentInfo($comment_id);

		$comments = new Comments();
		//DBで情報をセープします
		$comments->user_name 		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminName'] : $this->auth['name'];
		$comments->user_fb_id		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminId'] : $this->auth['id'];
		$comments->user_picture_url	 	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminPicture'] : $this->auth['picture'];
		$comments->page_id		  	 	 = $page_id;
		$comments->comment_id		  	 = $comment_id;
		$comments->picture_url			 = $get_comment_info['link'];
		$comments->picture_thumbnail_url = $get_comment_info['attachment_image'];
		$comments->picture_title		 = $post_data['picture_title'];
		$comments->picture_description	 = $post_data['picture_description'];

		if ($comments->save() == false) {
			foreach ($comments->getMessages() as $message) {
			 	$this->flash->error($message);
            }
            return $this->response->redirect('comment/picture/'.$page_id);
    	}

    	$this->updateTopicCommentCount($page_id);

    	$this->flash->success('投稿しました');
  		$this->response->redirect('topic/index/'.$page_id);
    }

    /**
     * まとめトッピング動画を投稿する機能
     * @param  string $page_id トッピングID
     */
    public function createVideoAction($page_id){
 		libxml_use_internal_errors(true);

    	$form = new CommentVideoForm;

    	// POST　parameterを検察します
    	$post_data = $this->request->getPost();
        if (!$form->isValid($post_data)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->response->redirect('comment/video/'.$page_id);
        }

        //動画のURLを取ります
		$videoEmbed = isset($post_data['iframe_url']) ? $post_data['iframe_url'] : null ;
		if( $videoEmbed != null )
		{
			$doc = new DOMDocument();
			@$doc->loadHTML($videoEmbed);
			$iframe_url = 'http:'.$doc->getElementsByTagName('iframe')->item(0)->getAttribute('src');
		}else{
			$iframe_url = null;
		}

		$message = $post_data['video_title'] ." \r\n\r\n ". $post_data['video_description']." \r\n\r\n ". $post_data['video_url'];
		$post_info = array(
				'message' => $message,
			);

		$post_fb_result = $this->_postOnFb($page_id , $post_info , null);

		if($post_fb_result == false){
			$this->flash->error('エラーがありますから、もう一度やり直してください。');
			return $this->response->redirect('comment/index/'.$page_id);
		}

		$comment_id = $post_fb_result['comment_id'];

		$get_comment_info = $this->_getFbCommentInfo($comment_id);

		$comments = new Comments();
		//DBで情報をセープします
		$comments->user_name 		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminName'] : $this->auth['name'];
		$comments->user_fb_id		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminId'] : $this->auth['id'];
		$comments->user_picture_url	 	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminPicture'] : $this->auth['picture'];
		$comments->page_id		  	 	 = $page_id;
		$comments->comment_id		  	 = $comment_id;

		if($iframe_url != null){
			$comments->video_url = $iframe_url;
		}elseif( isset($get_comment_info['link']) && $get_comment_info['link'] != null){
			$comments->video_url = $get_comment_info['link'];
		}else{
			$comments->video_url = $post_data['video_url'];
		}

		$comments->video_title			 = $post_data['video_title'];
		$comments->video_description	 = $post_data['video_description'];
		$comments->video_thumbnail_url	 = isset($get_comment_info['attachment_image']) ? $get_comment_info['attachment_image'] : $post_data['video_url'];

		if( $get_comment_info['attachment_type'] == null && $iframe_url != null ){
			$comments->video_type = 'video';
		}elseif( isset($get_comment_info['attachment_type'])){
			$comments->video_type = $get_comment_info['attachment_type'];
		}else{
			$comments->video_type = 'photo';
		}

		if ($comments->save() == false) {
			foreach ($comments->getMessages() as $message) {
			 	$this->flash->error($message);
            }
            return $this->response->redirect('comment/video/'.$page_id);
    	}

    	$this->updateTopicCommentCount($page_id);

    	$this->flash->success('投稿しました');
  		$this->response->redirect('topic/index/'.$page_id);

    }

	/**
     * まとめトッピングテキストを投稿する機能
     * @param  string $page_id トッピングID
     */
    public function createTextAction($page_id){
    	$form = new CommentTextForm;

    	// POST　parameterを検察します
    	$post_data = $this->request->getPost();
        if (!$form->isValid($post_data)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->response->redirect('comment/text/'.$page_id);
        }

		$post_info = array(
				'message' => $post_data['text_comment'],
			);

		$post_fb_result = $this->_postOnFb($page_id , $post_info , null);

		if($post_fb_result == false){
			$this->flash->error('エラーがありますから、もう一度やり直してください。');
			return $this->response->redirect('comment/index/'.$page_id);
		}

		$comment_id = $post_fb_result['comment_id'];

		$get_comment_info = $this->_getFbCommentInfo($comment_id);

		$comments = new Comments();
		//DBで情報をセープします
		$comments->user_name 		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminName'] : $this->auth['name'];
		$comments->user_fb_id		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminId'] : $this->auth['id'];
		$comments->user_picture_url	 	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminPicture'] : $this->auth['picture'];
		$comments->page_id		  	 	 = $page_id;
		$comments->comment_id		  	 = $comment_id;
		$comments->text_comment			 = $post_data['text_comment'];

		if ($comments->save() == false) {
			foreach ($comments->getMessages() as $message) {
			 	$this->flash->error($message);
            }

            return $this->response->redirect('comment/index/'.$page_id);
    	}

    	$this->updateTopicCommentCount($page_id);

    	$this->flash->success('投稿しました');
  		$this->response->redirect('topic/index/'.$page_id);
    }
}
