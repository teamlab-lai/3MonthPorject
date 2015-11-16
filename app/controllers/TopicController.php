<?php
use Phalcon\Paginator\Adapter\Model as Paginator;
/**
 * TopicController
 *
 *  matome topic
 *  まとめトッピーク
 */
class TopicController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Topic');
        $this->assets->addCss('css/topic.css');
        $this->assets->addJs('js/topic.js');
        $this->view->auth = $this->auth = $this->getAuth();
        parent::initialize();
    }

    public function indexAction($page_id)
    {
        $numberPage = $this->request->getQuery("page", "int");
        $numberPage = (isset( $numberPage )) ? $numberPage : 1;

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
	    	//クリック数を更新します
	    	$topic->views ++ ;
	    	$topic->save();

    	}

    	$this->view->topic = $topic;

        if($this->auth == null){
            $user_fav = false;
        }else{
        	//お気に入りデータを検察します
         	$user_fav = Favorite::findFirst(
    			array(
    			'(page_id = :page_id: AND user_fb_id = :user_fb_id:)',
    			'bind' => array(
                    'page_id' => $page_id,
                    'user_fb_id' => $this->auth['id'],
                    ),
    			)
    		);
        }

    	if($user_fav == true){
    		$this->view->is_fav = true;
    	}else{
    		$this->view->is_fav = false;
    	}

        //コメントを取る機能
        $comments = Comments::find(
            array(
            '(page_id = :page_id: AND parent_comment_id is null )',
            'bind' => array('page_id' => $page_id),
            'order' => "update_time DESC",
            )
        );

        $paginator = new Paginator(array(
            "data"  => $comments,
            "limit" => 20,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }


    /**
     * FB APIでLIKE数とCOMMENT数を取ります
     * @param  string $fb_id FBのトッピングIDまたは
     * @return  array $result LIKE数とCOMMENT数
     */
    private function _getFBLikesAndComments( $fb_id ){
        $result = array(
            'result' => false,
            'likes' => 0,
            'comments' => 0,
            );

        if($this->auth['isAdmin'] == true){
            $this->fb->setDefaultAccessToken($this->auth['adminToken']);
        }else{
            $this->fb->setDefaultAccessToken($this->auth['token']);
        }

        try{
            $response = $this->fb->get('/'.$fb_id.'?fields=comments,likes');
            $response = $response->getDecodedBody();
            $result['comments'] = (isset($response['comments']['data'])) ? number_format(count($response['comments']['data'])) : 0;
            $result['likes'] = (isset($response['likes']['data'])) ? number_format(count($response['likes']['data'])) : 0;
            $result['result'] = true;

        }catch(Facebook\Exceptions\FacebookResponseException $e) {
            return  $result;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return  $result;
        }

        return  $result;
    }

    /**
     * 親まとめトピック
     * @param  string $page_id まとめトピックID
     */
    public function oyaMatomeAction($page_id){
    	$this->assets->addCss('css/oyaMatome.css');

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

        $comments_likes = $this->_getFBLikesAndComments($page_id);

        if( $comments_likes['result'] == false){
            //ページが削除しました
            $this->dispatcher->forward(
                array(
                    "controller" => "post",
                    "action"     => "doDelete",
                    "params" => array('page_id'=>$page_id),
                )
            );

            $this->flash->notice("ページが削除しました、もう一度お願いします。");
            $this->response->redirect('index/index');
        }

        $this->view->likes = $comments_likes['likes'];
        $this->view->comments = $comments_likes['comments'];

        //お気に入りデータを検察します
        if($this->auth == null){
            $user_fav = false;
        }else{
            //お気に入りデータを検察します
            $user_fav = Favorite::findFirst(
                array(
                '(page_id = :page_id: AND user_fb_id = :user_fb_id:)',
                'bind' => array(
                    'page_id' => $page_id,
                    'user_fb_id' => $this->auth['id'],
                    ),
                )
            );
        }
        if($user_fav == true){
            $this->view->is_fav = true;
        }else{
            $this->view->is_fav = false;
        }
    }

    /**
     * 子まとめトピック
     * @param  string $comment_id まとめトピックID
     */
    public function koMatomeAction($comment_id){
        $this->assets->addCss('css/koMatome.css');

        if(!isset($comment_id) || $comment_id == null){
            $this->response->redirect('errors/show401');
            return;
        }
        //ユーザーに選択されたコメント情報
        $comment = Comments::findFirst(
            array(
            '(comment_id = :comment_id:)',
            'bind' => array('comment_id' => $comment_id),
            )
        );

        if($comment == false){
            $this->flash->notice("コメントが有りません。");
            $this->response->redirect('index/index');
            return;
        }

        $this->view->comment = $comment;

        $comments_likes = $this->_getFBLikesAndComments($comment_id);

        if( $comments_likes['result'] == false){
            //ページが削除しました
            $this->dispatcher->forward(
                array(
                    "controller" => "comment",
                    "action"     => "doDelete",
                    "params" => array('comment_id'=>$comment_id),
                )
            );

            $this->flash->notice("コメントが削除しました、もう一度お願いします。");
            $this->response->redirect('index/index');
        }

        $this->view->likes = $comments_likes['likes'];
        $this->view->comments = $comments_likes['comments'];


        //お気に入りデータを検察します
        if($this->auth == null){
            $user_fav = false;
        }else{
            //お気に入りデータを検察します
            $user_fav = Favorite::findFirst(
                array(
                '(page_id = :page_id: AND user_fb_id = :user_fb_id:)',
                'bind' => array(
                    'page_id' => $comment->page_id,
                    'user_fb_id' => $this->auth['id'],
                    ),
                )
            );
        }
        if($user_fav == true){
            $this->view->is_fav = true;
        }else{
            $this->view->is_fav = false;
        }
    }

}
