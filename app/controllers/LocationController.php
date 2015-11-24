<?php
class LocationController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Location');
        $this->view->title ='位置情報';
        parent::initialize();
       //$this->view->auth = $this->auth = $this->getAuth();
    }

    public function indexAction($page_id)
    {
        if(!isset($page_id) || $page_id == null){
            $this->response->redirect('errors/show401');
            return;
        }
        $topic = Topics::findFirst(
            array(
            '(page_id = :page_id:)',
            'bind' => array('page_id' => $page_id),
            )
        );

        if($topic == null){
            $this->flash->notice("トピックが有りません。");
            $this->response->redirect('index/index');
            return;
        }

        if($topic->latitude != null && $topic->longitude != null){
            $this->view->center_location = $topic->latitude.','.$topic->longitude;
        }else{
            $this->view->center_location = '35.422,139.4254';
        }

        //このトッピークのコメント情報を取ります
        $comments = Comments::find(
            array(
            '(page_id = :page_id:)',
            'bind' => array('page_id' => $page_id),
            )
        );

        $have_information = false;
        $locations = array();
        foreach($comments AS $index=>$comment){
            if($comment->latitude != null && $comment->longitude != null){
                $have_information = true;
                $comment_location = array(
                    '<div class="col-xs-12">
                        <div class="col-xs-6">'.$comment->user_name.'</div>
                        <div class="col-xs-6">
                            <div href = "#" class = "thumbnail">
                                <img class="center-pic" src = "'.( ($comment->user_picture_url != null ) ? $comment->user_picture_url : '/matome/img/default-page.png' ).'" alt = "">
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <a href="http://'.$_SERVER['SERVER_NAME'].$this->url->getStatic('topic/koMatome/'.$comment->comment_id).'">コメントを読む</a>
                        </div>
                        <div class="col-xs-12">
                            <small>'.( date('Y/m/d' , strtotime($comment->update_time))).'</small>
                        </div>
                    </div>',
                    $comment->latitude,
                    $comment->longitude,
                );
                array_push($locations, $comment_location);
            }
        }

        if($have_information == false){
            $this->flash->notice("位置情報が有りません。");
            $this->response->redirect('back/index/');
            return;
        }
        $this->view->locations = json_encode($locations);
    }
}
