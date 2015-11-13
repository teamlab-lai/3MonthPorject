<?php
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
class FavoriteController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Favorite');
        $this->view->title ='お気に入り';
        $this->assets->addCss('css/favorite.css');
        $this->assets->addJs('js/favorite.js');
        $this->auth = $this->getAuth();
        parent::initialize();
    }

    public function indexAction()
    {

		$numberPage = $this->request->getQuery("page", "int");
        $numberPage = (isset( $numberPage )) ? $numberPage : 1;

        //ユーザーのお気に入りリスト
		$query = "SELECT favorite.id AS id,favorite.page_id AS page_id, topics.title AS title,
				topics.user_name AS user_name , date_format(favorite.update_time, '%Y-%m-%d') update_time,
				topics.video_url AS video_url
				FROM favorite JOIN topics ON favorite.page_id = topics.page_id
				WHERE favorite.user_fb_id = :user_fb_id:
				Order By favorite.update_time DESC";

		$user_favs = $this->modelsManager->executeQuery($query,
			array(
		    	'user_fb_id' => $this->auth['id']
			)
		);

		$paginator = new Paginator(array(
            "data"  => $user_favs,
            "limit" => 20,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * まとめトピックをお気に入る機能
     * @return array REST API スタイル答え
     */
    public function createAction(){
    	$this->view->disable();
    	$request = new Request();
    	$response = new Response();

		if ($request->isPost()) {
			if ($request->isAjax()) {
				$page_id = $this->request->getPost("page_id");
				if($page_id == null){
					$response->setJsonContent(
			            array(
			                'status'   => 'ERROR',
		                	'messages' => '	エラーが有りました。'
			            )
			        );
			        return $response;
				}

				$user_fb_id = $this->auth['id'];

				//同じデータを検察します
				$user_favs = Favorite::findFirst(
					array(
					'(page_id = :page_id: AND user_fb_id =:user_fb_id:)',
					'bind' => array(
						'page_id' => $page_id,
						'user_fb_id' => $user_fb_id,
						),
					)
				);
				if($user_favs == true){
					$response->setJsonContent(
			            array(
			                'status'   => 'ERROR',
		                	'messages' => '同じデータは有りました。'
			            )
			        );
			        return $response;
				}

				//お気に入りを追加する機能
				try{

					$transactionManager = new TransactionManager();
  					$transaction = $transactionManager->get();

  					//お気に入りを追加します
					$favorite = new Favorite();
					$favorite->setTransaction($transaction);
					$favorite->user_fb_id = $user_fb_id;
					$favorite->page_id = $page_id;
					if ($favorite->save() == false) {
						$transaction->rollback("お気に入りをセーフできません");
		        	}

		        	//お気に入り数を追加します
		        	$topic = Topics::findFirst(
					array(
						'(page_id = :page_id:)',
						'bind' => array('page_id' => $page_id),
						)
					);
		        	$topic->setTransaction($transaction);
		        	$topic->favorite_count ++;
		        	$favorite_count = $topic->favorite_count;
					if ($topic->save() == false) {
						$transaction->rollback("お気に入りをセーフできません");
		        	}

		        	$transaction->commit();

	        		$response->setJsonContent(
			            array(
			                'status' => 'OK',
			                'fav_total' => $favorite_count,
			            )
			        );
			    }catch (Phalcon\Mvc\Model\Transaction\Failed $e) {
		    		 $response->setJsonContent(
			            array(
			                'status'   => 'ERROR',
		                	'messages' => $e->getMessage(),
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
     * まとめトピックをお気に入り削除機能
     * @return array REST API スタイル答え
     */
    public function deleteAction(){
    	$this->view->disable();
    	$request = new Request();
    	$response = new Response();

		if ($request->isPost()) {
			if ($request->isAjax()) {
				$page_id = $this->request->getPost("page_id");
				if($page_id == null){
					$response->setJsonContent(
			            array(
			                'status'   => 'ERROR',
		                	'messages' => '	エラーが有りました。'
			            )
			        );
			        return $response;
				}

				$user_fb_id = $this->auth['id'];

				//お気に入りを削除機能
				try{
					$transactionManager = new TransactionManager();
  					$transaction = $transactionManager->get();

					//データを検察します
					$user_favs = Favorite::findFirst(
						array(
						'(page_id = :page_id: AND user_fb_id = :user_fb_id: )',
						'bind' => array(
							'page_id' => $page_id,
							'user_fb_id' => $user_fb_id,
							),
						)
					);
					$user_favs->setTransaction($transaction);
					if($user_favs == false){
						$transaction->rollback("データは有りません。");
					}

					//お気に入りを削除します
					if ($user_favs->delete() == false) {
						$transaction->rollback("データをセーブできません。");
		        	}


		        	//お気に入り数を削減します
		        	$topic = Topics::findFirst(
					array(
						'(page_id = :page_id:)',
						'bind' => array('page_id' => $page_id),
						)
					);
		        	$topic->setTransaction($transaction);
		        	$topic->favorite_count --;
		        	$favorite_count = $topic->favorite_count ;
					if ($topic->save() == false) {
						$transaction->rollback("お気に入りをセーフできません");
		        	}

		        	$transaction->commit();

	        		$response->setJsonContent(
			            array(
			                'status' => 'OK',
			                'fav_total' => $favorite_count,
			            )
			        );

			    }catch (Phalcon\Mvc\Model\Transaction\Failed $e) {
		    		 $response->setJsonContent(
			            array(
			                'status'   => 'ERROR',
		                	'messages' => $e->getMessage(),
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
