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
    public function oyaTopicAction($page_id){
    	$this->assets->addCss('css/oyaTopic.css');

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
		//FB APIでLIKE数とCOMMENT数を取ります
		if($this->auth['isAdmin'] == true){
			$this->fb->setDefaultAccessToken($this->auth['adminToken']);
		}else{
			$this->fb->setDefaultAccessToken($this->auth['token']);
		}

        try{
    		$response = $this->fb->get('/'.$page_id.'?fields=comments,likes');
    		$response = $response->getDecodedBody();
            $this->view->comments = (isset($response['comments']['data'])) ? number_format(count($response['comments']['data'])) : 0;
    		$this->view->likes = (isset($response['likes']['data'])) ? number_format(count($response['likes']['data'])) : 0;

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

        }catch(Facebook\Exceptions\FacebookResponseException $e) {
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
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
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

    }
}
