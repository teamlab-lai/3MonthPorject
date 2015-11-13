<?php
use Phalcon\Http\Request;
use Phalcon\Http\Response;
/**
 * SearchController
 *
 * Search matome topic
 */
class SearchController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Search');
        $this->view->title ='検索';
        $this->assets->addCss('css/search.css');
        $this->assets->addJs('js/search.js');
        parent::initialize();
    }

    public function indexAction()
    {
    	//COOKIEの中で、検察履歴があったら
    	if($this->cookies->has('matome-search-history')) {

    		$page_ids = unserialize($this->cookies->get('matome-search-history')->getValue());

    		$page_ids = "'".implode("','", $page_ids)."'";

    		$topics = Topics::find(
	            array(
	            	'conditions' => "page_id IN (".$page_ids.")",
	                'order' 	 => "update_time DESC",
	            )
    		);
    		$this->view->topics = $topics;
        }

    	$this->view->form = new SearchForm;
    }

    /**
     * キーワードを検察します
     */
    public function doSearchAction(){
    	$this->view->title ='検索結果';
    	$form = new SearchForm;

    	// POST　parameterを検察します
    	$post_data = $this->request->getPost();
        if (!$form->isValid($post_data)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->forward('search/index');
        }

        $keyword =	$this->request->getPost("keyword", "striptags");
        $topics = Topics::find(
            array(
            	'conditions' => 'title LIKE :keyword: OR description LIKE :keyword:',
    			'bind' => array('keyword' => '%' . $keyword . '%'),
                'order' 	 => "update_time DESC",
            )
        );
        if($topics->count() == 0){
        	$this->flash->notice('まとめが有りません。');
        	return $this->forward('search/index');
        }

        $this->view->topics = $topics;
    }

    /**
     * COOKIEでトピックをセーフします
     * @param  string $page_id FB page id
     */
    public function historyAction($page_id){

        if(!isset($page_id) || $page_id == null){
            $this->response->redirect('errors/show401');
            return;
        }

    	//もしCOOKIEが有りました。
    	if($this->cookies->has('matome-search-history')) {

    		$page_ids = $this->cookies->get('matome-search-history');
    		$page_ids = $page_ids->getValue();
    		$page_ids = unserialize($page_ids);

    		//もしCOOKIEの中ではARRAYです
    		if( is_array($page_ids)){
    			$new_page_ids = $page_ids;
    			if( !in_array($page_id, $new_page_ids)){
    				array_push($new_page_ids, $page_id);
    			}
    		}else{
    			if($page_ids != $page_id){
    				$new_page_ids = array();
    				if( $page_ids != false){
    					array_push($new_page_ids, $page_ids);
    				}

    				array_push($new_page_ids, $page_id);

    			}
    		}
    		$this->cookies->set('matome-search-history' , serialize($new_page_ids) ,time() + 15 * 86400);
    		$this->cookies->send();

        }else{
        	$page_ids = array();
        	array_push($page_ids, $page_id);

        	$this->cookies->set('matome-search-history' , serialize($page_ids) ,time() + 15 * 86400);
        	$this->cookies->send();
        }

        $this->response->redirect('topic/index/'.$page_id);
    	//return $this->forward('topic/index/'.$page_id);
    }

    /**
     * 履歴を削除します
     * @return array REST API スタイル答え
     */
    public function deleteHistoryAction(){
        $this->view->disable();
        $request = new Request();
        $response = new Response();
        if ($request->isPost()) {
            if ($request->isAjax()) {
                $historys = $this->request->getPost("historys");
                if($historys == null || $historys == '' || !is_array($historys)){
                    $response->setJsonContent(
                        array(
                            'status'   => 'ERROR',
                            'messages' => 'データは違いです。'
                        )
                    );
                    return $response;
                }

                //もしCOOKIEが有りました。
                if($this->cookies->has('matome-search-history')) {
                    $page_ids = $this->cookies->get('matome-search-history');
                    $page_ids = $page_ids->getValue();
                    $page_ids = unserialize($page_ids);
                    $deleted_ids = array();

                    //COOKIEのデータを削除します
                    foreach( $historys AS  $history){
                        if(($key = array_search($history, $page_ids)) !== false) {
                            array_push($deleted_ids, $history);
                            unset($page_ids[$key]);
                        }
                    }

                    //新しいCOOKIEデータをセッティングします
                    $this->cookies->set('matome-search-history' , serialize($page_ids) ,time() + 15 * 86400);
                    $this->cookies->send();

                    $response->setJsonContent(
                        array(
                            'status'   => 'OK',
                            'deleted_ids' =>$deleted_ids,
                        )
                    );

                }else{
                    $response->setJsonContent(
                        array(
                            'status'   => 'ERROR',
                            'messages' => 'データは違いです。'
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
