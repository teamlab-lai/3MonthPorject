<?php
use Phalcon\Paginator\Adapter\Model as Paginator;
/**
 * TopicController
 *
 *  matome topic
 *  まとめトッピーク
 */
class TopicController extends FbMethodController
{

    public function initialize()
    {
        $this->tag->setTitle('Topic');
        $this->assets->addCss('css/topic.css');
        $this->assets->addCss('css/font-awesome.min.css');
        $this->assets->addJs('js/myFavorite.js');
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
     * 親まとめトピック
     * @param  string $page_id まとめトピックID
     */
    public function oyaMatomeAction($page_id){
    	$this->assets->addCss('css/oyaMatome.css');
        $this->assets->addJs('js/fbMethod.js');
        $this->assets->addJs('js/oyaMatome.js');

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
        $this->view->is_liked = $comments_likes['is_liked'];

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
        $this->assets->addJs('js/fbMethod.js');
        $this->assets->addJs('js/koMatome.js');

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
            /*
            //ページが削除しました
            $this->dispatcher->forward(
                array(
                    "controller" => "comment",
                    "action"     => "doDelete",
                    "params" => array('comment_id'=>$comment_id),
                )
            );

            $this->flash->notice("コメントが削除しました、もう一度お願いします。");
            */
            $this->flash->notice("エラーが有りますから、もう一度お願いします。");
            $this->response->redirect('index/index');
        }

        $this->view->likes = isset($comments_likes['likes']) ? (int)$comments_likes['likes'] : 0;
        $this->view->comments = isset($comments_likes['comments']) ? (int)$comments_likes['comments'] : 0;
        $this->view->is_liked = $comments_likes['is_liked'];

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

        $this->view->FbPageId = $this->FbPageId;
        $this->view->FbAppId = $this->FbAppId;

        //子まとめのコメント資料FB APIでを取ります
        $this->view->comment_box = $this->_getCommentDetail($comment_id);

         //ユーザーの資料を取ります
        if($this->auth['isAdmin'] == true){
            $this->view->user_info = array(
                'user_name' =>$this->auth['adminName'],
                'user_photo' =>$this->auth['adminPicture'],
                );
        }else{
            $this->view->user_info = array(
                'user_name' =>$this->auth['name'],
                'user_photo' =>$this->auth['picture'],
                );
        }
    }

}
