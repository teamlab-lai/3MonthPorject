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
class CommentController extends FbMethodController
{

    public function initialize()
    {
        $this->tag->setTitle('Comment');
        $this->view->title ='コメントを作る';
        $this->auth = $this->getAuth();

        $this->assets->addCss('css/jquery.urlive.css'); //
        $this->assets->addCss('css/font-awesome.min.css');
    	$this->assets->addCss('css/comment.css');

        $this->assets->addJs('js/jquery.urlive.js');
    	$this->assets->addJs('js/comment.js');

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

    	//ユーザーに選択されたタイトル情報
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

    	//ユーザーに選択されたタイトル情報
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

    	//ユーザーに選択されたタイトル情報
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

    	//ユーザーに選択されたタイトル情報
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
        $ipaddress = $this->_get_client_ip();
        $loc = $this->_get_loc($ipaddress);

		$comments = new Comments();
		//DBで情報をセープします
		$comments->user_name 		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminName'] : $this->auth['name'];
		$comments->user_fb_id		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminId'] : $this->auth['id'];
		$comments->user_picture_url	 	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminPicture'] : $this->auth['picture'];
		$comments->page_id		  	 	 = $page_id;
		$comments->comment_id		  	 = $comment_id;
		$comments->url_comment			 = $post_data['url_comment'];
        $comments->ip_location           = $ipaddress;
        $comments->longitude             = ( isset($loc['longitude']) && $loc['longitude'] != null) ? $loc['longitude'] : null;
        $comments->latitude              = ( isset($loc['latitude']) && $loc['latitude'] != null) ? $loc['latitude'] : null;

		if ($comments->save() == false) {
			foreach ($comments->getMessages() as $message) {
			 	$this->flash->error($message);
            }

            return $this->response->redirect('comment/index/'.$page_id);
    	}

    	$this->updateTopicCommentCount($page_id);

    	$this->flash->success('コメントを投稿しました');
  		$this->response->redirect('topic/index/'.$page_id);

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
        $ipaddress = $this->_get_client_ip();
        $loc = $this->_get_loc($ipaddress);

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
        $comments->ip_location           = $ipaddress;
        $comments->longitude             = ( isset($loc['longitude']) && $loc['longitude'] != null) ? $loc['longitude'] : null;
        $comments->latitude              = ( isset($loc['latitude']) && $loc['latitude'] != null) ? $loc['latitude'] : null;

		if ($comments->save() == false) {
			foreach ($comments->getMessages() as $message) {
			 	$this->flash->error($message);
            }
            return $this->response->redirect('comment/picture/'.$page_id);
    	}

    	$this->updateTopicCommentCount($page_id);

    	$this->flash->success('コメントを投稿しました');
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

        //動画のembedのURLのウェブサイトを探します
        if(preg_match("/vimeo/i",$post_data['video_url']) == true){

            //VimeoウェブサイトのURL
            $video_embed_url = $this->_getVimeoEmbedUrl($post_data['video_url']);

        }elseif(preg_match("/youtube/i",$post_data['video_url']) == true){
            //YoutubeウェブサイトのURL
            $video_embed_url = $this->_getYoutubeEmbedUrl($post_data['video_url']);

        }else{
            $video_embed_url = null;
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
        $ipaddress = $this->_get_client_ip();
        $loc = $this->_get_loc($ipaddress);

		$comments = new Comments();
		//DBで情報をセープします
		$comments->user_name 		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminName'] : $this->auth['name'];
		$comments->user_fb_id		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminId'] : $this->auth['id'];
		$comments->user_picture_url	 	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminPicture'] : $this->auth['picture'];
		$comments->page_id		  	 	 = $page_id;
		$comments->comment_id		  	 = $comment_id;

		if($video_embed_url != null){
			$comments->video_url = $video_embed_url;
		}elseif( isset($get_comment_info['link']) && $get_comment_info['link'] != null){
			$comments->video_url = $get_comment_info['link'];
		}else{
			$comments->video_url = $post_data['video_url'];
		}

		$comments->video_title			 = $post_data['video_title'];
		$comments->video_description	 = $post_data['video_description'];
		$comments->video_thumbnail_url	 = isset($get_comment_info['attachment_image']) ? $get_comment_info['attachment_image'] : $post_data['video_url'];

		if( $get_comment_info['attachment_type'] == null && $video_embed_url != null ){
			$comments->video_type = 'video';
		}elseif( isset($get_comment_info['attachment_type'])){
			$comments->video_type = $get_comment_info['attachment_type'];
		}else{
			$comments->video_type = 'photo';
		}

        $comments->ip_location           = $ipaddress;
        $comments->longitude             = ( isset($loc['longitude']) && $loc['longitude'] != null) ? $loc['longitude'] : null;
        $comments->latitude              = ( isset($loc['latitude']) && $loc['latitude'] != null) ? $loc['latitude'] : null;

		if ($comments->save() == false) {
			foreach ($comments->getMessages() as $message) {
			 	$this->flash->error($message);
            }
            return $this->response->redirect('comment/video/'.$page_id);
    	}

    	$this->updateTopicCommentCount($page_id);

    	$this->flash->success('コメントを投稿しました');
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
        $ipaddress = $this->_get_client_ip();
        $loc = $this->_get_loc($ipaddress);

		$comments = new Comments();
		//DBで情報をセープします
		$comments->user_name 		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminName'] : $this->auth['name'];
		$comments->user_fb_id		  	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminId'] : $this->auth['id'];
		$comments->user_picture_url	 	 = ($this->auth['isAdmin'] == ture) ? $this->auth['adminPicture'] : $this->auth['picture'];
		$comments->page_id		  	 	 = $page_id;
		$comments->comment_id		  	 = $comment_id;
		$comments->text_comment			 = $post_data['text_comment'];
        $comments->ip_location           = $ipaddress;
        $comments->longitude             = ( isset($loc['longitude']) && $loc['longitude'] != null) ? $loc['longitude'] : null;
        $comments->latitude              = ( isset($loc['latitude']) && $loc['latitude'] != null) ? $loc['latitude'] : null;

		if ($comments->save() == false) {
			foreach ($comments->getMessages() as $message) {
			 	$this->flash->error($message);
            }

            return $this->response->redirect('comment/index/'.$page_id);
    	}

    	$this->updateTopicCommentCount($page_id);

    	$this->flash->success('コメントを投稿しました');
  		$this->response->redirect('topic/index/'.$page_id);
    }

    /**
     * コメントを削除する機能
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
				$comment_id = $this->request->getPost("comment_id");

				if($comment_id != null){
					$result = $this->_fbPageDelete($comment_id);
					if($result == true){
						$comment = Comments::findFirst(
				            array(
				            '(comment_id = :comment_id:)',
				            'bind' => array('comment_id' => $comment_id),
				            )
				        );
						if($comment != false){
							$page_id = $comment->page_id;
							//DBでコメントを削除します
							$del_process = $this->doDeleteAction($comment_id);
						}
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
	                'redirect_url' => "http://" . $_SERVER['SERVER_NAME'] . $this->url->getStatic('topic/index/'.$page_id),
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
     * DBにコメントを削除する機能
     * @param  String $comment_id コメントID
     * @return boolean
     */
    public function doDeleteAction($comment_id){

    	try{
    		$transactionManager = new TransactionManager();
  			$transaction = $transactionManager->get();

  			$comment = Comments::findFirst(
	            array(
	            '(comment_id = :comment_id:)',
	            'bind' => array('comment_id' => $comment_id),
	            )
	        );

  			if($comment != false){

  				$page_id = $comment->page_id;

	  			$comment->setTransaction($transaction);
	  			//トッピングを削除すます
				if(!$comment->delete()){
					$transaction->rollback("コメントを削除できません。");
				}
			}

			if( isset($page_id) && $page_id != null){
				$topic = Topics::findFirst(
					array(
					'(page_id = :page_id:)',
					'bind' => array('page_id' => $page_id),
					)
				);
				$topic->setTransaction($transaction);
				$topic->comment_count -- ;
				if (!$topic->save()) {
					$transaction->rollback("トッピングのデータを変更できません。");
	        	}
			}


			$transaction->commit();
            $this->_delbreadcrumb('topic/koMatome/'.$comment_id);
			return true;

    	}catch (Phalcon\Mvc\Model\Transaction\Failed $e) {
    		//var_dump($e->getMessage());
    		return false;
		}
    }
}
