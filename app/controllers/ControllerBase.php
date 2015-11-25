<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

    protected function initialize()
    {

        date_default_timezone_set('Asia/tokyo');
        $this->tag->prependTitle('TeamLab-');
        $this->view->setTemplateAfter('main');

        $this->saveBreadcrumb($this->router->getControllerName(), $this->router->getActionName() );

        //$this->session->remove('breadcrumb');
        //var_dump($this->session->get('breadcrumb'));
        //var_dump($this->router->getRewriteUri());
    }

    protected function forward($uri)
    {
        $uriParts = explode('/', $uri);
        $params = array_slice($uriParts, 2);
    	return $this->dispatcher->forward(
    		array(
    			'controller' => $uriParts[0],
    			'action' => $uriParts[1],
                'params' => $params
    		)
    	);
    }

    protected function getAuth()
    {
        return $this->session->get('matome_auth');
    }

    /**
     * アプロードのファイルのタイプを確認します
     * @param  string $extension Extension (eg 'jpg')
     *
     * @return boolean
     */
    protected function _imageCheck($extension)
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
     * ユーザーのIP情報
     * @return string $ipaddress
     */
    protected function _get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')){
                    var_dump(1);
                    $ipaddress = getenv('HTTP_CLIENT_IP');}
        else if(getenv('HTTP_X_FORWARDED_FOR')){
                    var_dump(2);
                    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');}
        else if(getenv('HTTP_X_FORWARDED')){
                    var_dump(3);
                    $ipaddress = getenv('HTTP_X_FORWARDED');}
        else if(getenv('HTTP_FORWARDED_FOR')){
                    var_dump(4);
                    $ipaddress = getenv('HTTP_FORWARDED_FOR');}
        else if(getenv('HTTP_FORWARDED')){
                    var_dump(5);
                    $ipaddress = getenv('HTTP_FORWARDED');}
        else if(getenv('REMOTE_ADDR')){
                    var_dump(6);
                    $ipaddress = getenv('REMOTE_ADDR');}
        else
            $ipaddress = 'UNKNOWN';

        if( $ipaddress == '::1'){
            $ipaddress = '127.0.0.1';
        }
        return $ipaddress;
    }

    /**
     * IPで緯度と経度の情報を取ります
     * @param  string $ip ユーザーのＩＰ
     * @return array  $details 緯度と経度の情報
     */
    protected function _get_loc($ip){
        $info = array(
            'latitude' => null,
            'longitude' => null,
        );
        $json = file_get_contents("http://ipinfo.io/".$ip);
        $details = json_decode($json);
        if(isset($details->loc)){
            $loc = explode(',', $details->loc);
            $info = array(
                'latitude' => $loc[0],
                'longitude' => $loc[1],
            );
        }
        return $info;
    }

    /**
     * Vimeo APIでembedのURLを取ります
     * @param  string $url URL
     * @return  false-urlの形式は間違います
     * @return  string embedのurlの形式
     */
    protected function _getVimeoEmbedUrl($url){
        $xml_info = @file_get_contents("https://vimeo.com/api/oembed.xml?url=".$url);
        if($xml_info === false){
            return false;
        }
        $decode_xml = simplexml_load_string($xml_info);
        if($decode_xml === false){
            return false;
        }
        $doc = new DOMDocument();
        @$doc->loadHTML($decode_xml->html);
        $video_embed_url = $doc->getElementsByTagName('iframe')->item(0)->getAttribute('src');
        return $video_embed_url;
    }

    /**
     * youtubeのembedのURLを取ります
     * @param  string $url URL
     * @return string embedのurlの形式
     */
    protected function _getYoutubeEmbedUrl($url){
        $ytarray = explode("/", $url);
        $ytendstring = end($ytarray);
        $ytendarray = explode("?v=", $ytendstring);
        $ytendstring = end($ytendarray);
        $ytendarray = explode("&", $ytendstring);
        $ytcode = $ytendarray[0];

        return 'https://www.youtube.com/embed/'.$ytcode;
    }

    /**
     * トッピークのコメント数を伸びます
     * @param  string $page_id トッピークID
     */
    protected function updateTopicCommentCount($page_id){

        //ユーザーに選択されたタイトル情報
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
     * ユーザーの行動をレコードします
     * @param  string $controller Controller名前
     * @param  string $action Action名前
     */
    protected function saveBreadcrumb($controller, $action){

        //このアレイのController名前またはactionをレコードしません
        //array[Controller名前]=>[ 'Action名前','Action名前',...]
        $skip_controller = array(
            'js',
            'css',
            'search'=>array(
                    'doSearch',
                    'history',
                    'deleteHistory'
                ),
            'reload',
            'post',
            'session',
            'back',
            'favorite'=>array(
                    'create',
                    'delete',
                ),
            'errors',
            'comment',
            'FbMethod',
        );

        $breadcrumb = $this->session->get('breadcrumb');
        $temp_uri = null;

        //アレイのController名前またはactionをレコードしません
        if($controller != null){
            if( isset($skip_controller[$controller]) && in_array($action, $skip_controller[$controller])){
                return;
            }elseif( in_array($controller, $skip_controller) ){
                return;
            }
        }

        $current_uri = $this->router->getRewriteUri();
        $current_uri = ltrim($current_uri, '/');

        //始める
        if(!$breadcrumb){
            $this->session->set('breadcrumb',array($current_uri) );
        }else{
            //ゆーざーはIndexのページがいったら
            if($current_uri == 'index/index' || $current_uri == '' || $current_uri == '/'){
                //始める
                $this->session->set('breadcrumb',array($current_uri) );
            }else{
                //レコード
                $last_uri = end($breadcrumb);
                if($last_uri != $current_uri && $current_uri != '' && $current_uri != null){
                    if($temp_uri != null && $temp_uri != $last_uri){
                        array_push($breadcrumb , $temp_uri);
                    }else{
                        array_push($breadcrumb , $current_uri);
                    }
                    $this->session->set('breadcrumb',$breadcrumb);
                }
            }

        }

    }

    /**
     * 同じのURLを削除します
     * @param  string $url
     */
    protected function _delbreadcrumb($url){
        $breadcrumb = $this->session->get('breadcrumb');
        $need_to_delete = array();
        foreach($breadcrumb AS $index => $history_url){
            if($history_url == $url){
                array_push($need_to_delete, $index );
            }
        }

        if($need_to_delete != null){
            foreach($need_to_delete AS $delete_index){
                unset($breadcrumb[$delete_index]);
            }

            $this->session->set('breadcrumb',$breadcrumb);
        }
    }
}
